<?php $canedititem = (($itemtype == 'build' && $issue->canEditAffectedBuilds()) || ($itemtype == 'component' && $issue->canEditAffectedComponents()) || ($itemtype == 'edition' && $issue->canEditAffectedEditions())); ?>
<div id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>" class="configurable-component">
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
            <div class="status-row dropper-container">
                <span class="status-badge dropper affected_status" id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>_status" style="background-color: <?php echo ($item['status'] instanceof \pachno\core\entities\Status) ? $item['status']->getColor() : '#FFF'; ?>;" title="<?php echo ($item['status'] instanceof \pachno\core\entities\Datatype) ? __($item['status']->getName()) : __('Unknown'); ?>"><?php echo ($item['status'] instanceof \pachno\core\entities\Datatype) ? $item['status']->getName() : __('Unknown'); ?></span>
                <div class="dropdown-container" id="affected_<?php echo $itemtype; ?>_<?php echo $item['a_id']; ?>_status_change">
                    <div class="list-mode">
                        <?php foreach ($statuses as $status): ?>
                            <?php if (!$status->canUserSet($pachno_user)) continue; ?>
                            <a href="javascript:void(0);" class="list-item" onclick="Pachno.Issues.Affected.setStatus('<?php echo make_url('status_affected', array('issue_id' => $issue->getID(), 'affected_type' => $itemtype, 'affected_id' => $item['a_id'], 'status_id' => $status->getID())); ?>', '<?php echo $itemtype.'_'.$item['a_id']; ?>');">
                                <span class="status-badge" style="background-color: <?php echo $status->getColor(); ?>;color: <?php echo $status->getTextColor(); ?>;">
                                    <span><?php echo __($status->getName()); ?></span>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($canedititem): ?>
            <a href="javascript:void(0);" class="icon" onclick="Pachno.UI.Dialog.show('<?php echo __('Remove %itemname?', array('%itemname' => $item[$itemtype]->getName())); ?>', '<?php echo __('Please confirm that you want to remove this item from the list of items affected by this issue'); ?>', {yes: {click: function() {Pachno.Issues.Affected.remove('<?php echo make_url('remove_affected', array('issue_id' => $issue->getID(), 'affected_type' => $itemtype, 'affected_id' => $item['a_id'])).'\', '.'\''.$itemtype.'_'.$item['a_id']; ?>');Pachno.UI.Dialog.dismiss();}}, no: {click: Pachno.UI.Dialog.dismiss}});"><?php echo fa_image_tag('times', array('id' => 'affected_'.$itemtype.'_'.$item['a_id'].'_delete_icon', 'class' => 'delete')); ?></a>
        <?php endif; ?>
    </div>
</div>
