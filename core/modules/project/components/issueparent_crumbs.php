<?php

    use pachno\core\entities\Issue;

    /**
     * @var Issue $issue
     */

    $backdrop = $backdrop ?? false;

?>
<?php if ($p_issues = $issue->getParentIssues()): ?>
    <?php include_component('project/issueparent_crumbs', ['issue' => array_shift($p_issues), 'parent' => true, 'backdrop' => $backdrop]); ?>
<?php endif; ?>
<?php if (isset($parent) && $parent): ?>
    <span class="crumb-item">
        <?= fa_image_tag(($issue->hasIssueType()) ? $issue->getIssueType()->getFontAwesomeIcon() : 'unknown', ['class' => (($issue->hasIssueType()) ? 'issuetype-icon issuetype-' . $issue->getIssueType()->getIcon() : 'issuetype-icon issuetype-unknown')]); ?>
        <?php if ($backdrop): ?>
            <a href="javascript:void(0);" class="trigger-backdrop issue_number_link" data-url="<?php echo $issue->getCardUrl(); ?>"><?= $issue->getFormattedTitle(true); ?></a>
        <?php else: ?>
            <?php echo link_tag($issue->getUrl(), $issue->getFormattedTitle(true), ['class' => 'issue_number_link']); ?>
        <?php endif; ?>
        <?php if (isset($parent) && $parent) echo '&nbsp;&raquo;&nbsp;'; ?>
    </span>
<?php endif; ?>
