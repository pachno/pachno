<?php include_component('installation/header'); ?>
<div class="installation_box">
    <h2>License information</h2>
    This software is Open Source Initiative approved Open Source Software. Open Source Initiative Approved is a trademark of the Open Source Initiative.
    True to the <a target="_blank" href="http://opensource.org/docs/definition.php"><span>the Open Source Definition</span><?= fa_image_tag('external-link-alt', ['class' => 'icon external'], 'fas'); ?></a>, Pachno is released under the MPL 2.0.<br>
    <br>
    <span class="button-container">
        <a target="_blank" href="http://opensource.org/licenses/MPL-2.0" class="button secondary"><span>Read the license</span><?= fa_image_tag('external-link-alt', ['class' => 'icon'], 'fas'); ?></a><br>
    </span>
    <br>
    Before you can continue the installation, you need to confirm that you agree to be bound by the terms in this license.<br>
    <br>
    <br>
    <form accept-charset="utf-8" action="index.php" method="post" style="display: flex; width: 100%; align-items: center; justify-content: center; flex-direction: row;">
        <input type="hidden" name="step" value="1">
        <input type="hidden" name="agree_license" value="1">
        <div class="contact-container">
            <a href="https://pachno.zulipchat.com" class="button secondary" target="_blank"><?= fa_image_tag('comments', ['class' => 'icon'], 'far'); ?><span>Zulip chat</span></a>
            <a href="https://twitter.com/pachnopm" class="button secondary" target="_blank"><?= fa_image_tag('twitter', ['class' => 'icon'], 'fab'); ?><span>@pachnopm</span></a>
            <a href="mailto:feedback@pach.no" class="button secondary"><?= fa_image_tag('at', ['class' => 'icon']); ?><span>feedback@pach.no</span></a>
        </div>
        <input type="submit" class="button primary" style="margin-left: auto;" value="Agree and continue" id="start_installation">
    </form>
</div>
<?php include_component('installation/footer'); ?>
