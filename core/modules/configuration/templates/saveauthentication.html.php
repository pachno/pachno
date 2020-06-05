<?php

    $pachno_response->setTitle(__('Configure authentication'));
    
?>
<div class="message-box type-info">
    <span class="icon">

    </span>
    <span class="message">
        <span class="title"><?php echo __('Settings saved'); ?></span>
        <span><?php echo __('To apply changes to the authentication system, you have been automatically logged out. The new authentication system is now in use.'); ?></span>
    </span>
    <span class="actions">
        <?php echo link_tag(make_url('home'), '<span>'.__('Continue').'</span>'.fa_image_tag('chevron-right', ['class' => 'icon']), ['class' => 'button primary']); ?>
    </span>
</div>
