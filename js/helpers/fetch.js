import $ from "jquery";
import {clearFormSubmit, is_string} from "../tools/tools";
import UI from "./ui";

let fetch_debugger = undefined;

export const EVENTS = {
    updated: 'fetch-dom-updated'
};

const processCommonAjaxPostEvents = function (options) {
    if (options.remove) {
        if (is_string(options.remove)) {
            if ($(options.remove))
                $(options.remove).remove();
        } else {
            options.remove.each(function (s) {
                if (is_string(s) && $(s))
                    $(s).remove();
                else if ($(s))
                    s.remove();
            });
        }
    }
    if (options.hide) {
        if (is_string(options.hide)) {
            if ($(options.hide))
                $(options.hide).hide();
        } else {
            options.hide.each(function (s) {
                if (is_string(s) && $(s))
                    $(s).hide();
                else if ($(s))
                    s.hide();
            });
        }
    }
    if (options.show) {
        if (is_string(options.show)) {
            if ($(options.show))
                $(options.show).show();
        } else {
            options.show.each(function (s) {
                if ($(s))
                    $(s).show();
            });
        }
    }
    if (options.enable) {
        if (is_string(options.enable)) {
            if ($(options.enable))
                $(options.enable).prop('disabled', false);
        } else {
            options.enable.each(function (s) {
                if ($(s))
                    $(s).prop('disabled', false);
            });
        }
    }
    if (options.disable) {
        if (is_string(options.disable)) {
            if ($(options.disable))
                $(options.disable).prop('disabled', true);
        } else {
            options.disable.each(function (s) {
                if ($(s))
                    $(s).prop('disabled', true);
            });
        }
    }
    if (options.reset) {
        if (is_string(options.reset)) {
            if ($(options.reset))
                $(options.reset).reset();
        } else {
            options.reset.each(function (s) {
                if ($(s))
                    $(s).reset();
            });
        }
    }
    if (options.clear) {
        if (is_string(options.clear)) {
            if ($(options.clear))
                $(options.clear).clear();
        } else {
            options.clear.each(function (s) {
                if ($(s))
                    $(s).clear();
            });
        }
    }
}

export const fetchHelper = function (url, options) {
    const method = (options.method) ? options.method : 'POST';
    const $form = (options.form) ? $('#' + options.form) : undefined;

    let params = (options.params) ? options.params : '';

    if (options.form && options.form != undefined) {
        params = $form.serialize();
    }
    if (options.additional_params) {
        params += options.additional_params;
    }

    const onLoading = () => {
        if (options.loading) {
            if (fetch_debugger !== undefined) {
                $('#___PACHNO_DEBUG_INFO___indicator').show();
            }
            if ($(options.loading.indicator)) {
                $(options.loading.indicator).show();
            }
            if ($(options.loading.disable)) {
                $(options.loading.disabled).prop('disabled', true);
            }
            processCommonAjaxPostEvents(options.loading);
            if (options.loading.callback) {
                options.loading.callback();
            }
        }
        if ($form !== undefined) {
            $form.addClass('submitting');
            $form.find('button[type=submit]').each(function () {
                var $button = $(this);
                $button.addClass('auto-disabled');
                $button.attr("disabled", true);
            });
        }
    }

    onLoading();
    let response;
    let fetch_options = {
        method: method
    };
    if (method === 'POST') {
        fetch_options.body = params;
    }

    fetch(url, fetch_options)
        .then((_response) => {
            response = _response;
            const contentType = response.headers.get("content-type");
            const is_json = (contentType && contentType.indexOf("application/json") !== -1);

            return new Promise((resolve, reject) => {
                if (response.ok && is_json) {
                    response.json().then(json => {
                        resolve(json);
                    });
                } else {
                    if (options.failure) {
                        processCommonAjaxPostEvents(options.failure);
                    }

                    response.json().then(json => {
                        UI.Message.error(json.error, json.message);
                        if (options.failure.callback) {
                            options.failure.callback(json);
                        }
                    });
                    reject(response);
                }
            });
        })
        .then((json, responseText) => {
            if (json || (options.success && options.success.update)) {
                if (json && json.forward != undefined) {
                    document.location = json.forward;
                } else {
                    if (options.success && options.success.update) {
                        var json_content_element = (is_string(options.success.update) || options.success.update.from == undefined) ? 'content' : options.success.update.from;
                        var content = (json) ? json[json_content_element] : responseText;
                        var update_element = (is_string(options.success.update)) ? options.success.update : options.success.update.element;
                        if ($(update_element)) {
                            var insertion = (is_string(options.success.update)) ? false : (options.success.update.insertion) ? options.success.update.insertion : false;
                            if (insertion) {
                                $(update_element).append(content);
                            } else {
                                $(update_element).html(content);
                            }
                        }
                        if (json && json.message) {
                            UI.Message.success(json.message);
                        }
                    } else if (options.success && options.success.replace) {
                        var json_content_element = (is_string(options.success.replace) || options.success.replace.from == undefined) ? 'content' : options.success.replace.from;
                        var content = (json) ? json[json_content_element] : responseText;
                        var replace_element = (is_string(options.success.replace)) ? options.success.replace : options.success.replace.element;
                        if ($(replace_element)) {
                            Element.replace(replace_element, content);
                        }
                        if (json && json.message) {
                            UI.Message.success(json.message);
                        }
                    } else if (json && (json.title || json.content)) {
                        UI.Message.success(json.title, json.content);
                    } else if (json && (json.message)) {
                        UI.Message.success(json.message);
                    }
                    if (options.success) {
                        processCommonAjaxPostEvents(options.success);
                        if (options.success.callback) {
                            options.success.callback(json);
                        }
                    }
                }
            }
        })
        .then(() => {
            if (fetch_debugger !== undefined) {
                $('#___PACHNO_DEBUG_INFO___indicator').hide();
                var d = new Date(),
                    d_id = response.headers.get('x-pachno-debugid'),
                    d_time = response.headers.get('x-pachno-loadtime'),
                    d_session_time = response.headers.get('x-pachno-sessiontime'),
                    d_calculated_time = response.headers.get('x-pachno-calculatedtime');

                fetch_debugger.updateDebugInfo({location: url, time: d, debug_id: d_id, loadtime: d_time, session_loadtime: d_session_time, calculated_loadtime: d_calculated_time });
            }
            if (options.loading) {
                $(options.loading.indicator).hide();
                if ($(options.loading.disable)) {
                    $(options.loading.disabled).prop('disabled', false);
                }
            }
            if (options.complete) {
                processCommonAjaxPostEvents(options.complete);
                if (options.complete.callback) {
                    var json = (response.responseJSON) ? response.responseJSON : undefined;
                    options.complete.callback(json);
                }
            }
            Pachno.trigger(EVENTS.updated);
        })
        .catch(error => {
            console.error(error);
            console.error('OPTIONS', options);

            clearFormSubmit($form);
        });
};

export const formSubmitHelper = function (url, form_id) {
    fetchHelper(url, {
        form: form_id,
        loading: {indicator: form_id + '_indicator', disable: form_id + '_button'},
        success: {enable: form_id + '_button'},
        failure: {enable: form_id + '_button'}
    });
};

export const setFetchDebugger = function (fetch_debugger) {
    fetch_debugger = fetch_debugger;
};
