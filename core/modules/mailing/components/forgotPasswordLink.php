<a href="javascript:void(0);" class="button secondary trigger-show-forgot-password">
    <span><?php echo __('Forgot password'); ?></span>
</a>
<script>
    $('body').on('click', '.trigger-show-forgot-password', function () {
        $('#login-popup').addClass('hidden');
        $('#forgot_password_container').removeClass('hidden');
    });
    $('body').on('click', '.trigger-show-login', function () {
        $('#forgot_password_container').addClass('hidden');
    })
</script>