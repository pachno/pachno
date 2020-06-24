<?php use pachno\core\entities\AgileBoard; ?>
<a href="<?php echo make_url('agile_whiteboard', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>" class="board" id="agileboard_<?php echo $board->getID(); ?>">
    <div class="image-container"><?= image_tag('/unthemed/mono/icon-board.png', [], true); ?></div>
    <div class="details">
        <div class="name"><?php echo $board->getName(); ?></div>
        <div class="description"><?php echo $board->getDescription(); ?></div>
    </div>
    <div class="actions-container">
        <button class="button secondary icon trigger-backdrop" data-url="<?php echo make_url('get_partial_for_backdrop', ['key' => 'agileboard', 'project_id' => $board->getProject()->getID(), 'board_id' => $board->getID()]); ?>" data-docked-backdrop="right" title="<?php echo __('Edit this board'); ?>"><?php echo fa_image_tag('cog'); ?></button>
        <button class="button secondary icon" onclick="Pachno.UI.Dialog.show('<?php echo __('Delete this board?'); ?>', '<?php echo __('Do you really want to delete this board?').'<br>'.__('Deleting this will make it unavailable. No issues or saved searches will be affected by this action.'); ?>', {yes: {click: function() {Pachno.Project.Planning.removeAgileBoard('<?php echo make_url('agile_board', array('board_id' => $board->getID(), 'project_key' => $board->getProject()->getKey())); ?>');}}, no: {click: Pachno.UI.Dialog.dismiss}});return false;" title="<?php echo __('Delete this board'); ?>"><?php echo fa_image_tag('times', ['class' => 'delete']); ?></button>
    </div>
</a>
