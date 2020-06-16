    <?php

        if ($return_notification)
        {
            switch ($notification->getNotificationType())
            {
                case \pachno\core\entities\Notification::TYPE_ISSUE_CREATED:
                    ?>
                    <?php echo $notification->getTarget()->getProject()->getName(); ?>
                    <?php echo \pachno\core\framework\Context::getI18n()->formatTime($notification->getCreatedAt(), 20); ?>
                    <?php echo __('%user_name created a new issue under %project_name', array('%user_name' => get_component_html('main/userdropdown_inline.text', array('user' => $notification->getTarget()->getPostedBy())), '%project_name' => $notification->getTarget()->getProject()->getName())); ?>
                    <?php echo $notification->getTarget()->getFormattedIssueNo(true); ?> - <?php echo $notification->getTarget()->getTitle(); ?>
                    <?php
                    break;
                case \pachno\core\entities\Notification::TYPE_ISSUE_UPDATED:
                    ?>
                    <?php echo $notification->getTarget()->getProject()->getName(); ?>
                    <?php echo \pachno\core\framework\Context::getI18n()->formatTime($notification->getCreatedAt(), 20); ?>
                    <?php echo __('%issue_no was updated by %user_name', array('%user_name' => get_component_html('main/userdropdown_inline.text', array('user' => $notification->getTriggeredByUser())), '%issue_no' => $notification->getTarget()->getFormattedIssueNo(true) .' - '. $notification->getTarget()->getTitle())); ?>
                    <?php
                    break;
                case \pachno\core\entities\Notification::TYPE_ISSUE_COMMENTED:
                    ?>
                    <?php echo $notification->getTarget()->getTarget()->getProject()->getName(); ?>
                    <?php echo \pachno\core\framework\Context::getI18n()->formatTime($notification->getCreatedAt(), 20); ?>
                    <?php echo __('%user_name posted a %comment on %issue_no', array('%user_name' => get_component_html('main/userdropdown_inline.text', array('user' => $notification->getTriggeredByUser())), '%comment' => __('%username_posted_a comment %on_issue', array('%username_posted_a' => '', '%on_issue' => '')), '%issue_no' => $notification->getTarget()->getTarget()->getFormattedIssueNo(true) .' - '. $notification->getTarget()->getTarget()->getTitle())); ?>
                    <?php
                    break;
                case \pachno\core\entities\Notification::TYPE_COMMENT_MENTIONED:
                    if ($notification->getTarget()->getTargetType() == \pachno\core\entities\Comment::TYPE_ISSUE): ?>
                        <?php echo $notification->getTarget()->getTarget()->getProject()->getName(); ?>
                        <?php echo \pachno\core\framework\Context::getI18n()->formatTime($notification->getCreatedAt(), 20); ?>
                        <?php echo __('%user_name mentioned you in a %comment on %issue_no', array('%user_name' => get_component_html('main/userdropdown_inline.text', array('user' => $notification->getTriggeredByUser())), '%comment' => __('%username_posted_a comment %on_issue', array('%username_posted_a' => '', '%on_issue' => '')), '%issue_no' => $notification->getTarget()->getTarget()->getFormattedIssueNo(true) .' - '. $notification->getTarget()->getTarget()->getTitle())); ?>
                    <?php else: ?>
                        <?php echo $notification->getTarget()->getTarget()->getProject()->getName(); ?>
                        <?php echo \pachno\core\framework\Context::getI18n()->formatTime($notification->getCreatedAt(), 20); ?>
                        <?php echo __('%user_name mentioned you in a %comment on %article_name', array('%user_name' => get_component_html('main/userdropdown_inline.text', array('user' => $notification->getTriggeredByUser())), '%comment' => __('%username_posted_a comment %on_issue', array('%username_posted_a' => '', '%on_issue' => '')), '%article_name' => $notification->getTarget()->getTarget()->getName())); ?>
                    <?php endif; ?>
                    <?php
                    break;
                case \pachno\core\entities\Notification::TYPE_ARTICLE_COMMENTED:
                    ?>
                    <?php echo $notification->getTarget()->getTarget()->getProject()->getName(); ?>
                    <?php echo \pachno\core\framework\Context::getI18n()->formatTime($notification->getCreatedAt(), 20); ?>
                    <?php echo __('%user_name posted a %comment on %article_name', array('%user_name' => get_component_html('main/userdropdown_inline.text', array('user' => $notification->getTriggeredByUser())), '%comment' => __('%username_posted_a comment %on_issue', array('%username_posted_a' => '', '%on_issue' => '')), '%article_name' => $notification->getTarget()->getTarget()->getName())); ?>
                    <?php
                    break;
                case \pachno\core\entities\Notification::TYPE_ARTICLE_CREATED:
                    ?>
                    <?php echo $notification->getTarget()->getProject()->getName(); ?>
                    <?php echo \pachno\core\framework\Context::getI18n()->formatTime($notification->getCreatedAt(), 20); ?>
                    <?php echo __('%user_name created a new article %article_name', array('%user_name' => get_component_html('main/userdropdown_inline.text', array('user' => $notification->getTriggeredByUser())), '%article_name' => $notification->getTarget()->getName())); ?>
                    <?php
                    break;
                case \pachno\core\entities\Notification::TYPE_ARTICLE_UPDATED:
                    ?>
                    <?php echo $notification->getTarget()->getProject()->getName(); ?>
                    <?php echo \pachno\core\framework\Context::getI18n()->formatTime($notification->getCreatedAt(), 20); ?>
                    <?php echo __('%user_name updated %article_name', array('%user_name' => get_component_html('main/userdropdown_inline.text', array('user' => $notification->getTriggeredByUser())), '%article_name' => $notification->getTarget()->getName())); ?>
                    <?php
                    break;
                case \pachno\core\entities\Notification::TYPE_ISSUE_MENTIONED:
                    ?>
                    <?php echo $notification->getTarget()->getProject()->getName(); ?>
                    <?php echo \pachno\core\framework\Context::getI18n()->formatTime($notification->getCreatedAt(), 20); ?>
                    <?php echo __('%user_name mentioned you in an issue %issue_no', array('%user_name' => get_component_html('main/userdropdown_inline.text', array('user' => $notification->getTriggeredByUser())), '%issue_no' => $notification->getTarget()->getFormattedIssueNo(true) .' - '. $notification->getTarget()->getTitle())); ?>
                    <?php
                    break;
                case \pachno\core\entities\Notification::TYPE_ARTICLE_MENTIONED:
                    ?>
                    <?php echo $notification->getTarget()->getProject()->getName(); ?>
                    <?php echo \pachno\core\framework\Context::getI18n()->formatTime($notification->getCreatedAt(), 20); ?>
                    <?php echo __('%user_name mentioned you in an article %article_name', array('%user_name' => get_component_html('main/userdropdown_inline.text', array('user' => $notification->getTriggeredByUser())), '%article_name' => $notification->getTarget()->getName())); ?>
                    <?php
                    break;
                default:
                    \pachno\core\framework\Event::createNew('core', '_notification_view_text', $notification)->trigger();
            }
        }

    ?>
