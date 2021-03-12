import Pachno from "../classes/pachno";
import $ from "jquery";

const getModuleUpdates = function () {
    const type = 'module';
    const $plugins = $('#modules-list').find('.module');

    let data = '';
    $plugins.each(function () {
        const $plugin = $(this);
        if (!$plugin.hasClass('disabled')) {
            data += '&addons[]=' + $plugin.data('module-key');
        }
    });
    Pachno.fetch(Pachno.data_url, {
        method: 'GET',
        data: 'say=get_module_updates' + data
    })
        .then((json) => {
            $plugins.each(function () {
                const plugin = $(this);
                const json_plugin = json[plugin.data('module-key')];
                if (json_plugin !== undefined) {
                    if (plugin.data('version') != json_plugin.version) {
                        plugin.addClass('can-update');
                        let link = $(type + '_'+plugin.data('module-key')+'_download_location');
                        link.attr('href', json_plugin.download);
                        // $('body').on('click', '.update-module-menu-item', function (e) {
                        //     var p luginbox = $(this).parents('li.'+type);
                        //     $('#update_module_help_' + pluginbox.data('id')).show();
                        //     if (!Pachno.Core.Pollers.pluginupdatepoller)
                        //         Pachno.Core.Pollers.pluginupdatepoller = new PeriodicalExecuter(Pachno.Core.validatePluginUpdateUploadedPoller(type, pluginbox.data('module-key')), 5);
                        // });
                    }
                }
            });
    });
};

const getAvailableModules = function () {
    Pachno.fetch(Pachno.data_url, {
        method: 'GET',
        data: '&say=get_modules',
        success: {
            update: '#available_modules_container',
            callback: function () {
                $('body').on('click', '.module .install-button', installModule);
            }
        }
    });
};

const installModule = function () {
    const $button = $(this);
    const type = 'module';
    const $module = $button.parents('.module');
    $module.addClass('submitting');
    $button.prop('disabled', true);
    Pachno.fetch(Pachno.data_url, {
        method: 'POST',
        data: { say: 'install-module', module_key: $button.data('key') },
        success: {
            callback: function (json) {
                if (json.installed) {
                    $('#online-module-' + json[type+'_key']).addClass('installed');
                    $('#installed-modules-list').append(json[type]);
                }
            }
        },
        failure: {
            callback: function () {
                $module.removeClass('submitting');
                $button.prop('disabled', false);
            }
        }
    });
};

const setupListeners = function () {
    Pachno.on(Pachno.EVENTS.ready, function () {
        if ($('#available_modules_container').length) {
            getModuleUpdates();
            getAvailableModules();
        }
    });
};

export {
    setupListeners
};
