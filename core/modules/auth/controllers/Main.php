<?php

    namespace pachno\core\modules\auth\controllers;

    use Exception;
    use pachno\core\entities;
    use pachno\core\framework;
    use pachno\core\framework\Settings;
    use pachno\core\framework\Request;

    /**
     * actions for the user module
     *
     * @Routes(name_prefix="auth_")
     */
    class Main extends framework\Action
    {

        /**
         * Reset user password
         *
         * @Route(name="reset_password", url="/reset/password/:user/:reset_hash")
         * @AnonymousRoute
         *
         * @param Request $request The request object
         */
        public function runResetPassword(Request $request)
        {
            $i18n = framework\Context::getI18n();

            try {
                if ($request->hasParameter('user') && $request->hasParameter('reset_hash')) {
                    $user = entities\User::getByUsername(str_replace('%2E', '.', $request['user']));
                    if ($user instanceof entities\User) {
                        if ($request['reset_hash'] == $user->getActivationKey()) {
                            $this->error = false;
                            if ($request->isPost()) {
                                $p1 = trim($request['password_1']);
                                $p2 = trim($request['password_2']);

                                if ($p1 && $p2 && $p1 == $p2) {
                                    $user->setPassword($p1);
                                    $user->regenerateActivationKey();
                                    $user->save();
                                    framework\Context::setMessage('login_message', $i18n->__('Your password has been reset. Please log in.'));
                                    framework\Context::setMessage('login_referer', $this->getRouting()->generate('home'));

                                    return $this->forward($this->getRouting()->generate('auth_login_page'));
                                } else {
                                    $this->error = true;
                                }
                            } else {
                                $user->regenerateActivationKey();
                            }
                            $this->user = $user;
                        } else {
                            throw new Exception('Your password recovery token is either invalid or has expired');
                        }
                    } else {
                        throw new Exception('User is invalid or does not exist');
                    }
                } else {
                    throw new Exception('An internal error has occured');
                }
            } catch (Exception $e) {
                framework\Context::setMessage('login_message_err', $i18n->__($e->getMessage()));

                return $this->forward($this->getRouting()->generate('auth_login_page'));
            }
        }

    }
