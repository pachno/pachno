<?php

    /** @var \pachno\core\entities\User[] $friends */

?>
<div class="header"><?php echo __('Friends'); ?></div>
<?php if (count($friends) > 0): ?>
    <?php foreach ($friends as $friend): ?>
        <div class="list-item">
            <div class="name"><?php include_component('main/userdropdown', array('user' => $friend)); ?></div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="onboarding">
        <div class="image-container">
            <?= image_tag('/unthemed/onboarding_friends.png', [], true); ?>
        </div>
        <div class="helper-text">
            <?= __('Got friends?'); ?><br>
            <?= __('Click their names and add them as friends in Pachno'); ?>
        </div>
    </div>
<?php endif; ?>
