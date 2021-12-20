<div class="backdrop_box medium" id="viewissue_add_relation_div">
    <div class="backdrop_detail_header">
        <span><?php echo __('Find related issues'); ?></span>
        <a href="javascript:void(0);" class="closer"><?php echo fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="form-container">
            <form id="viewissue_find_issue_form" action="<?php echo make_url('viewissue_find_related_issues', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())); ?>" method="post" accept-charset="<?php echo \pachno\core\framework\Settings::getCharset(); ?>" data-simple-submit data-update-container="#viewissue_relation_results">
                <div class="form-row">
                    <div class="helper-text"><?php echo __('Please enter some details to search for, and then select the matching issues to relate them'); ?></div>
                </div>
                <div class="form-row unified">
                    <label for="viewissue_find_issue_input"><?php echo __('Find issue(s)'); ?>&nbsp;</label>
                    <input type="text" name="searchfor" id="viewissue_find_issue_input">
                    <button type="submit" class="button secondary highlight">
                        <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                        <span><?php echo __('Find'); ?></span>
                        <?= fa_image_tag('search', ['class' => 'icon']); ?>
                    </button>
                </div>
            </form>
        </div>
        <div id="viewissue_relation_results" class="form-container"></div>
    </div>
</div>
