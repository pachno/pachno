<?php

    use pachno\core\entities\Group;
    use pachno\core\entities\Team;
    use pachno\core\entities\Client;

    /**
     * @var Group|Team|Client $target
     */

?>
<?php if (count($permissions_list) > 0): ?>
    <?php foreach ($permissions_list as $permission_key => $permission): ?>
        <?php $is_checked = $target->hasPermission($permission_key, $module); ?>
        <div class="list-item <?php if (isset($disabled) && $disabled) echo ' disabled'; ?> <?php if (array_key_exists('details', $permission) && is_array($permission['details']) && !empty($permission['details'])) echo ' expandable'; ?>">
            <?php if (!array_key_exists('container', $permission) || !$permission['container']): ?>
                <input <?php if (isset($disabled) && $disabled) echo 'disabled checked'; ?> type="checkbox" class="fancy-checkbox" name="permissions[]" id="group_<?php echo $target->getID(); ?>_permission_<?php echo $permission_key; ?>_checkbox" value="<?php echo $module; ?>,<?php echo $permission_key; ?>"<?php if ($is_checked) echo ' checked'; ?><?php if (array_key_exists('details', $permission) && is_array($permission['details']) && !empty($permission['details'])): ?> onchange="var chk = $(this).checked; $('#group_<?php echo $target->getID(); ?>_permission_<?php echo $permission_key; ?>_sublist').select('input[type=checkbox]').each( function (elm) { if (chk) { $(elm).disable(); $(elm).checked = true; $(elm).up('.list-item').addClass('disabled'); } else { $(elm).enable(); $(elm).checked = false; $(elm).up('.list-item').removeClass('disabled'); } });"<?php endif; ?>>
                <label for="group_<?php echo $target->getID(); ?>_permission_<?php echo $permission_key; ?>_checkbox" class="name">
                    <?= fa_image_tag('toggle-on', ['class' => 'checked']) . fa_image_tag('toggle-off', ['class' => 'unchecked']); ?>
                    <span><?php echo (array_key_exists('description', $permission)) ? $permission['description'] : $permission_key; ?></span>
                </label>
            <?php endif; ?>
            <?php if (array_key_exists('details', $permission) && is_array($permission['details']) && !empty($permission['details'])): ?>
                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
            <?php endif; ?>
        </div>
        <?php if (array_key_exists('details', $permission) && is_array($permission['details']) && !empty($permission['details'])): ?>
            <div id="group_<?php echo $target->getID(); ?>_permission_<?php echo $permission_key; ?>_sublist" class="submenu">
                <?php include_component('configuration/grouppermissionseditlist', ['permissions_list' => $permission['details'], 'target' => $target, 'disabled' => (isset($disabled)) ? $disabled : $is_checked, 'module' => $module]); ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
