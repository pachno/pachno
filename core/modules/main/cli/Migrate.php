<?php /** @noinspection DisconnectedForeachInstructionInspection */

namespace pachno\core\modules\main\cli;

    /**
     * CLI command class, main -> fix_files
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage core
     */

    use pachno\core\entities\Article;
    use pachno\core\entities\File;
    use pachno\core\entities\tables\Articles;
    use pachno\core\entities\tables\Files;
    use pachno\core\entities\tables\Modules;
    use pachno\core\entities\tables\Projects;
    use pachno\core\entities\tables\Scopes;
    use pachno\core\entities\tables\Settings;
    use pachno\core\entities\tables\Users;
    use pachno\core\entities\tables\UserSessions;
    use pachno\core\framework\cli\Command;
    use pachno\core\modules\main\cli\entities\tbg;

    /**
     * CLI command class, main -> migrate
     *
     * @package pachno
     * @subpackage core
     */
    class Migrate extends \pachno\core\framework\cli\Command
    {

        protected function _setup()
        {
            $this->_command_name = 'migrate';
            $this->_description = "Migrate data from TBG -> Pachno";
        }

        /**
         * @param Article[] $articles
         * @param integer $project_id
         * @param string $project_key
         */
        protected function migrateArticles($articles, $project_id = 0, $project_key = '')
        {
            $this->cliEcho("Found " . count($articles) . " article(s)\n");
            $this->cliEcho("Converting: ");
            foreach ($articles as $article) {
                $this->cliEcho('.');
                $article->setProject($project_id);
                if (trim($article->getManualName())) {
                    $article->setName($article->getManualName());
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
            }

            $this->cliEcho("\n");
            $this->cliEcho("Generating article relations: ");

            foreach ($articles as $article) {
                $this->cliEcho('.');
                $paths = explode(':', $article->getName());
                if (count($paths) > 1) {
                    $parent_id = 0;
                    $concat_path = '';
                    foreach ($paths as $path) {
                        if (!$path) {
                            continue;
                        }

                        $concat_path = ($concat_path != '') ? $concat_path . ':' . $path : $path;
                        if ($concat_path != $article->getName()) {
                            $parent_article = Articles::getTable()->getArticleByName($concat_path, $project_id);
                            if (!$parent_article instanceof Article) {
                                $parent_article = new Article();
                                $parent_article->setParentArticle($parent_id);
                                $parent_article->setProject($project_id);
                                $parent_article->setName($concat_path);
                                $parent_article->setAuthor(0);
                                $parent_article->save();
                            }
                        } else {
                            $parent_article = $article;
                        }

                        $parent_article->setParentArticle($parent_id);
                        $parent_article->save();
                        $parent_id = $parent_article->getID();
                    }
                }
            }
            $this->cliEcho("\n");
        }

        public function do_execute()
        {
            if (\pachno\core\framework\Context::isInstallmode()) {
                $this->cliEcho("Pachno is not installed\n", 'red');
                return;
            }

            $this->cliEcho("Migrating users\n");
            Users::getTable()->upgrade(tbg\tables\Users::getTable());
            UserSessions::getTable()->upgrade(tbg\tables\UserSessions::getTable());
            $this->cliEcho("\n");

            Articles::getTable()->upgrade(tbg\tables\Articles::getTable());
            $projects = Projects::getTable()->getAll(true);
            $cc = 1;
            foreach ($projects as $project) {
                $this->cliEcho('Migrating project ');
                $this->cliEcho('[' . $project->getID() . '] ', Command::COLOR_GREEN, Command::STYLE_BOLD);
                $this->cliEcho($project->getName(), Command::COLOR_WHITE, Command::STYLE_BOLD);
                $this->cliEcho(" ({$cc}/" . count($projects) . ")\n");
                $cc++;

                if ($project->isDeleted()) {
                    $this->cliEcho("Skipping project (deleted)\n");
                    continue;
                }

                if ($project->getKey() == '') {
                    $this->cliEcho("Skipping project (missing key)\n");
                    continue;
                }
                $articles = Articles::getTable()->getLegacyArticles($project, null, false);
                $this->migrateArticles($articles, $project->getID(), $project->getKey());
            }

            $scopes = Scopes::getTable()->selectAll();
            $cc = 1;
            foreach ($scopes as $scope) {
                $this->cliEcho('Migrating scope ');
                $this->cliEcho('[' . $scope->getID() . '] ', Command::COLOR_MAGENTA, Command::STYLE_BOLD);
                $this->cliEcho($scope->getName(), Command::COLOR_WHITE, Command::STYLE_BOLD);
                $this->cliEcho(" ({$cc}/" . count($scopes) . ")\n");

                $articles = Articles::getTable()->getLegacyArticles(null, $scope, false);
                $this->migrateArticles($articles);
                $cc += 1;
            }

            $this->cliEcho("Migrating settings\n");
            Settings::getTable()->migrateSettings();

            Modules::getTable()->removeModuleByName('vcs_integration', true);
        }

    }
