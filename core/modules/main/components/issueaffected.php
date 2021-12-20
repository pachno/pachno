<div class="configurable-components-list affected-list" id="affected_list">
    <?php if ($issue->getProject()->isEditionsEnabled()): ?>
        <?php foreach ($editions as $edition): ?>
            <?php include_component('main/affecteditem', array('item' => $edition, 'itemtype' => 'edition', 'itemtypename' => __('Edition'), 'issue' => $issue, 'statuses' => $statuses)); ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if ($issue->getProject()->isComponentsEnabled()): ?>
        <?php foreach ($components as $component): ?>
            <?php include_component('main/affecteditem', array('item' => $component, 'itemtype' => 'component', 'itemtypename' => __('Component'), 'issue' => $issue, 'statuses' => $statuses)); ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if ($issue->getProject()->isBuildsEnabled()): ?>
        <?php foreach ($builds as $build): ?>
            <?php include_component('main/affecteditem', array('item' => $build, 'itemtype' => 'build', 'itemtypename' => __('Release'), 'issue' => $issue, 'statuses' => $statuses)); ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
