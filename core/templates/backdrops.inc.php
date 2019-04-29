<div class="almost_not_transparent shadowed popup_message failure" onclick="Pachno.Main.Helpers.Message.clear();" style="display: none;" id="pachno_failuremessage">
    <div style="padding: 10px 0 10px 0;">
        <div class="dismiss_me"><?php echo __('Click this message to dismiss it'); ?></div>
        <span class="messagetitle" id="pachno_failuremessage_title"></span>
        <span id="pachno_failuremessage_content"></span>
    </div>
</div>
<div class="tutorial-message" id="tutorial-message" style="display: none;" data-disable-url="<?php echo make_url('disable_tutorial'); ?>">
    <div id="tutorial-message-container"></div>
    <div class="tutorial-buttons dialog-query-buttons">
        <a class="button secondary button-disable" id="disable-tutorial-button" href="javascript:void(0);"><?php echo __('Skip this tutorial'); ?></a>
        <button class="button button-standard button-next primary" id="tutorial-next-button"></button>
    </div>
    <br style="clear: both;">
    <div class="tutorial-status"><span id="tutorial-current-step"></span> of <span id="tutorial-total-steps"></span></div>
</div>
<div class="almost_not_transparent shadowed popup_message success" onclick="Pachno.Main.Helpers.Message.clear();" style="display: none;" id="pachno_successmessage">
    <div style="padding: 10px 0 10px 0;">
        <div class="dismiss_me"><?php echo __('Click this message to dismiss it'); ?></div>
        <span class="messagetitle" id="pachno_successmessage_title"></span>
        <span id="pachno_successmessage_content"></span>
    </div>
</div>
<div id="fullpage_backdrop" class="fullpage_backdrop" style="display: none;">
    <div id="fullpage_backdrop_indicator">
        <?php echo image_tag('spinning_32.gif'); ?><br>
        <?php echo __('Please wait ...'); ?>
    </div>
    <div id="fullpage_backdrop_content" class="fullpage_backdrop_content"> </div>
</div>
<?php if (\pachno\core\framework\Context::getRouting()->getCurrentRouteName() != 'login_page' && $pachno_user->isGuest()): ?>
    <div id="login_backdrop" class="fullpage_backdrop" style="display: none;">
        <div id="login_content" class="fullpage_backdrop_content">
            <?php include_component('main/loginpopup', array('content' => get_component_html('main/login'), 'mandatory' => false)); ?>
        </div>
    </div>
<?php endif; ?>
<div class="fullpage_backdrop" id="dialog_backdrop" style="display: none;">
    <div id="dialog_backdrop_content" class="backdrop_box">
        <div class="backdrop_detail_header"><span id="dialog_title"></span></div>
        <div class="backdrop_detail_content">
            <p id="dialog_content"></p>
        </div>
        <div class="backdrop_details_submit">
            <span class="explanation"></span>
            <div class="dialog-query-buttons">
                <?php echo image_tag('spinning_20.gif', array('style' => 'display: none;', 'id' => 'dialog_indicator')); ?>
                <a href="javascript:void(0)" id="dialog_no" class="button secondary"><?php echo __('No'); ?></a>
                <a href="javascript:void(0)" id="dialog_yes" class="button primary"><?php echo __('Yes'); ?></a>
            </div>
        </div>
    </div>
</div>
<div class="fullpage_backdrop" id="dialog_backdrop_modal" style="display: none;">
    <div id="dialog_backdrop_modal_content" class="backdrop_box">
        <div class="backdrop_detail_header"><span id="dialog_modal_title"></span></div>
        <div class="backdrop_detail_content">
            <p id="dialog_modal_content"></p>
        </div>
        <div class="backdrop_details_submit">
            <span class="explanation"></span>
            <div class="submit_container">
                <a href="javascript:void(0)" id="dialog_okay" onclick="Pachno.Main.Helpers.Dialog.dismissModal();" class="button"><?php echo __('Okay'); ?></a>
            </div>
        </div>
    </div>
</div>
<input type="file" id="file_upload_dummy" style="display: none;" multiple onchange="Pachno.Main.selectFiles(this);" data-upload-url="<?php echo make_url('upload_file'); ?>">