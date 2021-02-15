<div class="backdrop_box <?php echo isset($medium_backdrop) && $medium_backdrop == 1 ? 'medium' : 'large'; ?>" id="reportissue_container">
    <div class="backdrop_detail_header">
        <span><?php echo $title; ?></span>
        <a href="javascript:void(0);" class="closer" onclick="Pachno.UI.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content with-sidebar sidebar-right">
        <?php include_component('main/reportissue', compact('selected_project', 'issuetypes', 'board', 'selected_statuses', 'selected_issuetype', 'selected_milestone', 'selected_build', 'parent_issue', 'errors')); ?>
    </div>
</div>
<script>
    new IssueReporter(<?php if (isset($selected_issuetype) && $selected_issuetype instanceof \pachno\core\entities\Issuetype) echo 'true'; ?>);
</script>
