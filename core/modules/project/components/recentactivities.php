<?php

/**
 * @var \pachno\core\entities\Issue[] $issues
 */

use pachno\core\framework\Context;

?>
<div id="tab_<?php echo $id ?>_pane"<?php if ($default_displayed !== true): ?> style="display: none;"<?php endif;?>>
    <?php if (isset($link)): echo $link; endif; ?>
    <?php if (count($issues) > 0): ?>
        <table cellpadding=0 cellspacing=0 class="recent_activities" style="margin-top: 5px;">
        <?php foreach ($issues as $issue): ?>
            <?php if ($issue->isDeleted()): continue; endif; ?>
            <tr>
                <td class="imgtd"><?= fa_image_tag(($issue->hasIssueType()) ? $issue->getIssueType()->getFontAwesomeIcon() : 'unknown', ['class' => (($issue->hasIssueType()) ? 'icon issuetype-icon issuetype-' . $issue->getIssueType()->getIcon() : 'icon issuetype-icon issuetype-unknown')]); ?></td>
                <td>
                    <?php echo link_tag($issue->getUrl(), '<b>' . $issue->getFormattedIssueNo(true) . ' - ' . $issue->getTitle() . '</b>', array('class' => (($issue->isClosed()) ? 'issue_closed' : 'issue_open'))); ?><br>
                    <span class="faded_out dark recent_activities_details">
                        <?php echo \pachno\core\framework\Context::getI18n()->formatTime($issue->getPosted(), 20); ?>,
                        <strong><?php echo ($issue->getStatus() instanceof \pachno\core\entities\Datatype) ? $issue->getStatus()->getName() : __('Status not determined'); ?></strong>
                        <?php if ($issue->isClosed() && is_object($issue->getResolution())): ?>
                        , <?php echo $issue->getResolution()->getName(); ?>
                        <?php endif; ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
        </table>
    <?php else: ?>
        <div class="onboarding unthemed">
            <div class="image-container">
                <?= image_tag('/unthemed/no-issues.png', [], true); ?>
            </div>
            <div class="helper-text">
                <?php echo __($empty); ?>
            </div>
        </div>
        <div class="button-container">
            <a href="<?= make_url('project_issues', ['project_key' => Context::getCurrentProject()->getKey()]); ?>" class="button secondary highlight">
                <?= fa_image_tag('search', ['class' => 'icon']); ?>
                <span class="name"><?= __('Find other issues'); ?></span>
            </a>
        </div>
    <?php endif; ?>
</div>