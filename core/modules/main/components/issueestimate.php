<div class="backdrop_box large" id="viewissue_add_item_div">
    <div class="backdrop_detail_header">
        <span><?= __('Issue time tracking - estimation'); ?></span>
        <button class="closer"><?= fa_image_tag('times'); ?></button>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <?php include_component('main/issueestimator', array('issue' => $issue, 'field' => 'estimated_time', 'mode' => 'left')); ?>
    </div>
</div>
