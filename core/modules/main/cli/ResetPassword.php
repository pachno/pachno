<?php

    namespace pachno\core\modules\main\cli;

    use Exception;
    use pachno\core\entities\User;
    use pachno\core\framework;

    /**
     * CLI command class, main -> reset_password
     *
     * @package pachno
     * @subpackage core
     */
    class ResetPassword extends framework\cli\Command
    {

        public function do_execute()
        {
            $username = $this->getProvidedArgument('username');
            $password = trim($this->getProvidedArgument('password'));

            $user = User::getByUsername($username);
            if (!$user instanceof User) {
                throw new Exception("Invalid username {$username}");
            }

            $this->cliEcho('Setting new password for user ');
            $this->cliEcho("{$username}\n", 'white', 'bold');
            if (!$password) {
                $password = $user->createPassword();
                $this->cliEcho("\n" . str_pad('', strlen($password), '-') . "\n");
                $this->cliEcho($password . "\n", 'green');
                $this->cliEcho(str_pad('', strlen($password), '-') . "\n\n");
            } else {
                $this->cliEcho("Password specified via command line\n");
            }
            $user->setPassword($password);
            $user->save();

            $this->cliEcho("Done!\n");
        }

        protected function _setup()
        {
            $this->_command_name = 'auth_reset_password';
            $this->_description = 'Set or reset the password for a specific user';
            $this->addRequiredArgument('username', 'The username of the user to reset password for');
            $this->addOptionalArgument('password', 'The new password');
        }

    }
