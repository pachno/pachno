<div id="tab_livelink_pane"<?php if ($selected_tab != 'livelink'): ?> style="display: none;"<?php endif; ?> class="configuration-content centered" data-tab-id="livelink">
    <?php include_component('livelink/projectconfig_template', ['project' => $project]); ?>
</div>
<script>
    $(document).ready(() => {
        const submitSetupStep = function(e) {
            const form_id        = 'livelink_form',
                $form          = $('#' + form_id),
                $indicator     = $('#' + form_id + '_indicator'),
                $submit_button = $('#' + form_id + '_button'),
                url            = $form.attr("action");

            $indicator.show();
            e.preventDefault();

            const submitStep = function () {
                return new Promise(function (resolve, reject) {
                    $.ajax({
                        type: 'POST',
                        data: $form.serialize(),
                        url: url,
                        success: resolve,
                        error: function (details) {
                            $indicator.hide();
                            $submit_button.prop('disabled', false);
                            reject(details);
                        }
                    });
                });
            };

            submitStep()
                .then(function (result) {
                    $indicator.hide();
                    $form.addClass('disabled');
                    $('#livelink_address_container').addClass('verified');
                    $('#livelink_repository_url_input').prop('disabled', true);
                }, function (details) {
                    Pachno.UI.Message.error(details.responseJSON.error);
                });
        };

        $('#livelink_form').submit(submitSetupStep);

        $('#livelink_change_button').click(function (e) {
            e.preventDefault();

            $('#livelink_form').removeClass('disabled');
            $('#livelink_address_container').removeClass('verified');
            $('#livelink_repository_url_input').prop('disabled', false);
        });
    });
</script>
