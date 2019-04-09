<?php

    namespace pachno\core\modules\main\cli;

    /**
     * CLI command class, main -> license
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage core
     */

    /**
     * CLI command class, main -> clear-cache
     *
     * @package pachno
     * @subpackage core
     */
    class ClearCache extends \pachno\core\framework\cli\Command
    {
        public function getCommandAliases()
        {
            return [
                'cc'
            ];
        }

        protected function _setup()
        {
            $this->_command_name = 'clear-cache';
            $this->_description = "Clears the local cache";
        }

        public function do_execute()
        {
            $this->cliEcho('Removing cache files from ' . PACHNO_CACHE_PATH);
            $this->cliEcho("\n");
            foreach (new \DirectoryIterator(PACHNO_CACHE_PATH) as $cacheFile) {
                if (!$cacheFile->isDot()) {
                    $this->cliEcho("Removing {$cacheFile->getFilename()}\n");
                    unlink($cacheFile->getPathname());
                }
            }
            $this->cliEcho("Done!\n");
        }

    }
