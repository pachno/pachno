<div id="fullpage_backdrop" class="fullpage_backdrop" style="display: none;">
    <div id="fullpage_backdrop_indicator">
        <?php echo image_tag('spinning_32.gif'); ?><br>
        <?php echo __('Please wait ...'); ?>
    </div>
    <div id="fullpage_backdrop_content" class="fullpage_backdrop_content" data-simplebar> </div>
</div>
<div class="popup_message failure" onclick="Pachno.UI.Message.clear();" style="display: none;" id="pachno_failuremessage">
    <div class="message-content">
        <span class="title" id="pachno_failuremessage_title"></span>
        <span class="message" id="pachno_failuremessage_content"></span>
    </div>
    <div class="dismiss_me"><?php echo __('Okay'); ?></div>
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
<div class="popup_message success" onclick="Pachno.UI.Message.clear();" style="display: none;" id="pachno_successmessage">
    <div class="message-content">
        <span class="title" id="pachno_successmessage_title"></span>
        <span class="message" id="pachno_successmessage_content"></span>
    </div>
    <div class="dismiss_me"><?php echo __('Okay'); ?></div>
</div>
<?php /*if (\pachno\core\framework\Context::getRouting()->getCurrentRoute()->getName() != 'auth_login_page' && $pachno_user->isGuest()): ?>
    <div id="login_backdrop" class="fullpage_backdrop" style="display: none;">
        <div id="login_content" class="fullpage_backdrop_content">
            <?php include_component('auth/loginpopup', array('content' => get_component_html('auth/login'), 'mandatory' => false)); ?>
        </div>
    </div>
<?php endif; */ ?>
<div class="quicksearch-container" id="quicksearch-container">
    <div class="quicksearch-content">
        <div class="searchbox-container">
            <input type="search" name="quicksearch" id="quicksearch-input">
            <div class="description" id="current-command-description"></div>
        </div>
        <div class="separator"></div>
        <div class="quicksearch-results" id="quicksearch-results"></div>
    </div>
</div>
<div class="fullpage_backdrop" id="dialog_backdrop" style="display: none;">
    <div id="dialog_backdrop_content" class="fullpage_backdrop_content">
        <div class="backdrop_box small">
            <div class="backdrop_detail_header"><span id="dialog_title"></span></div>
            <div class="backdrop_detail_content dialog">
                <p id="dialog_content"></p>
            </div>
            <div class="backdrop_details_submit">
                <span class="explanation"></span>
                <div class="dialog-query-buttons">
                    <?= fa_image_tag('spinner', ['class' => 'indicator fa-spin']); ?>
                    <a href="javascript:void(0)" id="dialog_no" class="button secondary"><span><?php echo __('No'); ?></span></a>
                    <a href="javascript:void(0)" id="dialog_yes" class="button primary"><span><?php echo __('Yes'); ?></span></a>
                </div>
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
                <a href="javascript:void(0)" id="dialog_okay" onclick="Pachno.UI.Dialog.dismissModal();" class="button"><?php echo __('Okay'); ?></a>
            </div>
        </div>
    </div>
</div>
<label for="file_upload_dummy" id="file_upload_dummy_label"></label>
<input type="file" id="file_upload_dummy" multiple data-upload-url="<?php echo make_url('upload_file'); ?>">
