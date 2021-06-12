<?php include_component('installation/header'); ?>
<?php if (isset($error)): ?>
    <div class="installation_box">
        <div class="error"><?php echo nl2br($error); ?></div>
        <h2>An error occured</h2>
        An error occured and the installation has been stopped. Please try to fix the error based on the information above, then click back, and try again.<br>
        If you think this is a bug, please report it in our <a href="https://projects.pach.no" target="_new">online bug tracker</a>.
    </div>
<?php else: ?>
    <div class="installation_box">
        <h1>Thank you for installing Pachno!</h1>
        Pachno is open source software. If you find any bugs or issues, please use our <a href="https://pachno.com" target="_new">issue tracker</a> or send an email to <a href="mailto:support@pachno.com">support@pachno.com</a>.<br>
        <br>
        Pachno is written using a flexible, module-based architecture, that lets you easily add extra functionality. Even core functionality such as version control integration, email communication and the agile sections are provided using modules, and can be enabled / disabled from the configuration panel.<br>
        <br>
        <div class="feature">
            Online documentation is available from <a href="https://pachno.com/support" target="_new">pachno.com &raquo; support</a>, and our <a href="https://forum.pachno.com" target="_new">community forums</a> are full of helpful people.<br>
            We also provide <a target="_new" href="https://pachno.com/register/support">commercial support</a> and <a target="_new" href="https://pachno.com/training">online training</a> for individuals and groups. For other inquiries, send an email to <a href="mailto:support@pachno.com">support@pachno.com</a>.<br>
            Find additional modules online, at <a href="https://pachno.com/addons">pachno.com &raquo; Addons</a><br>
        </div>
        <br>
        <h2>Getting involved</h2>
        If you want to get involved with Pachno, don't hesitate to visit our community website <a target="_new" href="http://pachno.com/community">pachno.com/community</a> to see how you can join our growing community.
    </div>
    <form accept-charset="utf-8" action="<?php echo make_url('auth_login'); ?>" method="post" id="installation_form" style="display: flex; width: 100%; align-items: center; justify-content: center; flex-direction: row; margin: 30px 0 20px;">
        <input type="hidden" name="username" value="administrator">
        <input type="hidden" name="password" value="admin">
        <input type="hidden" name="referer" value="<?php echo make_url('about'); ?>">
        <button type="submit" onclick="document.getElementById('start_install').classList.add('disabled');document.getElementById('installation_form').classList.add('submitting');" id="start_install" style="margin-left: auto;">
            <span class="name"><?= __('Got it!'); ?></span>
            <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
        </button>
    </form>
<?php endif; ?>
<?php include_component('installation/footer'); ?>
