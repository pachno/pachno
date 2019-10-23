<?php

    namespace pachno\core\modules\mailing;

    use pachno\core\entities\tables\Articles;
    use pachno\core\framework\ActionComponent;
    use pachno\core\framework\Context;

    /**
     * Main action components
     */
    class Components extends ActionComponent
    {

        public function componentForgotPasswordPane()
        {
            $this->forgottenintro = Articles::getTable()->getArticleByName('ForgottenPasswordIntro');
        }

        public function componentForgotPasswordLink()
        {

        }

        public function componentSettings()
        {
        }

        public function componentAccountSettings()
        {

        }

        public function componentAccountSettings_NotificationCategories()
        {
            $category_notification_key = Mailing::NOTIFY_NEW_ISSUES_MY_PROJECTS_CATEGORY;
            $selected_category_notifications = [];
            foreach ($this->categories as $category_id => $category) {
                if ($this->getUser()->getNotificationSetting($category_notification_key . '_' . $category_id, false, 'mailing')->isOn()) {
                    $selected_category_notifications[] = $category_id;
                }
            }
            $this->selected_category_notifications = $selected_category_notifications;
            $this->category_key = $category_notification_key;
        }

        public function componentConfigCreateuserEmail()
        {

        }

        public function componentEditIncomingEmailAccount()
        {
            $this->project = Context::getCurrentProject();
        }

    }
