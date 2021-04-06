<?php

    namespace pachno\core\modules\profile\controllers;

    use Exception;
    use pachno\core\entities;
    use pachno\core\framework;
    use pachno\core\framework\Settings;
    use pachno\core\framework\Request;

    /**
     * actions for the user module
     *
     * @Routes(name_prefix="profile_")
     */
    class Main extends framework\Action
    {

        /**
         * "My account" page
         *
         * @Route(name="account", url="/account/*")
         * @param Request $request
         */
        public function runAccount(Request $request)
        {
            $this->forward403unless($this->getUser()->hasPermission(entities\Permission::PERMISSION_PAGE_ACCESS_ACCOUNT));
            $categories = entities\Category::getAll();
            $projects = [];
            $project_subscription_key = Settings::SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS;
            $category_subscription_key = Settings::SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS_CATEGORY;
            $category_notification_key = Settings::SETTINGS_USER_NOTIFY_NEW_ISSUES_MY_PROJECTS_CATEGORY;
            $subscriptionssettings = Settings::getSubscriptionsSettings();
            $notificationsettings = Settings:: getNotificationSettings();
            $selected_project_subscriptions = [];
            $selected_category_subscriptions = [];
            $selected_category_notifications = [];
            $this->all_projects_subscription = $this->getUser()->getNotificationSetting($project_subscription_key, false)->isOn();
            foreach (entities\Project::getAll() as $project_id => $project) {
                if ($project->hasAccess()) {
                    $projects[$project_id] = $project;
                    if ($this->getUser()->getNotificationSetting($project_subscription_key . '_' . $project_id, false)->isOn()) {
                        $selected_project_subscriptions[] = $project_id;
                    }
                }
            }
            foreach ($categories as $category_id => $category) {
                if ($this->getUser()->getNotificationSetting($category_subscription_key . '_' . $category_id, false)->isOn()) {
                    $selected_category_subscriptions[] = $category_id;
                }
                if ($this->getUser()->getNotificationSetting($category_notification_key . '_' . $category_id, false)->isOn()) {
                    $selected_category_notifications[] = $category_id;
                }
            }
            $this->selected_project_subscriptions = ($this->all_projects_subscription) ? [] : $selected_project_subscriptions;
            $this->projects = $projects;
            $this->selected_category_subscriptions = $selected_category_subscriptions;
            $this->selected_category_notifications = $selected_category_notifications;
            $this->categories = $categories;
            $this->subscriptionssettings = $subscriptionssettings;
            $this->notificationsettings = $notificationsettings;
            $this->has_autopassword = framework\Context::hasMessage('auto_password');
            if ($this->has_autopassword) {
                $this->autopassword = framework\Context::getMessage('auto_password');
            }

            if ($request->isPost() && $request->hasParameter('mode')) {
                switch ($request['mode']) {
                    case 'information':
                        if (!$request['buddyname'] || !$request['email']) {
                            $this->getResponse()->setHttpStatus(400);

                            return $this->renderJSON(['error' => framework\Context::getI18n()->__('Please fill out all the required fields')]);
                        }
                        $this->getUser()->setBuddyname($request['buddyname']);
                        $this->getUser()->setRealname($request['realname']);
                        $this->getUser()->setHomepage($request['homepage']);
                        $this->getUser()->setEmailPrivate((bool)$request['email_private']);
                        $this->getUser()->setUsesGravatar((bool)$request['use_gravatar']);
                        $this->getUser()->setTimezone($request->getRawParameter('timezone'));
                        $this->getUser()->setLanguage($request['profile_language']);

                        if ($this->getUser()->getEmail() != $request['email']) {
                            if (framework\Event::createNew('core', 'changeEmail', $this->getUser(), ['email' => $request['email']])->triggerUntilProcessed()->isProcessed() == false) {
                                $this->getUser()->setEmail($request['email']);
                            }
                        }

                        $this->getUser()->save();

                        return $this->renderJSON(['message' => framework\Context::getI18n()->__('Profile information saved')]);
                        break;
                    case 'settings':
                        $this->getUser()->setPreferredWikiSyntax($request['syntax_articles']);
                        $this->getUser()->setPreferredIssuesSyntax($request['syntax_issues']);
                        $this->getUser()->setPreferredCommentsSyntax($request['syntax_comments']);
                        $this->getUser()->setKeyboardNavigationEnabled($request['enable_keyboard_navigation']);
                        $this->getUser()->save();

                        return $this->renderJSON(['title' => framework\Context::getI18n()->__('Profile settings saved')]);
                        break;
                    case 'notificationsettings':
                        $this->getUser()->setDesktopNotificationsNewTabEnabled($request['enable_desktop_notifications_new_tab']);
                        foreach ($subscriptionssettings as $setting => $description) {
                            if ($setting == Settings::SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS_CATEGORY) {
                                foreach ($categories as $category_id => $category) {
                                    if ($request->hasParameter('core_' . $setting . '_' . $category_id)) {
                                        $this->getUser()->setNotificationSetting($setting . '_' . $category_id, true);
                                    } else {
                                        $this->getUser()->setNotificationSetting($setting . '_' . $category_id, false);
                                    }
                                }
                            } elseif ($setting == Settings::SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS) {
                                if ($request->hasParameter('core_' . $setting . '_all')) {
                                    $this->getUser()->setNotificationSetting($setting, true);
                                    foreach (entities\Project::getAll() as $project_id => $project) {
                                        $this->getUser()->setNotificationSetting($setting . '_' . $project_id, false);
                                    }
                                } else {
                                    $this->getUser()->setNotificationSetting($setting, false);
                                    foreach (entities\Project::getAll() as $project_id => $project) {
                                        if ($request->hasParameter('core_' . $setting . '_' . $project_id)) {
                                            $this->getUser()->setNotificationSetting($setting . '_' . $project_id, true);
                                        } else {
                                            $this->getUser()->setNotificationSetting($setting . '_' . $project_id, false);
                                        }
                                    }
                                }
                            } else {
                                if ($request->hasParameter('core_' . $setting)) {
                                    $this->getUser()->setNotificationSetting($setting, true);
                                } else {
                                    $this->getUser()->setNotificationSetting($setting, false);
                                }
                            }
                        }

                        foreach ($notificationsettings as $setting => $description) {
                            if ($setting == Settings::SETTINGS_USER_NOTIFY_NEW_ISSUES_MY_PROJECTS_CATEGORY) {
                                foreach ($categories as $category_id => $category) {
                                    if ($request->hasParameter('core_' . $setting . '_' . $category_id)) {
                                        $this->getUser()->setNotificationSetting($setting . '_' . $category_id, true);
                                    } else {
                                        $this->getUser()->setNotificationSetting($setting . '_' . $category_id, false);
                                    }
                                }
                            } else {
                                if ($request->hasParameter('core_' . $setting)) {
                                    if ($setting == Settings::SETTINGS_USER_NOTIFY_GROUPED_NOTIFICATIONS) {
                                        $this->getUser()->setNotificationSetting($setting, $request->getParameter('core_' . $setting));
                                    } else {
                                        $this->getUser()->setNotificationSetting($setting, true);
                                    }
                                } else {
                                    $this->getUser()->setNotificationSetting($setting, false);
                                }
                            }
                        }

                        framework\Event::createNew('core', 'mainActions::myAccount::saveNotificationSettings')->trigger(compact('request', 'categories'));
                        $this->getUser()->save();

                        return $this->renderJSON(['message' => framework\Context::getI18n()->__('Notification settings saved')]);
                        break;
                    case 'module':
                        foreach (framework\Context::getAllModules() as $modules) {
                            foreach ($modules as $module_name => $module) {
                                if ($request['target_module'] == $module_name && $module->hasAccountSettings()) {
                                    try {
                                        if ($module->postAccountSettings($request)) {
                                            return $this->renderJSON(['title' => framework\Context::getI18n()->__('Settings saved')]);
                                        } else {
                                            $this->getResponse()->setHttpStatus(400);

                                            return $this->renderJSON(['error' => framework\Context::getI18n()->__('An error occured')]);
                                        }
                                    } catch (Exception $e) {
                                        $this->getResponse()->setHttpStatus(400);

                                        return $this->renderJSON(['error' => framework\Context::getI18n()->__($e->getMessage())]);
                                    }
                                }
                            }
                        }
                        break;
                }
            }
            $this->languages = framework\I18n::getLanguages();
            $this->timezones = framework\I18n::getTimezones();
            $this->error = framework\Context::getMessageAndClear('error');
            $this->username_chosen = framework\Context::getMessageAndClear('username_chosen');
            $this->openid_used = framework\Context::getMessageAndClear('openid_used');
            $this->rsskey_generated = framework\Context::getMessageAndClear('rsskey_generated');

            $this->selected_tab = 'profile';
            if ($this->rsskey_generated)
                $this->selected_tab = 'security';
        }

    }
