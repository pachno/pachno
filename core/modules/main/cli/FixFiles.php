<?php

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
    use pachno\core\entities\File;
    use pachno\core\entities\tables\Files;

    /**
     * CLI command class, main -> fix_files
     *
     * @package pachno
     * @subpackage core
     */
    class FixFiles extends \pachno\core\framework\cli\Command
    {

        protected function _setup()
        {
            $this->_command_name = 'fix_files';
            $this->_description = "Removes any lingering uploaded files (not attached to issues or articles)";
        }

        public function do_execute()
        {
            if (\pachno\core\framework\Context::isInstallmode())
            {
                $this->cliEcho("Pachno is not installed\n", 'red');
            }
            else
            {
                $this->cliEcho("Finding files to remove\n", 'white', 'bold');
                $files = Files::getTable()->getUnattachedFiles();
                $this->cliEcho("Found " . count($files) . " files\n", 'white');
                foreach ($files as $file_id) {
                    $file = Files::getTable()->selectById($file_id);
                    $this->cliEcho('Deleting file ' . $file_id . "\n");
                    $file->delete();
                }
                $this->cliEcho("All " . count($files) . " files removed successfully!\n\n", 'white', 'bold');;
            }
        }

    }
