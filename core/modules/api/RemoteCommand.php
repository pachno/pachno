<?php

    namespace pachno\core\modules\api;

    use GuzzleHttp\Client,
        GuzzleHttp\Psr7\Request,
        pachno\core\framework\cli\Command;

    /**
     * CLI remote command class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage core
     */

    /**
     * CLI remote command class
     *
     * @package pachno
     * @subpackage core
     */
    abstract class RemoteCommand extends Command
    {

        protected $_current_remote_server = null;

        protected $_current_remote_user = null;

        protected $_current_remote_password_hash = null;

        protected $_verify_ssl = true;

        protected function _initializeUrlFopen()
        {
            if (!ini_get('allow_url_fopen'))
            {
                $this->cliEcho("The php.ini directive ", 'yellow');
                $this->cliEcho("allow_url.fopen", 'yellow', 'bold');
                $this->cliEcho(" is not set to 1\n", 'yellow');
                $this->cliEcho("Trying to set correct value for the current run ...");
                ini_set('allow_url_fopen', 1);
                if (!ini_get('allow_url_fopen'))
                {
                    throw new \Exception('Could not set "allow_url_fopen" to correct value. Please fix your cli configuration.');
                }
                else
                {
                    $this->cliEcho('OK', 'green', 'bold');
                    $this->cliEcho("\n\n");
                }
            }
        }
        
        protected function _setup()
        {
            $this->_initializeUrlFopen();
            $this->addOptionalArgument('no-verify-ssl', 'Set this argument to 1 to skip SSL verification (for example when using a self-signed certificate). NOT RECOMMENDED.');
        }

        protected function _prepare()
        {
            if (file_exists(PACHNO_CONFIG_PATH . '.remote_token')) {
                $this->_current_remote_server = file_get_contents(PACHNO_CONFIG_PATH . '.remote_server');
                $this->_current_remote_user = file_get_contents(PACHNO_CONFIG_PATH . '.remote_username');
                $this->_current_remote_password_hash = file_get_contents(PACHNO_CONFIG_PATH . '.remote_token');
            }
            if ($this->getProvidedArgument('no-verify-ssl')) {
                $this->_verify_ssl = false;
            }
        }

        protected function _getCurrentRemoteServer()
        {
            return $this->_current_remote_server;
        }

        protected function _getCurrentRemoteUser()
        {
            return $this->_current_remote_user;
        }

        protected function _getCurrentRemotePasswordHash()
        {
            return $this->_current_remote_password_hash;
        }

        protected function getRemoteResponse($url, $form_params = [])
        {
            $headers = [
                'Accept-language' => 'en',
                'Accept' => 'application/json'
            ];
            if ($this->getCommandName() != 'authenticate')
            {
                if (!file_exists(PACHNO_CONFIG_PATH . '.remote_server') ||
                    !file_exists(PACHNO_CONFIG_PATH . '.remote_username') ||
                    !file_exists(PACHNO_CONFIG_PATH . '.remote_token'))
                {
                    throw new \Exception("Please specify an installation of The Bug Genie to connect to by running the remote:authenticate command first");
                }
                $headers["Authorization"] = "Bearer {$this->_getCurrentRemoteUser()}.{$this->_getCurrentRemotePasswordHash()}";
            }

            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $this->_current_remote_server,
                // You can set any number of default request options.
                'timeout'  => 5.0,
                'verify' => $this->_verify_ssl
            ]);
            $method = (empty($form_params)) ? 'GET' : 'POST';
            $options = ['headers' => $headers];
            if ($form_params) {
                $options['form_params'] = $form_params;
            }
            if ($method === 'GET') {
                $response = $client->get($url, $options);
            } else {
                $response = $client->post($url, $options);
            }

            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody(), false, 512, JSON_THROW_ON_ERROR);
            } else {
                throw new \Exception($url . " could not be retrieved:\n" . $response->getBody());
            }
        }

        protected function getRemoteURL($route_name, $params = [])
        {
            $url = \pachno\core\framework\Context::getRouting()->generate($route_name, $params, true);
            $host = $this->_getCurrentRemoteServer();
            if (mb_substr($host, mb_strlen($host) - 2) != '/') $host .= '/';

            $final_url = $host . mb_substr($url, 1);

            return $final_url;
        }

    }
