<div class="backdrop_box login_popup" id="login_popup">
    <div class="backdrop_detail_header">
        <span><?= __('Log in or register'); ?></span>
        <?php if ($mandatory != true): ?>
            <a href="javascript:void(0);" onclick="$('login_backdrop').hide();" class="closer"><?= fa_image_tag('times'); ?></a>
        <?php endif; ?>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content rounded_top login_content">
        <?php echo $content; ?>
    </div>
</div>
<?php if (isset($options['error'])): ?>
    <script type="text/javascript">
        Pachno.Main.Helpers.Message.error('<?php echo $options['error']; ?>');
    </script>
<?php endif; ?>