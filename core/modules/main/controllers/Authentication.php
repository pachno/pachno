<?php

namespace pachno\core\modules\main\controllers;

use pachno\core\entities\UserSession,
    pachno\core\framework,
    pachno\core\entities,
    PragmaRX\Google2FA\Google2FA;

/**
 * Login actions
 */
class Authentication extends framework\Action
{

    /**
     * @param framework\AuthenticationBackend $authentication_backend
     * @param entities\User $user
     * @param bool $persist
     */
    protected function _persistLogin($authentication_backend, $user, $persist): void
    {
        if ($authentication_backend->getAuthenticationMethod() == framework\AuthenticationBackend::AUTHENTICATION_TYPE_TOKEN) {
            $token = $user->createUserSession();
            $authentication_backend->persistTokenSession($user, $token, $persist);
        } else {
            $password = $user->getHashPassword();
            $authentication_backend->persistPasswordSession($user, $password, $persist);
        }
    }

    /**
     * @param framework\Request $request
     *
     * @return string
     */
    protected function _getLoginForwardUrl(framework\Request $request): string
    {
        $forward_url = $this->getRouting()->generate('home');

        if ($request->hasParameter('return_to')) {
            $forward_url = $request['return_to'];
        } else {
            if (framework\Settings::get('returnfromlogin') == 'referer') {
                $forward_url = $request->getParameter('referer', $this->getRouting()->generate('dashboard'));
            } else {
                $forward_url = $this->getRouting()->generate(framework\Settings::get('returnfromlogin'));
            }
        }

        return htmlentities($forward_url, ENT_COMPAT, framework\Context::getI18n()->getCharset());
    }

    /**
     * Static login page
     *
     * @Route(name="login_page", url="/login")
     * @AnonymousRoute
     *
     * @param framework\Request $request
     */
    public function runLogin(framework\Request $request)
    {
        $this->section = $request->getParameter('section', 'login');
    }

    /**
     * Static elevated login page
     *
     * @Route(name="elevated_login", url="/login/elevate", methods="POST")
     * @AnonymousRoute
     *
     * @param framework\Request $request
     */
    public function runDoElevatedLogin(framework\Request $request)
    {
        if (!$this->getUser()->hasPassword($request['elevated_password'])) {
            return $this->renderJSON(array('elevated' => false, 'error' => $this->getI18n()->__('Incorrect password')));
        }

        // Calculate expiration period in seconds. setCookie() method should
        // add expiration period to current time.
        $expiration = 60 * $request->getParameter('elevation_duration', 30);
        $authentication_backend = framework\Settings::getAuthenticationBackend();

        if ($authentication_backend->getAuthenticationMethod() == framework\AuthenticationBackend::AUTHENTICATION_TYPE_TOKEN) {
            $token = $this->getUser()->createUserSession();
            $token->setExpiresAt(time() + $expiration);
            $token->setIsElevated(true);
            $token->save();

            framework\Context::getResponse()->setCookie('elevated_session_token', $token->getToken(), $expiration);
        } else {
            framework\Context::getResponse()->setCookie('elevated_password', $this->getUser()->getHashPassword(), $expiration);
        }
        return $this->renderJSON(array('elevated' => true));
    }

    /**
     * Static elevated login page
     *
     * @Route(name="elevated_login_page", url="/login/elevate", methods="GET")
     *
     * @param framework\Request $request
     */
    public function runElevatedLogin(framework\Request $request)
    {
        if ($this->getUser()->isGuest()) {
            return $this->forward($this->getRouting()->generate('login_page'));
        }
    }

    /**
     * Static 2FA verification page
     *
     * @Route(name="2fa_code_input", url="/2fa", methods="GET")
     * @AnonymousRoute
     *
     * @param framework\Request $request
     */
    public function runTwoFactorVerification(framework\Request $request)
    {
        if (!$this->getUser()->isGuest()) {
            return $this->forward($this->getRouting()->generate('account'));
        }

        $this->session_token = framework\Context::getRequest()->getCookie('session_token');
    }

    /**
     * User 2FA verification action
     *
     * @Route(name="user_verify_2fa", url="/2fa", methods="POST")
     *
     * @param framework\Request $request
     */
    public function runVerify2FA(framework\Request $request)
    {
        $user = entities\tables\Users::getTable()->getByUsername($request['username']);
        $user_session = $this->getUser()->getUserSession($request['session_token']);

        if ($user_session instanceof UserSession) {
            $secret = $user->get2faToken();
            $google2fa = new Google2FA();
            $verified = $google2fa->verifyKey($secret, $request['2fa_code']);
        } else {
            $verified = false;
        }

        if (!$verified) {
            $this->getResponse()->setHttpStatus(400);
            return $this->renderJSON(['error' => $this->getI18n()->__('Incorrect verification code')]);
        }

        $user_session->setIs2FaVerified(true);
        $user_session->save();

        if ($this->getUser()->is2FaEnabled()) {
            return $this->renderJSON(['result' => 'verified', 'forward' => $this->getRouting()->generate('home')]);
        }

        $user->set2FaEnabled(true);
        $user->save();

        return $this->renderJSON(['result' => 'verified']);
    }

