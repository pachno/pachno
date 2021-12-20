<?php

    use pachno\core\entities\User;
    use pachno\core\framework;

    /**
     * @var framework\Response $pachno_response
     * @var User $pachno_user
     */

?>
<?php if (!$pachno_user->isGuest()): ?>
    <button class="button primary" onclick="Pachno.UI.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'agileboard', 'project_id' => $project->getID(), 'is_private' => 0)); ?>');"><?= fa_image_tag('plus-square'); ?><span><?= __('Create board'); ?></span></button>
<?php endif; ?>
