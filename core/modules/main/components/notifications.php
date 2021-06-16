<?php

    use pachno\core\entities\Notification;

    /**
     * @var Notification[] $notifications
     * @var int $num_unread
     * @var int $num_read
     */

?>
<div class="notifications dropdown-container list-mode dynamic_menu populate-once" id="user_notifications" data-menu-url="<?= make_url('get_partial_for_backdrop', ['key' => 'notifications']); ?>" data-simplebar>
    <?php if ($num_unread + $num_read == 0): ?>
        <div class="onboarding">
            <div class="image-container">
                <?= image_tag('/unthemed/no-notifications.png', [], true); ?>
            </div>
            <div class="helper-text">
                <?= __('No news is good news'); ?><br>
                <?= __('You will be notified when something happens'); ?>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($notifications as $notification): ?>
        <div class="list-item multiline <?= ($notification->isRead()) ? 'read' : 'unread'; ?>" id="notification_<?= $notification->getID(); ?>_container" data-notification-id="<?= $notification->getID(); ?>">
            <span class="icon">
                <?php if ($notification->getTarget() instanceof \pachno\core\entities\Issue): ?>
                    <?php if ($notification->getTarget()->hasIssueType()) echo fa_image_tag($notification->getTarget()->getIssueType()->getFontAwesomeIcon(), ['class' => (($notification->getTarget()->hasIssueType()) ? 'issuetype-icon issuetype-' . $notification->getTarget()->getIssueType()->getIcon() : 'issuetype-icon issuetype-unknown')]); ?>
                <?php else: ?>
                    <?= image_tag($notification->getTarget()->getProject()->getIconName(), ['class' => 'notification-project-logo'], true); ?>
                <?php endif; ?>
            </span>
            <span class="name">
            <?php

                switch ($notification->getNotificationType())
                {
                    case Notification::TYPE_ISSUE_CREATED:
                        ?>
                        <span class="title">
                            <?php echo $notification->getTarget()->getFormattedTitle(true); ?>
                        </span>
                        <span class="description">
                            <?= __('%user_name created a new issue', ['%user_name' => '<span class="userlink inline">' . image_tag($notification->getTriggeredByUser()->getAvatarURL(), ['class' => 'avatar small'], true) . '@' . $notification->getTriggeredByUser()->getUsername() . '</span>']); ?>
                        </span>
                        <span class="information">
                            <span class="row">
                                <time class="item"><?= \pachno\core\framework\Context::getI18n()->formatTime($notification->getCreatedAt(), 20); ?></time>
                                <span class="item"><?= $notification->getTarget()->getProject()->getName(); ?></span>
                            </span>
                            <span class="row">
                                <a class="button secondary highlight" href="<?= $notification->getTarget()->getUrl(); ?>"><?= __('Open this issue'); ?></a>
                            </span>
                        </span>
                        <?php
                        break;
                    case Notification::TYPE_ISSUE_UPDATED:
                        ?>
                        <span class="title">
                            <?= image_tag($notification->getTarget()->getProject()->getIconName(), array('class' => 'notification-project-logo'), true); ?>
                            <time><?= \pachno\core\framework\Context::getI18n()->formatTime($notification->getCreatedAt(), 20); ?></time>
                            <?= __('%issue_no was updated by %user_name', array('%user_name' => get_component_html('main/userdropdown_inline', array('user' => $notification->getTriggeredByUser())), '%issue_no' => link_tag($notification->getTarget()->getUrl(), $notification->getTarget()->getFormattedIssueNo(true)) .' - '. $notification->getTarget()->getTitle())); ?>
                        </span>
                        <?php
                        break;
                    case Notification::TYPE_ISSUE_COMMENTED:
                        ?>
                        <span class="title">
                            <?= image_tag($notification->getTarget()->getTarget()->getProject()->getIconName(), array('class' => 'notification-project-logo'), true); ?>
                            <time><?= \pachno\core\framework\Context::getI18n()->formatTime($notification->getCreatedAt(), 20); ?></time>
                            <?= __('%user_name posted a %comment on %issue_no', array('%user_name' => get_component_html('main/userdropdown_inline', array('user' => $notification->getTriggeredByUser())), '%comment' => link_tag($notification->getTarget()->getTarget()->getUrl().'#comment_'.$notification->getTarget()->getID(), __('%username_posted_a comment %on_issue', array('%username_posted_a' => '', '%on_issue' => ''))), '%issue_no' => link_tag($notification->getTarget()->getTarget()->getUrl(), $notification->getTarget()->getTarget()->getFormattedIssueNo(true)) .' - '. $notification->getTarget()->getTarget()->getTitle())); ?>
                        </span>
                        <?php
                        break;
                    case Notification::TYPE_COMMENT_MENTIONED:
                        if ($notification->getTarget()->getTargetType() == \pachno\core\entities\Comment::TYPE_ISSUE): ?>
                            <span class="title">
                                <?= image_tag($notification->getTarget()->getTarget()->getProject()->getIconName(), array('class' => 'notification-project-logo'), true); ?>
                                <time><?= \pachno\core\framework\Context::getI18n()->formatTime($notification->getCreatedAt(), 20); ?></time>
                                <?= __('%user_name mentioned you in a %comment on %issue_no', array('%user_name' => get_component_html('main/userdropdown_inline', array('user' => $notification->getTriggeredByUser())), '%comment' => link_tag($notification->getTarget()->getTarget()->getUrl().'#comment_'.$notification->getTarget()->getID(), __('%username_posted_a comment %on_issue', array('%username_posted_a' => '', '%on_issue' => ''))), '%issue_no' => link_tag($notification->getTarget()->getTarget()->getUrl(), $notification->getTarget()->getTarget()->getFormattedIssueNo(true)) .' - '. $notification->getTarget()->getTarget()->getTitle())); ?>
                            </span>
                        <?php else: ?>
                            <span class="title">
                                <?= image_tag($notification->getTarget()->getTarget()->getProject()->getIconName(), array('class' => 'notification-project-logo'), true); ?>
                                <time><?= \pachno\core\framework\Context::getI18n()->formatTime($notification->getCreatedAt(), 20); ?></time>
                                <?= __('%user_name mentioned you in a %comment on %article_name', array('%user_name' => get_component_html('main/userdropdown_inline', array('user' => $notification->getTriggeredByUser())), '%comment' => link_tag(make_url('publish_article', array('article_name' => $notification->getTarget()->getTarget()->getName())).'#comment_'.$notification->getTarget()->getID(), __('%username_posted_a comment %on_issue', array('%username_posted_a' => '', '%on_issue' => ''))), '%article_name' => link_tag(make_url('publish_article', array('article_name' => $notification->getTarget()->getTarget()->getName())), $notification->getTarget()->getTarget()->getName()))); ?>
                            </span>
                        <?php endif; ?>
                        <?php
                        break;
                    case Notification::TYPE_ARTICLE_COMMENTED:
                        ?>
                        <span class="title">
                            <?= image_tag($notification->getTarget()->getTarget()->getProject()->getIconName(), array('class' => 'notification-project-logo'), true); ?>
                            <time><?= \pachno\core\framework\Context::getI18n()->formatTime($notification->getCreatedAt(), 20); ?></time>
                            <?= __('%user_name posted a %comment on %article_name', array('%user_name' => get_component_html('main/userdropdown_inline', array('user' => $notification->getTriggeredByUser())), '%comment' => link_tag(make_url('publish_article', array('article_name' => $notification->getTarget()->getTarget()->getName())).'#comment_'.$notification->getTarget()->getID(), __('%username_posted_a comment %on_issue', array('%username_posted_a' => '', '%on_issue' => ''))), '%article_name' => link_tag(make_url('publish_article', array('article_name' => $notification->getTarget()->getTarget()->getName())), $notification->getTarget()->getTarget()->getName()))); ?>
                        </span>
                        <?php
                        break;
                    case Notification::TYPE_ARTICLE_CREATED:
                        ?>
                        <span class="title">
                            <?= image_tag($notification->getTarget()->getProject()->getIconName(), array('class' => 'notification-project-logo'), true); ?>
                            <time><?= \pachno\core\framework\Context::getI18n()->formatTime($notification->getCreatedAt(), 20); ?></time>
                            <?= __('%user_name created a new article %article_name', array('%user_name' => get_component_html('main/userdropdown_inline', array('user' => $notification->getTriggeredByUser())), '%article_name' => link_tag(make_url('publish_article', array('article_name' => $notification->getTarget()->getName())), $notification->getTarget()->getName()))); ?>
                        </span>
                        <?php
                        break;
                    case Notification::TYPE_ARTICLE_UPDATED:
                        ?>
                        <span class="title">
                            <?= image_tag($notification->getTarget()->getProject()->getIconName(), array('class' => 'notification-project-logo'), true); ?>
                            <time><?= \pachno\core\framework\Context::getI18n()->formatTime($notification->getCreatedAt(), 20); ?></time>
                            <?= __('%user_name updated %article_name', array('%user_name' => get_component_html('main/userdropdown_inline', array('user' => $notification->getTriggeredByUser())), '%article_name' => link_tag(make_url('publish_article', array('article_name' => $notification->getTarget()->getName())), $notification->getTarget()->getName()))); ?>
                        </span>
                        <?php
                        break;
                    case Notification::TYPE_ISSUE_MENTIONED:
                        ?>
                        <span class="title">
                            <?= image_tag($notification->getTarget()->getProject()->getIconName(), array('class' => 'notification-project-logo'), true); ?>
                            <time><?= \pachno\core\framework\Context::getI18n()->formatTime($notification->getCreatedAt(), 20); ?></time>
                            <?= __('%user_name mentioned you in an issue %issue_no', array('%user_name' => get_component_html('main/userdropdown_inline', array('user' => $notification->getTriggeredByUser())), '%issue_no' => link_tag($notification->getTarget()->getUrl(), $notification->getTarget()->getFormattedIssueNo(true)) .' - '. $notification->getTarget()->getTitle())); ?>
                        </span>
                        <?php
                        break;
                    case Notification::TYPE_ARTICLE_MENTIONED:
                        ?>
                        <span class="title">
                            <?= image_tag($notification->getTarget()->getProject()->getIconName(), array('class' => 'notification-project-logo'), true); ?>
                            <time><?= \pachno\core\framework\Context::getI18n()->formatTime($notification->getCreatedAt(), 20); ?></time>
                            <?= __('%user_name mentioned you in an article %article_name', array('%user_name' => get_component_html('main/userdropdown_inline', array('user' => $notification->getTriggeredByUser())), '%article_name' => link_tag(make_url('publish_article', array('article_name' => $notification->getTarget()->getName())), $notification->getTarget()->getName()))); ?>
                        </span>
                        <?php
                        break;
                    default:
                        \pachno\core\framework\Event::createNew('core', '_notification_view', $notification)->trigger();
                }

            ?>
            </span>
        </div>
            <?php
                // Replace multiple spaces with single space with regex, apply trim, decode entities to show non standard characters and strip tags to remove any left / decoded "injections" to retrieve only valid text of notification.
                if (($notification_text = strip_tags(html_entity_decode(trim(preg_replace('!\s+!', ' ', get_component_html('main/notification_text', compact('notification')))), ENT_COMPAT, \pachno\core\framework\Context::getI18n()->getCharset()))) != ''):
            ?>
                <script>
                    <?php /* require(['domReady', 'pachno/index'], function (domReady, Pachno) {
                        domReady(function () {
                            Pachno.Main.Notifications.Web.Send("<?= __('New notification'); ?>", "<?= $notification_text; ?>", '<?= $notification->getID(); ?>', '<?= $notification->getTriggeredByUser()->getAvatarURL(); ?>', function () {
                                var target_url = "<?= $notification->getTargetUrl(); ?>";
                                var desktop_notifications_new_tab = <?= $desktop_notifications_new_tab ? 'true' : 'false'; ?>;
                                if (target_url.startsWith('http')) {
                                    if (desktop_notifications_new_tab) {
                                        window.open(target_url, '_blank').focus();
                                    }
                                    else {
                                        window.location = target_url;
                                    }
                                }
                                else {
                                    Pachno.UI.Backdrop.show(target_url);
                                }
                            });
                        });
                    }); */ ?>
                </script>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
