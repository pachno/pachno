<?php

    /**
     * @var \pachno\core\entities\Group $group
     */

?>
<div class="backdrop_box large edit_agileboard">
    <div class="backdrop_detail_header">
        <span><?php echo ($group->getId()) ? __('Edit group') : __('Create new group'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="Pachno.UI.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="form-container">
            <form action="<?php echo make_url('configure_group', ['group_id' => $group->getID(), 'mode' => 'edit']); ?>" id="edit_group_form" method="post" data-simple-submit data-auto-close>
                <div class="form-row">
                    <input type="text" id="group_<?php echo $group->getID(); ?>_name" name="name" value="<?php echo htmlentities($group->getName(), ENT_COMPAT, \pachno\core\framework\Context::getI18n()->getCharset()); ?>" class="name-input-enhance">
                    <label style for="group_<?php echo $group->getID(); ?>_name"><?php echo __('Group name'); ?></label>
                    <div class="helper-text"><?php echo __('Enter the name of the group, and select permissions inherited by users or teams assigned with this group from the list below'); ?></div>
                </div>
                <h3><?php echo __('Group permissions'); ?></h3>
                <div class="form-row">
                    <div class="list-mode">
                        <div class="list-item filter-container">
                            <input type="search" placeholder="<?= __('Filter available permissions'); ?>">
                        </div>
                        <div class="interactive_menu_values filter_existing_values">
                            <?php include_component('configuration/grouppermissionseditlist', ['target' => $group, 'permissions_list' => \pachno\core\framework\Context::getAvailablePermissions('user'), 'module' => 'core', 'target_id' => null]); ?>
                            <?php include_component('configuration/grouppermissionseditlist', ['target' => $group, 'permissions_list' => \pachno\core\framework\Context::getAvailablePermissions('pages'), 'module' => 'core', 'target_id' => null]); ?>
                            <?php include_component('configuration/grouppermissionseditlist', ['target' => $group, 'permissions_list' => \pachno\core\framework\Context::getAvailablePermissions('configuration'), 'module' => 'core', 'target_id' => null, 'is_configuration' => true]); ?>
                            <?php \pachno\core\framework\Event::createNew('core', 'grouppermissionsedit', $group)->trigger(); ?>
                        </div>
                    </div>
                </div>
                <div class="form-row submit-container">
                    <button type="submit" class="button primary">
                        <?php echo fa_image_tag('spinner', ['class' => 'indicator fa-spin icon']); ?>
                        <span><?php echo __('Save group'); ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
