<?php /** @noinspection DisconnectedForeachInstructionInspection */

    namespace pachno\core\modules\main\cli;

    use pachno\core\entities\Article;
    use pachno\core\entities\ArticleCategoryLink;
    use pachno\core\entities\Datatype;
    use pachno\core\entities\DatatypeBase;
    use pachno\core\entities\LogItem;
    use pachno\core\entities\Project;
    use pachno\core\entities\Scope;
    use pachno\core\entities\tables\ArticleCategoryLinks;
    use pachno\core\entities\tables\Articles;
    use pachno\core\entities\tables\Issues;
    use pachno\core\entities\tables\IssueSpentTimes;
    use pachno\core\entities\tables\ListTypes;
    use pachno\core\entities\tables\LogItems;
    use pachno\core\entities\tables\Modules;
    use pachno\core\entities\tables\Projects;
    use pachno\core\entities\tables\RolePermissions;
    use pachno\core\entities\tables\Scopes;
    use pachno\core\entities\tables\Settings;
    use pachno\core\entities\tables\Users;
    use pachno\core\entities\tables\UserSessions;
    use pachno\core\entities\tables\WorkflowSteps;
    use pachno\core\entities\tables\WorkflowTransitionActions;
    use pachno\core\entities\tables\WorkflowTransitionValidationRules;
    use pachno\core\entities\WorkflowTransitionAction;
    use pachno\core\entities\WorkflowTransitionValidationRule;
    use pachno\core\framework\cli\Command;
    use pachno\core\framework\Context;
    use pachno\core\modules\main\cli\entities\tbg;
    use Ramsey\Uuid\Uuid;

    /**
     * CLI command class, main -> migrate
     *
     * @package pachno
     * @subpackage core
     */
    class Migrate extends Command
    {

        public function do_execute()
        {
            if (Context::isInstallmode()) {
                $this->cliEcho("Pachno is not installed\n", 'red');

                return;
            }

            Settings::getTable()->setMaintenanceMode(true);

            $this->cliEcho("Migrating tables: ", self::COLOR_WHITE, self::STYLE_BOLD);
            $this->cliEcho('Users', self::COLOR_WHITE, self::STYLE_DEFAULT);
            Users::getTable()->upgrade(tbg\tables\Users::getTable());
            UserSessions::getTable()->upgrade(tbg\tables\UserSessions::getTable());

            $this->cliMoveLeft(5);
            $this->cliEcho('Articles', self::COLOR_WHITE, self::STYLE_DEFAULT);
            Articles::getTable()->upgrade(tbg\tables\Articles::getTable());
            $this->cliMoveLeft(8);
            $this->cliEcho('ArticleCategoryLinks', self::COLOR_WHITE, self::STYLE_DEFAULT);
            ArticleCategoryLinks::getTable()->upgrade(tbg\tables\ArticleCategoryLinks::getTable());
            $this->cliMoveLeft(20);
            $this->cliEcho(str_pad('100%', 20)."\n", self::COLOR_GREEN, self::STYLE_BOLD);

            $projects = Projects::getTable()->getAll(true);
            $cc = 1;
            $lines = 1;

            foreach ($projects as $project) {
                if ($cc > 1) {
                    $this->cliLineUp($lines);
                    $this->cliMoveLeft();
                }
                $percentage = round((100 / count($projects)) * $cc);
                $this->cliEcho("Migrating projects: ", self::COLOR_WHITE, self::STYLE_BOLD);
                $this->cliEcho("{$percentage}%\n", self::COLOR_GREEN, self::STYLE_BOLD);
                $this->cliEcho("({$cc}/" . count($projects) . ') ');
                $this->cliEcho('[' . $project->getID() . '] ', Command::COLOR_GREEN, Command::STYLE_DEFAULT);
                $this->cliEcho(str_pad($project->getName(), 60)."\n", Command::COLOR_WHITE, Command::STYLE_DEFAULT);

                $cc++;

                if ($project->isDeleted() || $project->getKey() == '') {
                    $lines = 1;
                    continue;
                }

                $lines = 2;
                $articles = Articles::getTable()->getLegacyArticles($project, null, false);
                $this->migrateArticles($articles, $project);
            }

            $scopes = Scopes::getTable()->selectAll();
            $cc = 1;
            $lines--;

            foreach ($scopes as $scope) {
                $this->cliLineUp($lines);
                $this->cliMoveLeft();

                $percentage = round((100 / count($scopes)) * $cc);
                $this->cliEcho("Migrating scopes: ", self::COLOR_WHITE, self::STYLE_BOLD);
                $this->cliEcho(str_pad("{$percentage}%", 55) ."\n", self::COLOR_GREEN, self::STYLE_BOLD);
                $this->cliEcho("({$cc}/" . count($scopes) . ') ');
                $this->cliEcho('[' . $scope->getID() . '] ', Command::COLOR_MAGENTA, Command::STYLE_BOLD);
                $this->cliEcho(str_pad($scope->getName(), 60)."\n", Command::COLOR_WHITE, Command::STYLE_BOLD);

                $this->migrateDataTypes($scope);

                $articles = Articles::getTable()->getLegacyArticles(null, $scope, false);
                $this->migrateArticles($articles, null, $scope->getID());
                $lines = 2;
                $cc++;
            }

            $this->cliLineUp();
            $this->cliClearLine();
            $this->cliMoveLeft();
            $res = ArticleCategoryLinks::getTable()->getDuplicates();
            if ($res) {
                $cc = 1;
                $count = $res->getCount();
                while ($row = $res->getNextRow()) {
                    $percentage = round((100 / $count) * $cc);
                    $this->cliClearLine();
                    $this->cliMoveLeft();
                    $this->cliEcho("Cleaning up after article migration: ", self::COLOR_WHITE, self::STYLE_BOLD);
                    $this->cliEcho("{$percentage}%", self::COLOR_GREEN);
                    ArticleCategoryLinks::getTable()->removeDuplicate($row['article_id'], $row['category_id'], $row['id']);
                }
            }

            Articles::getTable()->removeEmptyRedirects();
            $this->cliClearLine();
            $this->cliMoveLeft();
            $this->cliEcho("Cleaning up after article migration: ", self::COLOR_WHITE, self::STYLE_BOLD);
            $this->cliEcho("100%\n", self::COLOR_GREEN, self::STYLE_BOLD);

            $this->cliClearLine();
            $this->cliEcho("Migrating settings: ", self::COLOR_WHITE, self::STYLE_BOLD);
            Settings::getTable()->migrateSettings();
            Modules::getTable()->removeModuleByName('vcs_integration', true);
            Settings::getTable()->setMaintenanceMode(false);
            $this->cliEcho("100%\n", self::COLOR_GREEN, self::STYLE_BOLD);
        }

        protected function getRedirectArticle(Article $article)
        {
            $content = explode("\n", $article->getContent());

            preg_match('/(\[\[([^\]]*?)\]\])$/im', mb_substr(array_shift($content), 10), $matches);
            if (count($matches) != 3) {
                return false;
            }

            $redirect_article_name = $matches[2];

            return Articles::getTable()->getArticleByName($redirect_article_name, $article->getProject(), true, null, $article->getScope()->getID());
        }

        protected function isRedirectArticle(Article $article)
        {
            return (mb_strpos($article->getContent(), "#REDIRECT ") === 0);
        }

        protected function verifyArticlePath(Article $article)
        {
            $project_id = ($article->getProject() instanceof Project) ? $article->getProject()->getID() : 0;
            $scope_id = $article->getScope()->getID();
            $set_category = $article->isCategory();
            $manual_name = $article->getManualName();
            $prefixes = [];
            $paths = explode(':', $manual_name);

            if (count($paths) > 1 && mb_strtolower($paths[0]) === "category") {
                $set_category = true;
                $prefixes[] = array_shift($paths);
            }

            if ($project_id) {
                $prefixes[] = array_shift($paths);
            }

            $prefix = implode(':', $prefixes);

            if (count($paths) > 1) {
                $parent_id = 0;
                $concat_path = $prefix;
                $test_path = $paths;
                $article_name = array_pop($test_path);

                $existing_parent_article = Articles::getTable()->getArticleByName(implode(':', $test_path), $project_id, true, null, $scope_id);
                if ($existing_parent_article instanceof Article) {
                    $parent_id = ($existing_parent_article->isRedirect() && $existing_parent_article->getRedirectArticle() instanceof Article) ? $existing_parent_article->getRedirectArticle()->getID() : $existing_parent_article->getID();
                } else {
                    foreach ($paths as $path) {
                        $concat_path = ($concat_path != '') ? $concat_path . ':' . $path : $path;
                        if ($concat_path == $article->getManualName()) continue;
                        // UserGuide:Modules:LDAP:Configuration

                        // UserGuide
                        // Modules
                        // LDAP
                        // Configuration
                        $parent_article = Articles::getTable()->getArticleByName($concat_path, $project_id, true, $parent_id, $scope_id);
                        if (!$parent_article instanceof Article) {
                            $parent_article = new Article();
                            $parent_article->setParentArticle($parent_id);
                            $parent_article->setManualName($concat_path);
                            $parent_article->setProject($project_id);
                            $parent_article->setName($path);
                            $parent_article->setAuthor(0);
                            $parent_article->setIsCategory($set_category);
                            $parent_article->save();
                            $parent_id = $parent_article->getID();
                        } else {
                            $parent_id = ($parent_article->isRedirect() && $parent_article->getRedirectArticle() instanceof Article) ? $parent_article->getRedirectArticle()->getID() : $parent_article->getID();
                        }
                    }
                }
                $article->setParentArticle($parent_id);
                $article->setName($article_name);
                $article->save();
            }
        }

        /**
         * @param Article[] $articles
         * @param Project $project
         * @param integer $scope_id
         * @throws \Exception
         */
        protected function migrateArticles($articles, Project $project = null, $scope_id = 0)
        {
            $project_id = ($project instanceof Project) ? $project->getID() : 0;
            $cc = 1;
            foreach ($articles as $article) {
                $percentage = round((100 / count($articles)) * $cc);
                $this->cliClearLine();
                $this->cliMoveLeft();
                $this->cliEcho("Converting articles: ");
                $this->cliEcho("{$percentage}%", self::COLOR_GREEN);

                $article->setProject($project_id);
                if ($this->isRedirectArticle($article)) {
                    $article->setRedirectArticle($this->getRedirectArticle($article));
                    $article->setRedirectSlug(Uuid::uuid4()->toString());
                }

                $article->setManualName(trim($article->getManualName()));
                $article->setManualName(trim($article->getManualName(), ':'));
                $article->setName(trim($article->getName()));
                $article->setName(trim($article->getName(), ':'));
                if (trim($article->getManualName())) {
                    $article->setName($article->getManualName());
                } else {
                    $article->setManualName($article->getName());
                }
                $article->setIsCategory(0);

                if (strpos($article->getName(), 'Category:') === 0) {
                    $article->setIsCategory(true);
                    $article->setName(substr($article->getName(), 9));
                }
                if ($project_id) {
                    $article->setName(substr($article->getName(), strpos($article->getName(), ':') + 1));
                }
                $article->setArticleType(Article::TYPE_MANUAL);
                $article->save();
                $cc++;
            }

            $cc = 1;
            foreach ($articles as $article) {
                $percentage = round((100 / count($articles)) * $cc);
                $cc++;
                $this->cliMoveLeft();
                $this->cliEcho("Generating article relations: ");
                $this->cliEcho("{$percentage}%", self::COLOR_GREEN);
                $this->verifyArticlePath($article);
            }

//            foreach (ArticleCategoryLinks::getTable()->getLegacyCategories($scope_id, $project) as $articleCategoryLink) {
//                if (stripos($articleCategoryLink->getCategoryName(), 'category:') === false) {
//                    $articleCategoryLink->setCategoryName('Category:' . $articleCategoryLink->getCategoryName());
//                }
//                if ($project->getID() == 4) {
//                    var_dump($articleCategoryLink);
//                    die();
//                }
//                $categoryArticle = Articles::getTable()->getArticleByName($articleCategoryLink->getCategoryName(), $project_id, true, $scope_id);
//                if (!$categoryArticle instanceof Article) {
//                    $categoryArticle = new Article();
//                    $categoryArticle->setManualName($articleCategoryLink->getCategoryName());
//                    $categoryArticle->setName($categoryArticle->getManualName());
//                    $categoryArticle->setProject($project_id);
//                    $categoryArticle->setScope($scope_id);
//                    $categoryArticle->setIsCategory(true);
//                    $categoryArticle->save();
//                }
//                $articleCategoryLink->setCategory($categoryArticle);
//                $articleCategoryLink->save();
//                $this->verifyArticlePath($categoryArticle);
//            }

            $cc = 1;
            if ($project_id) {
                $articles = Articles::getTable()->getByProjectId($project_id);
            } else {
                $articles = Articles::getTable()->getByScopeId($scope_id);
            }
            foreach ($articles as $article) {
                $percentage = round((100 / count($articles)) * $cc);
                $this->cliClearLine();
                $this->cliMoveLeft();
                $this->cliEcho("Converting article categories: ");
                $this->cliEcho("{$percentage}%", self::COLOR_GREEN);

                $article_name = trim($article->getManualName());

                if ($article_name) {
                    $article_id = ($article->isRedirect() && $article->getRedirectArticle() instanceof Article) ? $article->getRedirectArticle()->getID() : $article->getID();
                    ArticleCategoryLinks::getTable()->updateArticleId($article_id, $article_name);
                    ArticleCategoryLinks::getTable()->updateCategoryId($article_id, $article_name);
                }

                if (!$article->isRedirect()) {
                    if ($article->isCategory()) {
                        if (!$article->getParentArticle() instanceof Article) {
                            foreach ($article->getCategories() as $articleCategoryLink) {
                                $article->setParentArticle($articleCategoryLink->getCategory());
                                break;
                            }
                            $article->save();
                        }
                        foreach ($article->getCategories() as $articleCategoryLink) {
                            $articleCategoryLink->delete();
                        }
                    } else {
                        if ($article->getParentArticle() instanceof Article) {
                            if ($article->getParentArticle()->isCategory()) {
                                $articleCategoryLink = new ArticleCategoryLink();
                                $articleCategoryLink->setArticle($article);
                                $articleCategoryLink->setCategory($article->getParentArticle());
                                $articleCategoryLink->save();
                                $article->setParentArticle(0);
                                $article->save();
                            }
                        } else {
                            foreach ($article->getCategories() as $articleCategoryLink) {
                                if (!$articleCategoryLink->getArticle()->isCategory()) {
                                    $article->setParentArticle($articleCategoryLink->getCategory());
                                    $article->save();
                                    $articleCategoryLink->delete();
                                }
                            }
                        }
                    }
                } else {
                    $article->setParentArticle(0);
                    $article->save();
                    foreach ($article->getCategories() as $articleCategoryLink) {
                        $articleCategoryLink->delete();
                    }
                }

                $words = preg_split('~[^A-Z]+\K|(?=[A-Z][^A-Z]+)~', $article->getName(), 0, PREG_SPLIT_NO_EMPTY);
                $article->setName(implode(' ', $words));
                $article->save();

                $cc++;
            }

            $this->cliClearLine();
        }

        protected function migrateDataTypes(Scope $scope)
        {
            $this->cliClearLine();
            $this->cliMoveLeft();

            $statuses = ListTypes::getTable()->getAllByItemType(Datatype::STATUS, $scope->getID());
            $priorities = ListTypes::getTable()->getAllByItemType(Datatype::PRIORITY, $scope->getID());
            $resolutions = ListTypes::getTable()->getAllByItemType(Datatype::RESOLUTION, $scope->getID());
            $reproducabilities = ListTypes::getTable()->getAllByItemType(Datatype::REPRODUCABILITY, $scope->getID());
            $roles = ListTypes::getTable()->getAllByItemType(Datatype::ROLE, $scope->getID());
            $activity_types = ListTypes::getTable()->getAllByItemType(Datatype::ACTIVITYTYPE, $scope->getID());

            $count = count($statuses) + count($priorities) + count($resolutions) + count($reproducabilities) + count($roles) + count($activity_types);
            $cc = 1;

            foreach ($statuses as $status) {
                $percentage = round((100 / $count) * $cc);
                $this->cliClearLine();
                $this->cliMoveLeft();
                $this->cliEcho("Migrating data types (status): {$percentage}%");

                $existing = ListTypes::getTable()->getByKeyAndItemType($status->getKey(), Datatype::STATUS, $status->getID(), true);
                if ($existing instanceof DatatypeBase) {
                    Issues::getTable()->updateIssueField('status', $status->getID(), $existing->getID());
                    LogItems::getTable()->updateLogRelatedItem(LogItem::ACTION_ISSUE_UPDATE_STATUS, $status->getID(), $existing->getID());
                    WorkflowSteps::getTable()->updateStepStatus($status->getID(), $existing->getID());
                    WorkflowTransitionActions::getTable()->updateTransitionAction(WorkflowTransitionAction::ACTION_SET_STATUS, $status->getID(), $existing->getID());
                    WorkflowTransitionValidationRules::getTable()->updateValidationRule(WorkflowTransitionValidationRule::RULE_STATUS_VALID, $status->getID(), $existing->getID());

                    if ($status->getItemdata() && !$existing->getItemdata()) {
                        $existing->setItemdata($status->getItemdata());
                        $existing->save();
                    }
                    $status->delete();
                    ListTypes::getTable()->removeFromItemCache($status);
                }
                $cc++;
            }

            foreach ($priorities as $priority) {
                $percentage = round((100 / $count) * $cc);
                $this->cliClearLine();
                $this->cliMoveLeft();
                $this->cliEcho("Migrating data types (priority): {$percentage}%");

                $existing = ListTypes::getTable()->getByKeyAndItemType($priority->getKey(), Datatype::PRIORITY, $priority->getID());
                if ($existing instanceof DatatypeBase) {
                    Issues::getTable()->updateIssueField('priority', $priority->getID(), $existing->getID());
                    LogItems::getTable()->updateLogRelatedItem(LogItem::ACTION_ISSUE_UPDATE_PRIORITY, $priority->getID(), $existing->getID());
                    WorkflowTransitionActions::getTable()->updateTransitionAction(WorkflowTransitionAction::ACTION_SET_PRIORITY, $priority->getID(), $existing->getID());
                    WorkflowTransitionValidationRules::getTable()->updateValidationRule(WorkflowTransitionValidationRule::RULE_PRIORITY_VALID, $priority->getID(), $existing->getID());

                    $priority->delete();
                    ListTypes::getTable()->removeFromItemCache($priority);
                }
                $cc++;
            }

            foreach ($resolutions as $resolution) {
                $percentage = round((100 / $count) * $cc);
                $this->cliClearLine();
                $this->cliMoveLeft();
                $this->cliEcho("Migrating data types (resolution): {$percentage}%");

                $existing = ListTypes::getTable()->getByKeyAndItemType($resolution->getKey(), Datatype::RESOLUTION, $resolution->getID());
                if ($existing instanceof DatatypeBase) {
                    Issues::getTable()->updateIssueField('resolution', $resolution->getID(), $existing->getID());
                    LogItems::getTable()->updateLogRelatedItem(LogItem::ACTION_ISSUE_UPDATE_RESOLUTION, $resolution->getID(), $existing->getID());
                    WorkflowTransitionActions::getTable()->updateTransitionAction(WorkflowTransitionAction::ACTION_SET_RESOLUTION, $resolution->getID(), $existing->getID());
                    WorkflowTransitionValidationRules::getTable()->updateValidationRule(WorkflowTransitionValidationRule::RULE_RESOLUTION_VALID, $resolution->getID(), $existing->getID());

                    $resolution->delete();
                    ListTypes::getTable()->removeFromItemCache($resolution);
                }
                $cc++;
            }

            foreach ($reproducabilities as $reproducability) {
                $percentage = round((100 / $count) * $cc);
                $this->cliClearLine();
                $this->cliMoveLeft();
                $this->cliEcho("Migrating data types (reproducability): {$percentage}%");

                $existing = ListTypes::getTable()->getByKeyAndItemType($reproducability->getKey(), Datatype::REPRODUCABILITY, $reproducability->getID());
                if ($existing instanceof DatatypeBase) {
                    Issues::getTable()->updateIssueField('reproducability', $reproducability->getID(), $existing->getID());
                    LogItems::getTable()->updateLogRelatedItem(LogItem::ACTION_ISSUE_UPDATE_REPRODUCABILITY, $reproducability->getID(), $existing->getID());
                    WorkflowTransitionActions::getTable()->updateTransitionAction(WorkflowTransitionAction::ACTION_SET_REPRODUCABILITY, $reproducability->getID(), $existing->getID());
                    WorkflowTransitionValidationRules::getTable()->updateValidationRule(WorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID, $reproducability->getID(), $existing->getID());

                    $reproducability->delete();
                    ListTypes::getTable()->removeFromItemCache($reproducability);
                }
                $cc++;
            }

            foreach ($roles as $role) {
                $percentage = round((100 / $count) * $cc);
                $this->cliClearLine();
                $this->cliMoveLeft();
                $this->cliEcho("Migrating data types (roles): {$percentage}%");

                $existing = ListTypes::getTable()->getByKeyAndItemType($role->getKey(), Datatype::ROLE, $role->getID());
                if ($existing instanceof DatatypeBase) {
                    RolePermissions::getTable()->updateRole($role->getID(), $existing->getID());

                    $role->delete();
                    ListTypes::getTable()->removeFromItemCache($role);
                }
                $cc++;
            }

            foreach ($activity_types as $activity_type) {
                $percentage = round((100 / $count) * $cc);
                $this->cliClearLine();
                $this->cliMoveLeft();
                $this->cliEcho("Migrating data types (activity types): {$percentage}%");

                $existing = ListTypes::getTable()->getByKeyAndItemType($activity_type->getKey(), Datatype::ACTIVITYTYPE, $activity_type->getID());
                if ($existing instanceof DatatypeBase) {
                    IssueSpentTimes::getTable()->updateActivityType($activity_type->getID(), $existing->getID());

                    $activity_type->delete();
                    ListTypes::getTable()->removeFromItemCache($activity_type);
                }
                $cc++;
            }
            $this->cliClearLine();
            $this->cliMoveLeft();
            $this->cliEcho("Migrating data types: 100%");
        }

        protected function _setup()
        {
            $this->_command_name = 'migrate';
            $this->_description = "Migrate data from TBG -> Pachno";
        }

    }
