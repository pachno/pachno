<?php

    use pachno\core\entities\Issue;

    /**
     * @var Issue $issue
     * @var Issue[][] $grouped_issues
     */

?>
<?php if ($grouped_issues): ?>
    <form id="viewissue_relate_issues_form" action="<?= make_url('viewissue_relate_issues', ['project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID()]); ?>" method="post" accept-charset="<?= \pachno\core\framework\Settings::getCharset(); ?>" data-simple-submit data-auto-close data-update-issues data-update-container="#related_child_issues_inline" data-update-insert>
        <div class="flexible-table">
            <div class="row header">
                <div class="column header info-icons"></div>
                <div class="column header name-container"><?= __('Issue number and details'); ?></div>
            </div>
            <?php foreach($grouped_issues as $project => $matched_issues): ?>
                <?php foreach($matched_issues as $matched_issue): ?>
                    <div class="row">
                        <input type="checkbox" value="<?= $matched_issue->getID(); ?>" class="fancy-checkbox" name="relate_issues[<?= $matched_issue->getID(); ?>]" id="relate_issue_<?= $matched_issue->getID(); ?>">
                        <label class="column name-container" for="relate_issue_<?= $matched_issue->getID(); ?>">
                            <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                            <?= fa_image_tag($matched_issue->getIssueType()->getFontAwesomeIcon(), ['class' => 'issuetype-icon issuetype-' . $matched_issue->getIssueType()->getType()]); ?>
                            <span><?= $matched_issue->getFormattedTitle(true); ?></span>
                        </label>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
        <div class="form-row">
            <div class="fancy-label-select">
                <input class="fancy-checkbox" type="radio" id="relate_issue_with_selected_children" name="relate_action" checked="checked" value="relate_children">
                <label for="relate_issue_with_selected_children">
                    <?= fa_image_tag('check', ['class' => 'checked']) . __('Add checked issues as children'); ?>
                </label>
                <input class="fancy-checkbox" type="radio" id="relate_issue_with_selected_parent" name="relate_action" value="relate_parent">
                <label for="relate_issue_with_selected_parent">
                    <?= fa_image_tag('check', ['class' => 'checked']) . __('Set selected issue as parent'); ?>
                </label>
            </div>
        </div>
        <div class="form-row submit-container">
            <button type="submit" class="button primary">
                <?= fa_image_tag('spinner', ['class' => 'indicator fa-spin icon']); ?>
                <span><?= __('Add relation'); ?></span>
            </button>
        </div>
    </form>
<?php else: ?>
    <div class="backdrop_detail_content">
        <span class="faded_out"><?= __('No issues matched your search. Please try again with different search terms.'); ?></span>
    </div>
<?php endif; ?>
