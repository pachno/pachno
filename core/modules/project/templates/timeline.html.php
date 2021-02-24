<?php

    $pachno_response->setTitle(__('"%project_name" project timeline', array('%project_name' => $selected_project->getName())));
    $pachno_response->addFeed(make_url('project_timeline', array('project_key' => $selected_project->getKey(), 'format' => 'rss')), __('"%project_name" project timeline', array('%project_name' => $selected_project->getName())));

?>
<div class="content-with-sidebar">
    <?php include_component('project/sidebar', ['dashboard' => __('Timeline')]); ?>
    <?php /* <div class="project_left">
            <input id="timeline_offset" value="40" type="hidden">
            <h3><?php echo __('Timeline actions'); ?></h3>
            <ul class="simple-list">
                <li class="<?php if ($important) echo 'selected'; ?>"><?php echo link_tag(make_url('project_timeline_important', array('project_key' => $selected_project->getKey())), image_tag('icon_important.png') . __('Only important items')); ?></li>
                <li class="<?php if (!$important) echo 'selected'; ?>"><?php echo link_tag(make_url('project_timeline', array('project_key' => $selected_project->getKey())), image_tag('icon_timeline.png') . __('All timeline items')); ?></li>
            </ul>
            <ul class="simple-list">
                <li><?php echo link_tag(make_url('project_timeline_important', array('project_key' => $selected_project->getKey(), 'format' => 'rss')), fa_image_tag('rss-square', ['class' => 'rss-icon']) . __('Only important items')); ?></li>
                <li><?php echo link_tag(make_url('project_timeline', array('project_key' => $selected_project->getKey(), 'format' => 'rss')), fa_image_tag('rss-square', ['class' => 'rss-icon']) . __('All timeline items')); ?></li>
            </ul>
    </div> */ ?>
    <div class="project-timeline">
        <div style="width: 790px;" id="timeline">
            <?php if (count($recent_activities) > 0): ?>
                <?php include_component('project/timeline', array('activities' => $recent_activities)); ?>
            <?php else: ?>
                <div class="onboarding medium">
                    <div class="image-container">
                        <?= image_tag('/unthemed/onboarding-recent-activities.png', [], true); ?>
                    </div>
                    <div class="helper-text">
                        <span class="title"><?php echo __('No recent activity registered for this project.'); ?></span>
                        <span><?php echo __('As soon as something important happens it will appear here.'); ?></span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php if (count($recent_activities) > 0): ?>
            <div class="project_timeline_more_button_container">
                <?php echo image_tag('spinning_32.gif', array('id' => 'timeline_indicator', 'style' => 'display: none;')); ?>
                <?php echo javascript_link_tag(__('Show more'), array('class' => 'button', 'onclick' => "Pachno.Project.Timeline.update('".make_url(($important) ? 'project_timeline_important' : 'project_timeline', array('project_key' => $selected_project->getKey()))."');", 'id' => 'timeline_more_link')); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
