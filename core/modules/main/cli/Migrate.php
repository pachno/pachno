<?php /** @noinspection DisconnectedForeachInstructionInspection */

    namespace pachno\core\modules\main\cli;

    use pachno\core\entities\Article;
    use pachno\core\entities\Datatype;
    use pachno\core\entities\DatatypeBase;
    use pachno\core\entities\LogItem;
    use pachno\core\entities\Scope;
    use pachno\core\entities\Status;
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
    use pachno\core\modules\project\Project;

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
                $this->migrateArticles($articles, $project->getID());
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
                $this->migrateArticles($articles, 0, $scope->getID());
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
                    $this->cliEcho("Cleaning up article categories: ");
                    $this->cliEcho("{$percentage}%", self::COLOR_GREEN);
                    ArticleCategoryLinks::getTable()->removeDuplicate($row['article_id'], $row['category_id'], $row['id']);
                }
            }

            $this->cliClearLine();
            $this->cliMoveLeft();
            $this->cliEcho("Cleaning up article categories: ");
            $this->cliEcho("100%\n", self::COLOR_GREEN, self::STYLE_BOLD);

            $this->cliEcho("Migrating settings: ", self::COLOR_WHITE, self::STYLE_BOLD);
            Settings::getTable()->migrateSettings();
            Modules::getTable()->removeModuleByName('vcs_integration', true);
            Settings::getTable()->setMaintenanceMode(false);

            $this->cliEcho(str_pad("100%", 55) ."\n", self::COLOR_GREEN, self::STYLE_BOLD);
        }

        protected function verifyArticlePath(Article $article)
        {
            $project_id = ($article->getProject() instanceof Project) ? $article->getProject()->getID() : 0;
            $scope_id = $article->getScope()->getID();
            $set_category = $article->isCategory();
            $manual_name = $article->getManualName();
            if (strpos($manual_name, 'Category:') === 0) {
                $manual_name = substr($manual_name, 9);
            }
            if ($project_id) {
                $manual_name = substr($manual_name, strpos($manual_name, ':') + 1);
            }

            $paths = explode(':', $manual_name);
            $this->cliEcho("Checking '{$manual_name}'");
            if (count($paths) > 1) {
                $parent_id = 0;
                $concat_path = '';
                $test_path = $paths;
                $article_name = array_pop($test_path);

                $existing_parent_article = Articles::getTable()->getArticleByName(implode(':', $test_path), $project_id, true, null, $scope_id);
                if ($existing_parent_article instanceof Article) {
                    $parent_id = ($existing_parent_article->isRedirect() && $existing_parent_article->getRedirectArticle() instanceof Article) ? $existing_parent_article->getRedirectArticle()->getID() : $existing_parent_article->getID();
                } else {
                    foreach ($paths as $path) {
                        $concat_path = ($concat_path != '') ? $concat_path . ':' . $path : $path;
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
                if ($article->getName() == 'MainPage') {
                    $article->setName('Main Page');
                }
                $article->save();
            }
        }

        /**
         * @param Article[] $articles
         * @param integer $project_id
         * @param integer $scope_id
         */
        protected function migrateArticles($articles, $project_id = 0, $scope_id = 0)
        {
            $cc = 1;
            foreach ($articles as $article) {
                $percentage = round((100 / count($articles)) * $cc);
                $this->cliClearLine();
                $this->cliMoveLeft();
                $this->cliEcho("Converting articles: ");
                $this->cliEcho("{$percentage}%", self::COLOR_GREEN);

                $article->setProject($project_id);
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
                if ($article->getName() == 'MainPage') {
                    $article->setName('Main Page');
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

                if ($article->isCategory()) {
                    if (count($article->getCategories()) > 1) {
                        $article->setParentArticle(0);
                        $article->save();
                    }
                }

                if (!$article->isRedirect()) {
                    if (count($article->getCategories()) == 1 && !$article->getParentArticle() instanceof Article) {
                        foreach ($article->getCategories() as $articleCategoryLink) {
                            $article->setParentArticle($articleCategoryLink->getCategory());
                            $articleCategoryLink->delete();
                            $article->save();
                        }
                    }
                }

                $cc++;
            }

            $this->cliClearLine();
        }

        protected function migrateDataTypes(Scope $scope)
        {
            $this->cliMoveLeft();
            $this->cliEcho(str_pad("Migrating data types: statuses ...", 50));
            foreach (ListTypes::getTable()->getAllByItemType(Datatype::STATUS, $scope->getID()) as $status) {
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
            }

            $this->cliMoveLeft();
            $this->cliEcho(str_pad("Migrating data types: priorities ...", 50));
            foreach (ListTypes::getTable()->getAllByItemType(Datatype::PRIORITY, $scope->getID()) as $priority) {
                $existing = ListTypes::getTable()->getByKeyAndItemType($priority->getKey(), Datatype::PRIORITY, $priority->getID());
                if ($existing instanceof DatatypeBase) {
                    Issues::getTable()->updateIssueField('priority', $priority->getID(), $existing->getID());
                    LogItems::getTable()->updateLogRelatedItem(LogItem::ACTION_ISSUE_UPDATE_PRIORITY, $priority->getID(), $existing->getID());
                    WorkflowTransitionActions::getTable()->updateTransitionAction(WorkflowTransitionAction::ACTION_SET_PRIORITY, $priority->getID(), $existing->getID());
                    WorkflowTransitionValidationRules::getTable()->updateValidationRule(WorkflowTransitionValidationRule::RULE_PRIORITY_VALID, $priority->getID(), $existing->getID());

                    $priority->delete();
                    ListTypes::getTable()->removeFromItemCache($priority);
                }
            }

            $this->cliMoveLeft();
            $this->cliEcho(str_pad("Migrating data types: resolutions ...", 50));
            foreach (ListTypes::getTable()->getAllByItemType(Datatype::RESOLUTION, $scope->getID()) as $resolution) {
                $existing = ListTypes::getTable()->getByKeyAndItemType($resolution->getKey(), Datatype::RESOLUTION, $resolution->getID());
                if ($existing instanceof DatatypeBase) {
                    Issues::getTable()->updateIssueField('resolution', $resolution->getID(), $existing->getID());
                    LogItems::getTable()->updateLogRelatedItem(LogItem::ACTION_ISSUE_UPDATE_RESOLUTION, $resolution->getID(), $existing->getID());
                    WorkflowTransitionActions::getTable()->updateTransitionAction(WorkflowTransitionAction::ACTION_SET_RESOLUTION, $resolution->getID(), $existing->getID());
                    WorkflowTransitionValidationRules::getTable()->updateValidationRule(WorkflowTransitionValidationRule::RULE_RESOLUTION_VALID, $resolution->getID(), $existing->getID());

                    $resolution->delete();
                    ListTypes::getTable()->removeFromItemCache($resolution);
                }
            }

            $this->cliMoveLeft();
            $this->cliEcho(str_pad("Migrating data types: reproducabilities ...", 50));
            foreach (ListTypes::getTable()->getAllByItemType(Datatype::REPRODUCABILITY, $scope->getID()) as $reproducability) {
                $existing = ListTypes::getTable()->getByKeyAndItemType($reproducability->getKey(), Datatype::REPRODUCABILITY, $reproducability->getID());
                if ($existing instanceof DatatypeBase) {
                    Issues::getTable()->updateIssueField('reproducability', $reproducability->getID(), $existing->getID());
                    LogItems::getTable()->updateLogRelatedItem(LogItem::ACTION_ISSUE_UPDATE_REPRODUCABILITY, $reproducability->getID(), $existing->getID());
                    WorkflowTransitionActions::getTable()->updateTransitionAction(WorkflowTransitionAction::ACTION_SET_REPRODUCABILITY, $reproducability->getID(), $existing->getID());
                    WorkflowTransitionValidationRules::getTable()->updateValidationRule(WorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID, $reproducability->getID(), $existing->getID());

                    $reproducability->delete();
                    ListTypes::getTable()->removeFromItemCache($reproducability);
                }
            }

            $this->cliMoveLeft();
            $this->cliEcho(str_pad("Migrating data types: roles ...", 50));
            foreach (ListTypes::getTable()->getAllByItemType(Datatype::ROLE, $scope->getID()) as $role) {
                $existing = ListTypes::getTable()->getByKeyAndItemType($role->getKey(), Datatype::ROLE, $role->getID());
                if ($existing instanceof DatatypeBase) {
                    RolePermissions::getTable()->updateRole($role->getID(), $existing->getID());

                    $role->delete();
                    ListTypes::getTable()->removeFromItemCache($role);
                }
            }

            $this->cliMoveLeft();
            $this->cliEcho(str_pad("Migrating data types: activity types ...", 50));
            foreach (ListTypes::getTable()->getAllByItemType(Datatype::ACTIVITYTYPE, $scope->getID()) as $activity_type) {
                $existing = ListTypes::getTable()->getByKeyAndItemType($activity_type->getKey(), Datatype::ACTIVITYTYPE, $activity_type->getID());
                if ($existing instanceof DatatypeBase) {
                    IssueSpentTimes::getTable()->updateActivityType($activity_type->getID(), $existing->getID());

                    $activity_type->delete();
                    ListTypes::getTable()->removeFromItemCache($activity_type);
                }
            }
            $this->cliMoveLeft();
            $this->cliEcho(str_pad("", 50));
        }

        protected function _setup()
        {
            $this->_command_name = 'migrate';
            $this->_description = "Migrate data from TBG -> Pachno";
        }

    }
