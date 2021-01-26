<?php

    use pachno\core\framework;
    use pachno\core\entities;

    /**
     * @var framework\Response $pachno_response
     * @var entities\Project $selected_project
     */

    $pachno_response->setTitle(__('"%project_name" roadmap', array('%project_name' => $selected_project->getName())));

?>
<div id="project_roadmap_page" class="content-with-sidebar">
    <?php include_component('project/sidebar', ['dashboard' => __('Roadmap')]); ?>
    <?php /* <h3><?php echo __('Roadmap filters'); ?></h3>
            <ul class="simple-list">
                <li class="<?php if ($mode == 'upcoming') echo 'selected'; ?>"><a href="javascript:void(0);" onclick="Pachno.Project.clearRoadmapFilters(); $('#project_roadmap_page').addClass('upcoming');Pachno.Project.toggleLeftSelection(this);Pachno.Project.showRoadmap();"><?php echo __('Upcoming roadmap'); ?></a></li>
                <li class="<?php if ($mode == 'all') echo 'selected'; ?>"><a href="javascript:void(0);" onclick="Pachno.Project.clearRoadmapFilters(); Pachno.Project.toggleLeftSelection(this);Pachno.Project.showRoadmap();"><?php echo __('Include past milestones'); ?></a></li>
                <li><h3><?php echo __('Milestone details'); ?></h3></li>
                <?php foreach ($milestones as $milestone): ?>
                    <li id="roadmap_milestone_<?php echo $milestone->getID(); ?>_details_link" class="milestone_details_link <?php if ($milestone->isReached()) echo 'closed'; ?> <?php if ($mode == 'milestone' && isset($selected_milestone) && $selected_milestone instanceof \pachno\core\entities\Milestone && $selected_milestone->getID() == $milestone->getID()) echo 'selected'; ?>"><a href="javascript:void(0);" onclick="Pachno.Project.showMilestoneDetails('<?php echo make_url('project_roadmap_milestone_details', array('project_key' => $selected_project->getKey(), 'milestone_id' => $milestone->getID())); ?>', <?php echo $milestone->getID(); ?>, true); Pachno.Project.toggleLeftSelection(this);"><?php echo $milestone->getName(); ?></a></li>
                <?php endforeach; ?>
            </ul> */ ?>
    <div id="project_planning">
        <div id="roadmap-header" class="top-search-filters-container">
            <div class="header">
                <div class="name-container">
                    <span class="board-name"><?= __('Roadmap'); ?></span>
                </div>
                <div class="stripe-container">
                    <div class="stripe"></div>
                </div>
                <div class="fancy-tabs" style="display: none;">
                    <span class="tab selected">
                        <span class="icon"><?= fa_image_tag('columns'); ?></span>
                        <span class="name"><?= __('Column view'); ?></span>
                    </span>
                </div>
            </div>
            <div class="search-and-filters-strip">
                <div class="search-strip">
                    <div class="fancy-dropdown-container from-left"">
                        <div class="fancy-dropdown data-default-label="<?= __('Show all'); ?>">
                            <label><?= __('Filter'); ?></label>
                            <span class="value"><?php echo __('Show all'); ?></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container list-mode filter-values-container">
                                <input type="radio" value="all" class="fancy-checkbox" name="milestone_type" id="filter_milestone_type_all">
                                <label for="filter_milestone_type_all" class="list-item">
                                    <?= fa_image_tag('list', ['class' => 'icon']); ?>
                                    <span class="name value"><?= __('Show all milestones') ?></span>
                                </label>
                                <div class="list-item separator"></div>
                                <input type="radio" value="sprint" class="fancy-checkbox" name="milestone_type" id="filter_milestone_type_sprint">
                                <label for="filter_milestone_type_sprint" class="list-item">
                                    <?= fa_image_tag('undo', ['class' => 'icon rotate-90']); ?>
                                    <span class="name value"><?= __('Show only sprints') ?></span>
                                </label>
                                <input type="radio" value="regular" class="fancy-checkbox" name="milestone_type" id="filter_milestone_type_regular" checked>
                                <label for="filter_milestone_type_regular" class="list-item">
                                    <?= fa_image_tag('tasks', ['class' => 'icon']); ?>
                                    <span class="name value"><?= __('Only regular milestones') ?></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="fancy-dropdown-container from-left"">
                        <div class="fancy-dropdown data-default-label="<?= __('Only active'); ?>">
                            <label><?= __('State'); ?></label>
                            <span class="value"><?php echo __('Only active'); ?></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container list-mode filter-values-container">
                                <input type="radio" value="all" class="fancy-checkbox" name="milestone_state" id="filter_milestone_state_all">
                                <label for="filter_milestone_state_all" class="list-item">
                                    <?= fa_image_tag('list', ['class' => 'icon']); ?>
                                    <span class="name value"><?= __('Show all') ?></span>
                                </label>
                                <div class="list-item separator"></div>
                                <input type="radio" value="sprint" class="fancy-checkbox" name="milestone_state" id="filter_milestone_state_active" checked>
                                <label for="filter_milestone_state_active" class="list-item">
                                    <?= fa_image_tag('undo', ['class' => 'icon rotate-90']); ?>
                                    <span class="name value"><?= __('Only active') ?></span>
                                </label>
                                <input type="radio" value="regular" class="fancy-checkbox" name="milestone_state" id="filter_milestone_state_closed">
                                <label for="filter_milestone_state_closed" class="list-item">
                                    <?= fa_image_tag('tasks', ['class' => 'icon']); ?>
                                    <span class="name value"><?= __('Only closed') ?></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="project_roadmap" class="loading">
            <div id="milestone-cards-container" class="milestone-cards-container">
            </div>
            <div class="indicator"><?= fa_image_tag('spinner', ['class' => 'fa-spin']); ?></div>
            <div id="onboarding-no-milestones" style="display: none;">
                <?= image_tag('/unthemed/navigation/turn.png', ['id' => 'indicate-button'], true); ?>
                <div class="onboarding large">
                    <div class="image-container">
                        <?= image_tag('/unthemed/no-roadmap.png', [], true); ?>
                    </div>
                    <div class="helper-text">
                        <?= __('Plan ahead with confidence'); ?><br>
                        <?= __('Track milestones and their progress'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    Pachno.on(Pachno.EVENTS.ready, function () {
        let roadmap;

        roadmap = new Roadmap({
            milestones_url: '<?= make_url('project_milestones', ['project_key' => $selected_project->getKey()]); ?>',
            sort_url: '<?php echo make_url('project_sort_milestones', ['project_key' => $selected_project->getKey()]); ?>'
        });
        window.currentRoadmap = roadmap;
    });
</script>
