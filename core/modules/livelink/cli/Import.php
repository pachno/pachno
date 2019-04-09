<?php

    namespace pachno\core\modules\livelink\cli;

    use pachno\core\entities\tables\Branches;
    use pachno\core\entities\tables\Commits;
    use pachno\core\entities\tables\LivelinkImports;
    use pachno\core\framework\Context;
    use pachno\core\framework\Settings;
    use pachno\core\modules\livelink\Livelink;
    use pachno\core\modules\mailing\Mailing;

    /**
     * CLI command class, livelink -> import
     *
     * @author Philip Kent <kentphilip@gmail.com>
     * @version 3.2
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage vcs_integration
     */

    /**
     * CLI command class, livelink -> import
     *
     * @package pachno
     * @subpackage vcs_integration
     */
    class Import extends \pachno\core\framework\cli\Command
    {

        protected function _setup()
        {
            $this->_command_name = 'import';
            $this->_description = "Import a project from an external repository";
        }

        public function do_execute()
        {
            /* Prepare variables */            
            try
            {
//                Commits::getTable()->create();
//                Branches::getTable()->create();
                $imports = LivelinkImports::getTable()->getPending();
                Mailing::getModule()->temporarilyDisable();

                $current = 0;

                foreach ($imports as $import) {
                    $current += 1;
                    $this->cliEcho("Running import {$current} of ".count($imports)."\n");
                    $this->cliEcho("---------\n");

                    if ($import->getProject()->isDeleted()) {
                        $this->cliEcho("Project ".$import->getProject()->getName()." is deleted. Skipping.\n\n");
                    } else {
                        $this->cliEcho("Importing project ".$import->getProject()->getName()." in scope " . $import->getScope()->getID() . "\n");
                        Context::setScope($import->getScope());
                        Context::switchUserContext($import->getUser());
                        Livelink::getModule()->performImport($import);

                        $this->cliEcho("Done!\n\n", 'white', 'bold');
                    }

                    $import->setCompletedAt(NOW);
                    $import->save();
                }

                Mailing::getModule()->removeTemporarilyDisable();
            }
            catch (\Exception $e)
            {
                if (isset($import)) {
                    $import->setCompletedAt(NOW);
                    $import->save();
                }

                throw $e;
            }
            
        }
    }
