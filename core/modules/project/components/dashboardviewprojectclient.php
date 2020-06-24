<div id="project_client">
    <?php if ($client instanceof \pachno\core\entities\Client): ?>
        <div class="project_client_info">
            <?php include_component('project/clientinfo', array('client' => $client)); ?>
        </div>
    <?php else: ?>
        <div class="faded_out" style="font-weight: normal;"><?php echo __('No client assigned'); ?></div>
    <?php endif; ?>
</div>
