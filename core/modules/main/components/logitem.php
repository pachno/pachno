<?php

    use pachno\core\entities\Milestone;
    use pachno\core\framework\Context;
    use pachno\core\entities\LogItem;
    use pachno\core\entities\Commit;
    use pachno\core\entities\Issue;

    /**
     * @var LogItem $item
     * @var bool $include_issue_title
     * @var bool $include_time
     * @var bool $include_project
     */

?>
<?php if ($item->getChangeType() == LogItem::ACTION_BUILD_RELEASED): ?>
    <div class="log-item">
        <div class="imgtd" style="position: absolute; margin-top: -1px; margin-left: 4px;"><?php echo image_tag('icon_build.png'); ?></div>
        <div style="clear: both;">
            <span class="time"><?php echo Context::getI18n()->formatTime($item->getTime(), 19); ?></span>&nbsp;<span style="font-size: 1.1em"><?php echo $item->getBuild()->getName(); ?></span><br><span style="display: inline-block; margin-top: 4px; margin-bottom: 15px;"><i><?php echo __('New version released'); ?></i></span>
        </div>
    </div>
<?php elseif ($item->getChangeType() == LogItem::ACTION_MILESTONE_STARTED): ?>
    <div class="log-item">
        <div class="imgtd" style="position: absolute; margin-top: -1px; margin-left: 4px;"><?php echo image_tag('icon_sprint.png'); ?></div>
        <div style="clear: both;">
            <span class="time"><?php echo Context::getI18n()->formatTime($item->getTime(), 19); ?></span>&nbsp;<span style="font-size: 1.1em"><?php echo $item->getMilestone()->getName(); ?></span><br><span style="display: inline-block; margin-top: 4px; margin-bottom: 15px;"><i><?php echo __('A new sprint has started'); ?></i></span>
        </div>
    </div>
<?php elseif ($item->getChangeType() == LogItem::ACTION_MILESTONE_REACHED && $item->getMilestone() instanceof Milestone): ?>
    <div class="log-item">
        <?php if ($item->getMilestone()->isSprint()): ?>
            <div class="imgtd" style="position: absolute; margin-top: -1px; margin-left: 4px;"><?php echo image_tag('icon_sprint.png'); ?></div>
            <div style="clear: both;">
                <span class="time"><?php echo Context::getI18n()->formatTime($item->getTime(), 19); ?></span>&nbsp;<span style="font-size: 1.1em"><?php echo $item->getMilestone()->getName(); ?></span><br><span style="display: inline-block; margin-top: 4px; margin-bottom: 15px;"><i><?php echo __('The sprint has ended'); ?></i></span>
            </div>
        <?php else: ?>
            <div class="imgtd" style="position: absolute; margin-top: -1px; margin-left: 4px;"><?php echo image_tag('icon_milestone.png'); ?></div>
            <div style="clear: both;">
                <span class="time"><?php echo Context::getI18n()->formatTime($item->getTime(), 19); ?></span>&nbsp;<span style="font-size: 1.1em"><?php echo $item->getText(); ?></span><br><span style="display: inline-block; margin-top: 4px; margin-bottom: 15px;"><i><?php echo __('A new milestone has been reached'); ?></i></span>
            </div>
        <?php endif; ?>
    </div>
<?php elseif ($item->getTargetType() == LogItem::TYPE_COMMIT && $item->getCommit() instanceof Commit): ?>
    <div class="log-item">
        <div class="imgtd"<?php if ($include_issue_title): ?> style="padding-top: <?php echo (isset($extra_padding) && $extra_padding) ? 10 : 3; ?>px;"<?php endif; ?>>
            <?php if ($include_issue_title): ?>
                <?php echo fa_image_tag('code-branch'); ?>
            <?php endif; ?>
        </div>
        <div style="clear: both;<?php if ($include_issue_title): ?> padding-bottom: <?php echo (isset($extra_padding) && $extra_padding) ? 15 : 10; ?>px;<?php endif; ?>">
            <?php if (($include_issue_title) && (isset($include_time) && $include_time == true)): ?><span class="time"><?php echo Context::getI18n()->formatTime($item->getTime(), 19); ?></span>&nbsp;<?php endif; ?>
            <?php if ($include_issue_title): ?>
                <?php echo link_tag(make_url('livelink_project_commit', ['commit_hash' => $item->getCommit()->getRevision(), 'project_key' => $item->getProject()->getKey()]), $title, ['style' => 'margin-top: 7px;'], $item->getCommit()->getTitle()); ?>
            <?php endif; ?>
            <?php if (($include_issue_title) && (isset($include_user) && $include_user == true)): ?>
                <br>
                <span class="user">
                    <?php if ($item->getUser() instanceof \pachno\core\entities\User): ?>
                        <?php if ($item->getChangeType() != LogItem::ACTION_COMMENT_CREATED): ?>
                            <?php echo $item->getUser()->getNameWithUsername().':'; ?>
                        <?php else: ?>
                            <?php echo __('%user said', array('%user' => $item->getUser()->getNameWithUsername())).':'; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if ($item->getChangeType() != LogItem::ACTION_COMMENT_CREATED): ?>
                            <span class="faded"><?php echo __('Unknown user').':'; ?></span>
                        <?php else: ?>
                            <?php echo __('Unknown user said').':'; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </span>
            <?php elseif ($include_issue_title): ?>
                <br>
            <?php endif; ?>
        </div>
    </div>
<?php elseif ($item->getTargetType() == LogItem::TYPE_ISSUE && $item->getIssue() instanceof Issue && !($item->getIssue()->isDeleted()) && $item->getIssue()->hasAccess()): ?>
    <div class="log-item <?php if (!$include_issue_title) echo 'without-title'; ?>">
        <div class="user-icon">
            <?php echo image_tag($item->getUser()->getAvatarURL(), ['class' => 'avatar large'], true); ?>
        </div>
        <div class="content">
            <?php if ($include_issue_title): ?>
                <div class="title-container">
                    <?php if ($include_project): ?>
                        <span class="faded_out smaller"><?php echo image_tag($item->getIssue()->getProject()->getIconName(), array('class' => 'issuelog-project-logo'), true); ?></span>
                    <?php endif; ?>
                    <a href="<?= $item->getIssue()->getUrl(); ?>" class="issue-link <?= (($item->getChangeType() == LogItem::ACTION_ISSUE_CLOSE) ? 'issue_closed' : 'issue_open'); ?>">
                        <span><?= Context::getI18n()->decodeUTF8($item->getIssue()->getFormattedTitle(true)); ?></span>
                    </a>
                    <div class="issue_more_actions_link_container dropper-container" style="display: none;">
                        <button title="<?php echo __('Show more actions'); ?>" class="button icon secondary dropper dynamic_menu_link" data-id="log_item_<?= $item->getId(); ?>_<?= $item->getIssue()->getID(); ?>" id="log_item_<?= $item->getID(); ?>_more_actions_<?= $item->getIssue()->getID(); ?>_button" href="javascript:void(0);"><?php echo fa_image_tag('ellipsis-v'); ?></button>
                        <?php include_component('main/issuemoreactions', array('issue' => $item->getIssue(), 'multi' => true, 'dynamic' => true)); ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="description">
                <?php if ($include_time): ?>
                    <span class="time count-badge"><?php echo Context::getI18n()->formatTime($item->getTime(), 19); ?></span>
                <?php endif; ?>
                <span><?php include_component('main/logitemtext', ['item' => $item]); ?></span>
            </div>
        </div>
    </div>
<?php else: ?>
    <?php //var_dump($item); ?>
<?php endif; ?>
