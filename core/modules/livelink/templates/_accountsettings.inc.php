<?php

/**
 * @var \pachno\core\entities\User $pachno_user
 * @var \pachno\core\modules\livelink\Livelink $module
 */

?>
<h3><?= __('Configured services'); ?></h3>
<p><?= __('Link your external services (such as GitHub, GitLab, etc) from this page to enable linking projects via %pachno_live_link', ['%pachno_live_link' => link_tag('https://pachno.com/features/livelink', fa_image_tag('leaf') . 'Pachno Live Link')]); ?></p>
<?php if (!$module->hasConnectors()): ?>
    <p class="livelink-intro">
        <?= __('%pachno_live_link requires integration plugins. Download the integration plugins from %configure_modules or visit %pachno_com to get started.', ['%pachno_live_link' => link_tag('https://pachno.com/features/livelink', fa_image_tag('leaf') . 'Pachno Live Link', ['target' => '_blank']), '%pachno_com' => link_tag('https://pachno.com/register/self-hosted', fa_image_tag('globe') . ' pachno.com'), '%configure_modules' => link_tag(make_url('configure_modules'), __('Configuration center') . '&nbsp;&raquo;&nbsp;' . __('Modules'))]); ?>
    </p>
<?php else: ?>
    <ul id="livelink-connector-accounts" class="livelink_connector_accounts">
        <?php foreach ($module->getConnectorModules() as $connector_key => $connector_provider): ?>
            <li id="livelink-<?= $connector_key; ?>-configuration" class="<?= ($connector_provider->getConnector()->isConfigured()) ? 'connected' : ''; ?>">
                <span class="description"><?= fa_image_tag($connector_provider->getConnector()->getLogo(), ['class' => 'connector_logo'], $connector_provider->getConnector()->getLogoStyle()) . $connector_provider->getConnector()->getName(); ?><span class="not-connected"><?= fa_image_tag('square') . __('Not connected'); ?></span><span class="connected-ok"><?= fa_image_tag('check-square', [], 'far') . __('Connected'); ?></span></span>
                <button class="button button-connect-livelink-connector" onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'livelink-configure_connector', 'connector' => $connector_key]); ?>');"><?= __('Connect'); ?></button>
                <button class="button button-disconnect-livelink-connector" data-connector="<?= $connector_key; ?>"><?= image_tag('spinning_16.gif', ['class' => "indicator"]) . __('Disconnect'); ?></button>
            </li>
        <?php endforeach; ?>
    </ul>
    <script>
        require(['domReady', 'pachno/index', 'jquery'], function (domReady, pachno_index_js, $) {
            domReady(function () {

                var $livelink_connector_accounts = $('#livelink-connector-accounts');

                var disconnectConnector = function(e) {
                    var url       = '<?= make_url('disconnect_livelink_connector'); ?>',
                        $button   = $(this),
                        connector = $button.data('connector');

                    e.preventDefault();

                    $button.addClass('submitting');
                    $button.attr('disabled', true);

                    var submitStep = function () {
                        return new Promise(function (resolve, reject) {
                            $.ajax({
                                type: 'POST',
                                dataType: 'text',
                                data: 'connector=' + connector,
                                url: url,
                                success: resolve,
                                error: function (details) {
                                    $button.removeClass('submitting');
                                    $button.attr('disabled', false);
                                    reject(details);
                                }
                            });
                        });
                    };

                    submitStep()
                        .then(function (result) {
                            $('#livelink-' + connector + '-configuration').removeClass('connected');
                            $button.removeClass('submitting');
                            $button.attr('disabled', false);
                        }, function (details) {
                            pachno_index_js.Helpers.Message.error(details.responseJSON.error);
                        });
                };

                $livelink_connector_accounts.off('click');
                $livelink_connector_accounts.on('click', '.button-disconnect-livelink-connector', disconnectConnector);
            });
        });
    </script>
<?php endif; ?>
