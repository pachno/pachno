<?php

    namespace pachno\core\modules\main\cli;

    use DirectoryIterator;
    use pachno\core\framework\cli\Command;

    /**
     * CLI command class, main -> clear-cache
     *
     * @package pachno
     * @subpackage core
     */
    class ClearCache extends Command
    {
        public function getCommandAliases()
        {
            return [
                'cc'
            ];
        }

        public function do_execute()
        {
            $this->cliEcho('Removing cache files from ' . PACHNO_CACHE_PATH);
            $this->cliEcho("\n");
            foreach (new DirectoryIterator(PACHNO_CACHE_PATH) as $cacheFile) {
                if (!$cacheFile->isDot()) {
                    $this->cliEcho("Removing {$cacheFile->getFilename()}\n");
                    unlink($cacheFile->getPathname());
                }
            }
            $this->cliEcho("Done!\n");
        }

        protected function _setup()
        {
            $this->_command_name = 'clear-cache';
            $this->_description = "Clears the local cache";
        }

    }
