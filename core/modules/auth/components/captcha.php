<?php

    /**
     * @var \Gregwar\Captcha\CaptchaBuilder $captcha
     */

?>
<div class="form-row captcha centered">
    <img src="<?= $captcha->inline(100); ?>">
</div>
<div class="form-row" data-field="verification_no">
    <label for="verification_no"><?php echo __('Enter the number you see above'); ?></label>
    <input type="text" class="required" id="verification_no" name="verification_no" maxlength="6" value="" autocomplete="off">
    <div class="error"></div>
</div>
