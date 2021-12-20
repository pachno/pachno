<?php include_component('installation/header'); ?>
<div class="installation_box">
    <?php if (isset($error)): ?>
        <div class="message-box type-error">
            <?= fa_image_tag('times'); ?>
            <span class="message">
                    <b>An error occured</b><br>
                    <?php echo nl2br($error); ?>
                </span>
        </div>
        <div style="font-size: 13px;">
            An error occured and the installation has been stopped. Please try to fix the error based on the information above, then click start the installation over.<br>
            If you think this is a bug, please report it in our <a href="https://projects.pach.no" target="_new">online bug tracker</a>.
        </div>
    <?php else: ?>
        <h2>Administrator account set up</h2>
        The administrator account has been set up successfully. To use this account, log in with the following information:
        <div class="installation_box">
            <dl class="install_list">
                <dt><label>Username</label></dt>
                <dd><span class="command_box"><?php echo $user->getUsername(); ?></span></dd>
                <dt><label>Password</label></dt>
                <dd><span class="command_box"><?php echo $password; ?></span></dd>
            </dl>
        </div>
        <br>
        Don't worry, that password is securely stored in your database with full encryption. The password displayed is the input you typed on the previous page.<br>
        You can change all these details from the "Account" page, after logging in.
        <div class="installation_box">
            <form accept-charset="utf-8" action="index.php" method="post" style="display: flex; width: 100%; align-items: center; justify-content: center; flex-direction: row;">
                <input type="hidden" name="step" value="6">
                <input type="submit" style="margin-left: auto;" value="Finalize installation" id="continue_button">
            </form>
        </div>
    <?php endif; ?>
</div>
<?php include_component('installation/footer'); ?>
