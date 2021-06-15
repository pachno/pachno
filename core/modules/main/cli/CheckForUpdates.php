<?php

    namespace pachno\core\modules\main\cli;

    use pachno\core\framework;
    use pachno\core\framework\cli\Command;

    /**
     * CLI command for checking if Pachno is up-to-date.
     *
     * @package pachno
     * @subpackage core
     */
    class CheckForUpdates extends Command
    {
        public const UPTODATE = 0;

        public const OUTDATED = 1;

        public const ERROR = 2;

        public function do_execute()
        {
            $latest_version = framework\Context::getLatestAvailableVersionInformation();

            if ($latest_version === null) {
                $uptodate = null;
                $title = framework\Context::getI18n()->__('Failed to check for updates');
                $message = framework\Context::getI18n()->__('The response from Pachno website was invalid');
                $title_color = "red";
                $exit_code = self::UPTODATE;
            } else {
                $update_available = framework\Context::isUpdateAvailable($latest_version);

                if ($update_available) {
                    $uptodate = false;
                    $title = framework\Context::getI18n()->__('Pachno is out of date');
                    $message = framework\Context::getI18n()->__('The latest version is %ver. Update now from pach.no', ['%ver' => $latest_version->nicever]);
                    $title_color = "yellow";
                    $exit_code = self::OUTDATED;
                } else {
                    $uptodate = true;
                    $title = framework\Context::getI18n()->__('Pachno is up to date');
                    $message = framework\Context::getI18n()->__('The latest version is %ver', ['%ver' => $latest_version->nicever]);
                    $title_color = "green";
                    $exit_code = self::ERROR;
                }
            }

            $this->cliEcho($title, $title_color, "bold");
            $this->cliEcho("\n");
            $this->cliEcho($message);
            $this->cliEcho("\n");

            exit($exit_code);
        }

        protected function _setup()
        {
            $this->_command_name = 'check_for_updates';
            $this->_description = "Checks if newer version is available for upgrade.";
        }
    }