<?php
/** @var \pachno\core\entities\Issue $issue
 *  @var \pachno\core\framework\Request $pachno_request
 *  @var \pachno\core\framework\Routing $pachno_routing
 */
?>
<a href="<?= make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())); ?>" title="<?= $issue->getFormattedTitle(true); ?>" class="list-item multiline <?php if ($pachno_routing->getCurrentRoute()->getName() == 'viewissue' && $pachno_request->getParameter('issue_no') == $issue->getFormattedIssueNo(true, false)) echo 'selected'; ?>">
    <span class="icon"><?php if ($issue->hasIssueType()) echo fa_image_tag($issue->getIssueType()->getFontAwesomeIcon(), ['class' => (($issue->hasIssueType()) ? 'issuetype-icon issuetype-' . $issue->getIssueType()->getIcon() : 'issuetype-icon issuetype-unknown')]); ?></span>
    <span class="name">
        <span class="title"><?php echo $issue->getFormattedTitle(true, false); ?></span>
        <span class="description">
            <span class="status-badge" style="background-color: <?php echo ($issue->getStatus() instanceof \pachno\core\entities\Datatype) ? $issue->getStatus()->getColor() : '#FFF'; ?>;color: <?php echo ($issue->getStatus() instanceof \pachno\core\entities\Datatype) ? $issue->getStatus()->getTextColor() : '#333'; ?>;"><span><?php echo ($issue->getStatus() instanceof \pachno\core\entities\Datatype) ? __($issue->getStatus()->getName()) : __('Status not set'); ?></span></span>
            <?php if ($issue->getPriority() instanceof \pachno\core\entities\Priority): ?>
                <span class="priority priority_<?= $issue->getPriority()->getValue(); ?>"><?= fa_image_tag($issue->getPriority()->getFontAwesomeIcon(), [], $issue->getPriority()->getFontAwesomeIconStyle()) . $issue->getPriority()->getName(); ?></span>
            <?php endif; ?>
        </span>
    </span>
</a>
