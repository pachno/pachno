<?php

    namespace pachno\core\modules\main\cli;

    use pachno\core\entities\tables\Files;
    use pachno\core\framework\cli\Command;
    use pachno\core\framework\Context;

    /**
     * CLI command class, main -> fix_files
     *
     * @package pachno
     * @subpackage core
     */
    class FixFiles extends Command
    {

        public function do_execute()
        {
            if (Context::isInstallmode()) {
                $this->cliEcho("Pachno is not installed\n", 'red');
            } else {
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

        protected function _setup()
        {
            $this->_command_name = 'fix_files';
            $this->_description = "Removes any lingering uploaded files (not attached to issues or articles)";
        }

    }
