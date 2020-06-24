<?php

    namespace pachno\core\modules\main\cli;

    use GlobIterator;
    use pachno\core\entities\Scope;
    use pachno\core\framework\cli\Command;

    /**
     * CLI command class, main -> list_scopes
     *
     * @package pachno
     * @subpackage core
     */
    class RenameComponents extends Command
    {

        public function do_execute()
        {
            $it = new GlobIterator(PACHNO_CORE_PATH . 'modules/*');
            foreach ($it as $dir) {
                if ($dir->isDir()) {
                    $it2 = new GlobIterator($dir->getPathname() . '/components/*');
                    foreach ($it2 as $file) {
                        if (substr($file->getFilename(), 0, 1) == '_') {
                            $new_path = str_replace(['components/_', '.inc.php'], ['components/', '.php'], $file->getPathname());
                            echo $file->getPathname() . ' -> ' . $new_path . "\n";
                            rename($file->getPathname(), $new_path);
                        }
                    }
                }
//                if (!$file->isFile()) continue; //Only rename files
//                $newName = str_replace('SKU#', '', $file->getPathname());
//                rename($file->getPathname(), $newName);
            }
        }

        protected function _setup()
        {
            $this->_command_name = 'rename_components';
            $this->_description = "Rename components";
            parent::_setup();
        }

    }
