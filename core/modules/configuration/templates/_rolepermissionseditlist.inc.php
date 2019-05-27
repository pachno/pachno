<?php if (count($permissions_list) > 0): ?>
    <?php foreach ($permissions_list as $permission_key => $permission): ?>
        <?php $is_checked = $role->hasPermission($permission_key, $module, $target_id); ?>
        <div class="list-item <?php if (isset($disabled) && $disabled) echo ' disabled'; ?> <?php if (array_key_exists('details', $permission) && is_array($permission['details']) && !empty($permission['details'])) echo ' expandable'; ?>">
            <?php if (!array_key_exists('container', $permission) || !$permission['container']): ?>
                <input <?php if (isset($disabled) && $disabled) echo 'disabled checked'; ?> type="checkbox" class="fancycheckbox" name="permissions[]" id="role_<?php echo $role->getID(); ?>_permission_<?php echo $permission_key; ?>_checkbox" value="<?php echo $module; ?>,<?php echo $target_id; ?>,<?php echo $permission_key; ?>"<?php if ($is_checked) echo ' checked'; ?><?php if (array_key_exists('details', $permission) && is_array($permission['details']) && !empty($permission['details'])): ?> onchange="var chk = $(this).checked; $('role_<?php echo $role->getID(); ?>_permission_<?php echo $permission_key; ?>_sublist').select('input[type=checkbox]').each( function (elm) { if (chk) { $(elm).disable(); $(elm).checked = true; $(elm).up('.list-item').addClassName('disabled'); } else { $(elm).enable(); $(elm).checked = false; $(elm).up('.list-item').removeClassName('disabled'); } });"<?php endif; ?>>
                <label for="role_<?php echo $role->getID(); ?>_permission_<?php echo $permission_key; ?>_checkbox" class="name">
                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                    <span><?php echo (array_key_exists('description', $permission)) ? $permission['description'] : $permission_key; ?></span>
                </label>
            <?php endif; ?>
            <?php if (array_key_exists('details', $permission) && is_array($permission['details']) && !empty($permission['details'])): ?>
                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
            <?php endif; ?>
        </div>
        <?php if (array_key_exists('details', $permission) && is_array($permission['details']) && !empty($permission['details'])): ?>
            <div id="role_<?php echo $role->getID(); ?>_permission_<?php echo $permission_key; ?>_sublist" class="submenu">
                <?php include_component('configuration/rolepermissionseditlist', array('permissions_list' => $permission['details'], 'role' => $role, 'disabled' => (isset($disabled)) ? $disabled : $is_checked, 'module' => $module, 'target_id' => $target_id)); ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
