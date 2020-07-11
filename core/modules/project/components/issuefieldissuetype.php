<div id="issuetype-field" class="issuetype-field dropper-container">
    <div id="issuetype_content" class="<?php if ($issue->isEditable() && $issue->canEditIssuetype()) echo 'dropper'; ?> issuetype-icon issuetype-<?= ($issue->hasIssueType()) ? $issue->getIssueType()->getIcon() : 'unknown'; ?>">
        <?php if ($issue->hasIssueType()) echo fa_image_tag($issue->getIssueType()->getFontAwesomeIcon(), ['class' => 'icon']); ?>
        <span class="name"><?= __($issue->getIssueType()->getName()); ?></span>
    </div>
    <?php if ($issue->isEditable() && $issue->canEditIssuetype()): ?>
        <div id="issuetype_change" class="dropdown-container from-right">
            <ul class="list-mode">
                <li class="header"><?php echo __('Change issue type'); ?></li>
                <?php foreach ($issue->getProject()->getIssuetypeScheme()->getIssuetypes() as $issuetype): ?>
                    <li class="list-item">
                        <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'issuetype', 'issuetype_id' => $issuetype->getID())); ?>', 'issuetype');">
                            <?php echo fa_image_tag($issuetype->getFontAwesomeIcon(), ['class' => 'issuetype-icon issuetype-' . $issuetype->getIcon()]); ?>
                            <span class="name"><?php echo __($issuetype->getName()); ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
