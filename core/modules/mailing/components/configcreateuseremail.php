<div class="form-row">
    <input type="checkbox" class="fancy-checkbox" name="send_login_details" value="1" id="user_send_login_details" checked>
    <label for="user_send_login_details" class="optional">
        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
        <span><?= __('Send user information by email'); ?></span>
    </label>
</div>
