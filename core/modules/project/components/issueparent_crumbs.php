<?php

    use pachno\core\entities\Issue;

    /**
     * @var Issue $issue
     */

?>
<?php if ($p_issues = $issue->getParentIssues()): ?>
    <?php include_component('project/issueparent_crumbs', array('issue' => array_shift($p_issues), 'parent' => true)); ?>
<?php endif; ?>
<span class="crumb-item">
    <span class="issue-state <?php echo $issue->isClosed() ? 'closed' : 'open'; ?>"><?php echo $issue->isClosed() ? __('Closed') : __('Open'); ?></span>
    <?= fa_image_tag(($issue->hasIssueType()) ? $issue->getIssueType()->getFontAwesomeIcon() : 'unknown', ['class' => (($issue->hasIssueType()) ? 'issuetype-icon issuetype-' . $issue->getIssueType()->getIcon() : 'issuetype-icon issuetype-unknown')]); ?>
    <?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())), (isset($parent) && $parent) ? $issue->getFormattedTitle(true) : $issue->getFormattedIssueNo(true), ['class' => (isset($parent) && $parent) ? 'issue_number_link' : 'issue_number_link current']); ?>
    <?php if (isset($parent) && $parent) echo '&nbsp;&raquo;&nbsp;'; ?>
</span>
