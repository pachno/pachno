<div class="backdrop_box large" id="project_config_popup_main_container">
    <div class="backdrop_detail_header">
        <a class="closer" href="javascript:void(0);" onclick="Pachno.UI.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="form-container">
            <?php include_component('project/issue', ['issue' => $issue]); ?>
        </div>
    </div>
</div>
