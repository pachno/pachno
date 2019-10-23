<?php

    namespace pachno\core\modules\main\cli;

    use pachno\core\framework\cli\Command;

    /**
     * CLI command class, main -> license
     *
     * @package pachno
     * @subpackage core
     */
    class License extends Command
    {

        public function do_execute()
        {
            if ($this->getProvidedArgument(2) == 'print' || $this->getProvidedArgument('print') == 'yes') {
                $thelicense = file_get_contents('LICENSE.TXT');
                $this->cliEcho("{$thelicense}\n");
            } else {
                $this->cliEcho("Pachno is released under the MPL 2.0.\n", 'white', 'bold');
                $this->cliEcho("Read the full license at:\n");
                $this->cliEcho("http://opensource.org/licenses/MPL-2.0\n\n", 'blue', 'underline');
                $this->cliEcho('or type: ');
                $this->cliEcho($this->getCommandLineName(), 'white', 'bold') . $this->cliEcho(' license', 'green', 'bold') . $this->cliEcho(' print', 'magenta');
            }
            $this->cliEcho("\n");
        }

        protected function _setup()
        {
            $this->_command_name = 'license';
            $this->_description = "Show license information";
            $this->addOptionalArgument('print', 'Print the license in full');
        }

    }
