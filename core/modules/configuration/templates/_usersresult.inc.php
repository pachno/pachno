<?php if (isset($too_short)): ?>
    <div style="padding: 3px; margin-top: 5px; font-weight: normal; font-size: 14px;" class="faded_out">
        <?php echo __('Please enter something to search for'); ?>
    </div>
<?php else: ?>
    <div style="padding: 3px; margin-top: 5px; font-weight: normal; font-size: 14px;" class="faded_out">
        <?php if (isset($title)): ?>
            <?php echo $title; ?>
        <?php else: ?>
            <?php echo __('%count users found when searching for "%searchstring"', array('%count' => "<span class=\"find_users_num_results\">{$total_results}</span>", '%searchstring' => $findstring)); ?>
        <?php endif ?>
    </div>
    <?php if ($total_results > 0): ?>
        <div class="flexible-table">
            <div class="row header">
                <div class="column header name-container"><?php echo __('Username'); ?></div>
                <div class="column header"><?php echo __('E-mail'); ?></div>
                <div class="column header info-icons"><?php echo __('Active'); ?></div>
                <div class="column header actions"></div>
            </div>
            <div class="body">
                <?php foreach ($users as $user): ?>
                    <?php include_component('finduser_row', array('user' => $user)); ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php if (isset($more_available)): ?>
    <script type="text/javascript">
        <?php if (\pachno\core\framework\Context::getScope()->getMaxUsers()): ?>
            $('#current_user_num_count').update(<?php echo $total_count; ?>);
        <?php endif; ?>
    </script>
<?php endif; ?>
