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
    use pachno\core\entities\tables\Projects;
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

        public function do_execute()
        {
            if (\pachno\core\framework\Context::isInstallmode()) {
                $this->cliEcho("Pachno is not installed\n", 'red');
                return;
            }

            Articles::getTable()->upgrade(tbg\tables\Articles::getTable());
            $projects = Projects::getTable()->getAll(true);
            $cc = 1;
            foreach ($projects as $project) {
                $this->cliEcho('Migrating ');
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
                $articles = Articles::getTable()->getLegacyArticles($project, false);
                $this->cliEcho("Found " . count($articles) . " article(s)\n");
                foreach ($articles as $article) {
                    $this->cliEcho('.');
                    $article->setProject($project);
                    if (trim($article->getManualName())) {
                        $article->setName($article->getManualName());
                    }
                    $article->setIsCategory(0);

                    if (strpos($article->getName(), 'Category:') === 0) {
                        $article->setIsCategory(true);
                        $article->setName(substr($article->getName(), 9));
                    }
                    $article->setName(substr($article->getName(), strlen($project->getKey()) + 1));
                    $article->setArticleType(Article::TYPE_MANUAL);

                    $article->save();
                }
                $this->cliEcho("\n");
            }
        }

    }
