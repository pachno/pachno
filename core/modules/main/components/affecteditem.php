<?php $canedititem = (($itemtype == 'build' && $issue->canEditAffectedBuilds()) || ($itemtype == 'component' && $issue->canEditAffectedComponents()) || ($itemtype == 'edition' && $issue->canEditAffectedEditions())); ?>
<div id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>" class="configurable-component" data-affected-item data-affected-item-id="<?= $item['a_id']; ?>">
    <div class="row">
        <?php if ($itemtype == 'component'): ?>
            <?php echo fa_image_tag('puzzle-piece', ['title' => $itemtypename, 'class' => 'icon']); ?>
        <?php elseif ($itemtype == 'edition'): ?>
            <?php echo fa_image_tag('window-restore', ['title' => $itemtypename, 'class' => 'icon'], 'far'); ?>
        <?php else: ?>
            <?php echo fa_image_tag('compact-disc', ['title' => $itemtypename, 'class' => 'icon']); ?>
        <?php endif; ?>
        <div class="name">
            <div class="title">
                <?php echo $item[$itemtype]->getName(); ?>
                <?php if ($itemtype == 'build'): ?>
                    <span class="faded_out">(<?php echo $item['build']->getVersionMajor().'.'.$item['build']->getVersionMinor().'.'.$item['build']->getVersionRevision(); ?>)</span>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($canedititem): ?>
            <a class="button secondary icon danger" href="javascript:void(0);" onclick="Pachno.UI.Dialog.show('<?= __('Remove %itemname?', array('%itemname' => $item[$itemtype]->getName())); ?>', '<?= __('Please confirm that you want to remove this item from the list of items affected by this issue'); ?>', {yes: {click: function() {Pachno.trigger(Pachno.EVENTS.issue.removeAffectedItem, { url: '<?= make_url('remove_affected', ['issue_id' => $issue->getID(), 'affected_type' => $itemtype, 'affected_id' => $item['a_id']]); ?>', id: <?= $item['a_id']; ?>, issue_id: <?= $issue->getID(); ?> })}}, no: { click: Pachno.UI.Dialog.dismiss }});">
                <span class="icon"><?= fa_image_tag('times'); ?></span>
            </a>
        <?php endif; ?>
    </div>
</div>
