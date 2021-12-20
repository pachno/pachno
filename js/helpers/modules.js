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
                        plugin.find('.can-update').removeClass('hidden');
                        let link = $(type + '_'+plugin.data('module-key')+'_download_location');
                        link.attr('href', json_plugin.download);
                        $('body').on('click', '.update-module-menu-item', function (e) {
                            const plugin_box = $(this).parents('li.'+type);
                            $('#update_module_help_' + plugin_box.data('id')).toggleClass('hidden');
                        });
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
            update: '#available_modules_container'
        }
    });
};

const installModule = function () {
    const $button = $(this);
    const $module = $button.parents('.module');
    const moduleKey = $button.data('key');
    const is_update = $button.data('update') !== undefined;

    $module.addClass('submitting');
    $button.prop('disabled', true);
    Pachno.fetch(Pachno.data_url, {
        method: 'POST',
        data: {
            say: 'install-module',
            module_key: moduleKey,
            download: (is_update) ? 1 :0,
            install_update: ($button.data('install-update') !== undefined) ? 1 :0
        },
        success: {
            callback: function (json) {
                if (json.installed) {
                    if ($('#online-module-' + moduleKey).length) {
                        $('#online-module-' + moduleKey).addClass('installed');
                    }
                    if ($('#module_' + moduleKey).length) {
                        $('#module_' + moduleKey).replaceWith(json.module);
                    } else {
                        $('#modules-list .onboarding').addClass('hidden');
                        $('#modules-list').append(json.module);
                    }
                    if (!is_update) {
                        getModuleUpdates();
                    }
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

const uninstallModule = function () {
    const $button = $(this);
    const $module = $button.parents('.module');
    const moduleKey = $button.data('key');

    $module.addClass('submitting');
    $button.prop('disabled', true);
    Pachno.fetch(Pachno.data_url, {
            method: 'POST',
            data: {
                say: 'uninstall-module',
                module_key: moduleKey
            },
            failure: {
                callback: function () {
                    $module.removeClass('submitting');
                    $button.prop('disabled', false);
                }
            }
        })
        .then((json) => {
            if (json.uninstalled) {
                if ($('#online-module-' + moduleKey).length) {
                    $('#online-module-' + moduleKey).removeClass('installed');
                }
                $('#module_' + moduleKey).replaceWith(json.module);
                $('#uninstall_module_' + moduleKey).addClass('hidden');
            }
        });
};

const setupListeners = function () {
    Pachno.on(Pachno.EVENTS.ready, function () {
        if ($('#available_modules_container').length) {
            getModuleUpdates();
            getAvailableModules();
        }
        $('body').on('click', '.module .trigger-install-module', installModule);
        $('body').on('click', '.trigger-uninstall-module', uninstallModule);
    });
};

export {
    setupListeners
};
