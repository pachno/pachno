<div class="dashboard_milestones">
<?php $milestone_cc = 0; ?>
<?php foreach ($upcoming_milestones as $milestone): ?>
    <?php if ($milestone->isScheduled()): ?>
        <?php include_component('main/milestonedashboardbox', array('milestone' => $milestone)); ?>
        <?php $milestone_cc++; ?>
    <?php endif; ?>
<?php endforeach; ?>
<?php foreach ($starting_milestones as $milestone): ?>
    <?php if ($milestone->isStarting()): ?>
        <?php include_component('main/milestonedashboardbox', array('milestone' => $milestone)); ?>
        <?php $milestone_cc++; ?>
    <?php endif; ?>
<?php endforeach; ?>
</div>
<?php if ($milestone_cc == 0): ?>
    <div class="onboarding medium">
        <div class="image-container">
            <?= image_tag('/unthemed/project-no-milestones.png', [], true); ?>
        </div>
        <div class="helper-text">
            <?= __("There are no milestones scheduled yet"); ?><br>
            <?= __('A great roadmap starts with a great plan'); ?>
        </div>
    </div>
<?php endif; ?>
