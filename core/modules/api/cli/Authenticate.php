<?php

    namespace pachno\core\modules\api\cli;

    /**
     * CLI command class, main -> set_remote
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage core
     */

    /**
     * CLI command class, main -> set_remote
     *
     * @package pachno
     * @subpackage core
     */
    class Authenticate extends \pachno\core\modules\api\RemoteCommand
    {

        protected function _setup()
        {
            $this->_command_name = 'authenticate';
            $this->_description = "Authenticate with a remote server";
            $this->addRequiredArgument('server_url', "The URL for the remote The Bug Genie installation");
            $this->addOptionalArgument('username', "The username to connect with. If not specified, will use the current logged in user");
            $this->_initializeUrlFopen();
        }
        
        public function do_execute()
        {
            $this->cliEcho('Authenticating with server: ');
            $this->cliEcho($this->getProvidedArgument('server_url'), 'white', 'bold');
            $this->cliEcho("\n");

            $path = PACHNO_CONFIG_PATH;
            try 
            {
                file_put_contents($path . '.remote_server', $this->getProvidedArgument('server_url'));
            }
            catch (\Exception $e)
            {
                $path = getenv('HOME') . DS;
                file_put_contents($path . '.remote_server', $this->getProvidedArgument('server_url'));
            }

            $this->cliEcho('Authenticating as user: ');
            $username = $this->getProvidedArgument('username', \pachno\core\framework\Context::getCurrentCLIusername());
            $this->cliEcho($username, 'white', 'bold');
            $this->cliEcho("\n");
            file_put_contents($path . '.remote_username', $username);
            $this->_current_remote_server = file_get_contents($path . '.remote_server');
            $this->cliEcho("\n");
            $this->cliEcho('You need to authenticate using an application-specific password.');
            $this->cliEcho("\n");
            $this->cliEcho("Create an application password from your account's 'Security' tab.");
            $this->cliEcho("\n");
            $this->cliEcho("Enter the application-specific password: ", 'white', 'bold');
            $password = self::_getCliInput();
            $response = $this->getRemoteResponse($this->getRemoteURL('api_authenticate'), array('username' => $username, 'password' => $password));
            if (!is_array($response))
            {
                throw new \Exception('An error occured when receiving authentication response from the server');
            }
            file_put_contents($path . '.remote_token', $response->token);
            $this->cliEcho("Authentication successful!\n", 'white', 'bold');
        }

    }
