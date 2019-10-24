<?php

    namespace pachno\core\modules\publish\cli;

    use pachno\core\framework\cli\Command;
    use pachno\core\framework\Context;
    use pachno\core\framework\Event;

    /**
     * CLI command class, publish -> import
     *
     * @package pachno
     * @subpackage publish
     */
    class Import extends Command
    {

        public function do_execute()
        {
            $this->cliEcho("Importing articles ... \n", 'white', 'bold');
            Event::listen('publish', 'fixture_article_loaded', [$this, 'listenPublishFixtureArticleCreated']);
            $overwrite = (bool)($this->getProvidedArgument('overwrite', 'no') == 'yes');

            Context::getModule('publish')->loadFixturesArticles(Context::getScope()->getID(), $overwrite);
        }

        public function listenPublishFixtureArticleCreated(Event $event)
        {
            $this->cliEcho(($event->getParameter('imported')) ? "Importing " : "Skipping ");
            $this->cliEcho($event->getSubject() . "\n", 'white', 'bold');
        }

        protected function _setup()
        {
            $this->_command_name = 'import_articles';
            $this->_description = "Imports all articles from the fixtures folder";
            $this->addOptionalArgument('overwrite', "Set to 'yes' to overwrite existing articles");
        }

    }