    /**
     * Switch user action
     *
     * @Route(name="switch_to_user", url="/userswitch/switch/:user_id/:csrf_token")
     *
     * @param framework\Request $request
     */
    public function runSwitchUser(framework\Request $request)
    {
        if (!$this->getUser()->canAccessConfigurationPage(framework\Settings::CONFIGURATION_SECTION_USERS) && !$request->hasCookie('original_username'))
            return $this->forward403();

        $response = $this->getResponse();
        $authentication_backend = framework\Settings::getAuthenticationBackend();
        if ($request['user_id']) {
            $user = new entities\User($request['user_id']);
            if ($authentication_backend->getAuthenticationMethod() == framework\AuthenticationBackend::AUTHENTICATION_TYPE_TOKEN) {
                $response->setCookie('original_username', $request->getCookie('username'));
                $response->setCookie('original_session_token', $request->getCookie('session_token'));
                framework\Context::getResponse()->setCookie('username', $user->getUsername());
                framework\Context::getResponse()->setCookie('session_token', $user->createUserSession()->getToken());
            } else {
                $response->setCookie('original_username', $request->getCookie('username'));
                $response->setCookie('original_password', $request->getCookie('password'));
                framework\Context::getResponse()->setCookie('password', $user->getHashPassword());
                framework\Context::getResponse()->setCookie('username', $user->getUsername());
            }
        } else {
            if ($authentication_backend->getAuthenticationMethod() == framework\AuthenticationBackend::AUTHENTICATION_TYPE_TOKEN) {
                $response->setCookie('username', $request->getCookie('original_username'));
                $response->setCookie('session_token', $request->getCookie('original_session_token'));
                framework\Context::getResponse()->deleteCookie('original_session_token');
                framework\Context::getResponse()->deleteCookie('original_username');
            } else {
                $response->setCookie('username', $request->getCookie('original_username'));
                $response->setCookie('password', $request->getCookie('original_password'));
                framework\Context::getResponse()->deleteCookie('original_password');
                framework\Context::getResponse()->deleteCookie('original_username');
            }
        }
        $this->forward($this->getRouting()->generate('home'));
    }

    /**
     * Do login (AJAX call)
     *
     * @Route(name="login", url="/do/login", methods="POST")
     * @AnonymousRoute
     *
     * @param framework\Request $request
     */
    public function runDoLogin(framework\Request $request)
    {
        $authentication_backend = framework\Settings::getAuthenticationBackend();

        try {
            $username = trim($request->getParameter('username', ''));
            $password = trim($request->getParameter('password', ''));
            $persist = (bool)$request->getParameter('rememberme', false);

            if ($username && $password) {
                $user = entities\User::identify($request, $this);

                if (!$user instanceof entities\User || $user->isGuest()) {
                    throw new \Exception('No such login');
                }

                $user->setOnline();
                $user->save();

                framework\Context::setUser($user);
                $this->verifyScopeMembership($user);

                if (!$user->isGuest()) {
                    $this->_persistLogin($authentication_backend, $user, $persist);
                }
            } else {
                throw new \Exception('Please enter a username and password');
            }
        } catch (\Exception $e) {
            $this->getResponse()->setHttpStatus(401);
            framework\Logging::log($e->getMessage(), 'auth', framework\Logging::LEVEL_WARNING_RISK);
            return $this->renderJSON(["error" => $this->getI18n()->__("Invalid login details")]);
        }

        if (!$user instanceof entities\User) {
            $this->getResponse()->setHttpStatus(401);
            return $this->renderJSON(["error" => $this->getI18n()->__("Invalid login details")]);
        }

        $forward_url = $this->_getLoginForwardUrl($request);
        return $this->renderJSON(['forward' => $forward_url]);
    }

    /**
     * Generate captcha picture
     *
     * @Route(name="captcha", url="/captcha/*")
     * @AnonymousRoute
     *
     * @param framework\Request $request The request object
     * @global array $_SESSION ['activation_number'] The session captcha activation number
     */
    public function runCaptcha(framework\Request $request)
    {
        framework\Context::loadLibrary('ui');

        if (!function_exists('imagecreatetruecolor')) {
            return $this->return404();
        }

        $this->getResponse()->setContentType('image/png');
        $this->getResponse()->setDecoration(\pachno\core\framework\Response::DECORATE_NONE);
        $chain = str_split($_SESSION['activation_number'], 1);
        $size = getimagesize(PACHNO_PATH . DS . 'themes' . DS . framework\Settings::getThemeName() . DS . 'images' . DS . 'numbers' . DS . '0.png');
        $captcha = imagecreatetruecolor($size[0] * sizeof($chain), $size[1]);
        foreach ($chain as $n => $number) {
            $pic = imagecreatefrompng(PACHNO_PATH . DS . 'themes' . DS . framework\Settings::getThemeName() . DS . 'images' . DS . 'numbers' . DS . "{$number}.png");
            imagecopymerge($captcha, $pic, $size[0] * $n, 0, 0, 0, imagesx($pic), imagesy($pic), 100);
            imagedestroy($pic);
        }
        imagepng($captcha);
        imagedestroy($captcha);

        return true;
    }

    /**
     * Logs the user out
     *
     * @Route(name="logout", url="/logout")
     * @param framework\Request $request
     */
    public function runLogout(framework\Request $request)
    {
        if ($this->getUser() instanceof entities\User) {
            framework\Logging::log('Setting user logout state');
            $this->getUser()->setOffline();
            $this->getUser()->save();
        }
        framework\Context::logout();
        if ($request->isAjaxCall()) {
            return $this->renderJSON(array('status' => 'logout ok', 'url' => framework\Context::getRouting()->generate(framework\Settings::getLogoutReturnRoute())));
        }
        $this->forward(framework\Context::getRouting()->generate(framework\Settings::getLogoutReturnRoute()));
    }

}
