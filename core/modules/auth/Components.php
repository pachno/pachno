<?php

    namespace pachno\core\modules\auth;

    use pachno\core\entities;
    use pachno\core\entities\tables;
    use pachno\core\framework;
    use pachno\core\framework\interfaces\AuthenticationProvider;
    use PragmaRX\Google2FA\Google2FA;

    /**
     * Class Components
     *
     * @property entities\User $user
     * @property entities\Issue[] $issues
     *
     * @package pachno\core\modules\user
     */
    class Components extends framework\ActionComponent
    {

        public function componentLoginpopup()
        {
            if (framework\Context::getRequest()->getParameter('redirect') == true)
                $this->mandatory = true;
        }

        public function componentLogin()
        {
            $this->selected_tab = isset($this->section) ? $this->section : 'login';
            $this->options = $this->getParameterHolder();

            if (framework\Context::hasMessage('login_referer')):
                $this->referer = htmlentities(framework\Context::getMessage('login_referer'), ENT_COMPAT, framework\Context::getI18n()->getCharset());
            elseif (array_key_exists('HTTP_REFERER', $_SERVER)):
                $this->referer = htmlentities($_SERVER['HTTP_REFERER'], ENT_COMPAT, framework\Context::getI18n()->getCharset());
            else:
                $this->referer = framework\Context::getRouting()->generate('dashboard');
            endif;

            try {
                $this->loginintro = null;
                $this->registrationintro = null;
                $this->loginintro = tables\Articles::getTable()->getArticleByName('LoginIntro');
                $this->registrationintro = tables\Articles::getTable()->getArticleByName('RegistrationIntro');
            } catch (Exception $e) {

            }

            if (framework\Settings::isLoginRequired()) {
                $authentication_backend = framework\Settings::getAuthenticationBackend();
                if ($authentication_backend->getAuthenticationMethod() == AuthenticationProvider::AUTHENTICATION_TYPE_TOKEN) {
                    framework\Context::getResponse()->deleteCookie('username');
                    framework\Context::getResponse()->deleteCookie('session_token');
                } else {
                    framework\Context::getResponse()->deleteCookie('username');
                    framework\Context::getResponse()->deleteCookie('password');
                }
                $this->error = framework\Context::geti18n()->__('You need to log in to access this site');
            }

            if (framework\Context::hasMessage('login_error')) {
                $this->error = framework\Context::getMessageAndClear('login_error');
            }
        }

        public function componentEnable2FA()
        {
            $secret = $this->getUser()->get2faToken();
            if (!$secret) {
                $google2fa = new Google2FA();
                $secret = $google2fa->generateSecretKey();
                $this->getUser()->set2faToken($secret);
                $this->getUser()->save();
            }

            $google2fa_qr_code = new \PragmaRX\Google2FAQRCode\Google2FA();
            $this->qr_code_inline = $google2fa_qr_code->getQRCodeInline('Pachno', $this->getUser()->getEmail(), $secret);
            $this->session_token = framework\Context::getRequest()->getCookie('session_token');
        }

        public function componentCaptcha()
        {
            if (!isset($_SESSION['activation_number'])) {
                $_SESSION['activation_number'] = pachno_get_activation_number();
            }
        }

    }
