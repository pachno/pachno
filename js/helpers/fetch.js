import $ from "jquery";
import {clearFormSubmit, is_string} from "../tools/tools";
import UI from "./ui";
import Pachno from "../classes/pachno";

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
    return new Promise((resolve, reject) => {
        const method = (options.method) ? options.method.toUpperCase() : 'GET';
        const $form = (options.form) ? $('#' + options.form) : undefined;

        if (options.form !== undefined && method === 'GET') {
            throw new Error('Cannot send form data when using GET method');
        }

        if (options.form && $form.length === 0) {
            throw new Error('Trying to post a form without an id');
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
                $form.find('.form-row').removeClass('invalid');
                $form.find('button[type=submit]').each(function () {
                    let $button = $(this);
                    $button.addClass('auto-disabled');
                    $button.prop('disabled', true);
                });
            }
        }

        onLoading();
        let response;
        let fetch_options = {
            method: method,
            headers: {
                "Accept": "application/json"
            }
        };

        if (['POST', 'PUT'].indexOf(method) !== -1) {
            let data;
            if ($form !== undefined && $form.length && $form[0].tagName.toLowerCase() === 'form') {
                data = new FormData($form[0]);
            } else {
                data = new FormData();
            }
            if (options.data) {
                for (let param in options.data) {
                    if (options.data.hasOwnProperty(param)) {
                        data.append(param, options.data[param]);
                    }
                }
            }

            fetch_options.body = data;
        } else if (method === 'GET') {
            if (options.data) {
                const concatenator = (url.indexOf('?') !== -1) ? '&' : '?';
                url += concatenator + options.data;
            }
        }

        fetch(url, fetch_options)
            .then((_response) => {
                response = _response;
                const contentType = response.headers.get("content-type");
                const is_json = (contentType && contentType.indexOf("application/json") !== -1);

                return new Promise((_resolve, _reject) => {
                    if (response.ok && is_json) {
                        response.json().then(json => {
                            _resolve(json);
                        });
                    } else {
                        if (options.failure) {
                            processCommonAjaxPostEvents(options.failure);
                        }

                        response.json().then(json => {
                            UI.Message.error(json.error, json.message);
                            if (options.failure && options.failure.callback) {
                                options.failure.callback(json);
                            }
                            if ($form !== undefined) {
                                $form.addClass('invalid');
                                if (json.errors !== undefined) {
                                    $form.find('.form-row.error').html();
                                    for (const error in json.errors) {
                                        if (!json.errors.hasOwnProperty(error))
                                            continue;

                                        const $errors = $form.find(`.form-row[data-field="${error}"]`);
                                        $errors.addClass('invalid');
                                        if (json.errors[error] !== "") {
                                            $errors.find('.error').html(json.errors[error]);
                                        }
                                    }
                                }
                            }
                            _reject(json);
                        }).catch(() => _reject(response));
                    }
                });
            })
            .then((json, responseText) => {
                if (json || (options.success && options.success.update)) {
                    if (json && json.forward !== undefined) {
                        document.location = json.forward;
                    } else {
                        if (options.success && options.success.update) {
                            let json_content_element = (is_string(options.success.update) || options.success.update.from === undefined) ? 'content' : options.success.update.from;
                            json_content_element = json_content_element || (json.component !== undefined) ? 'component' : 'content';

                            let content;
                            if (false && json && json_content_element !== undefined && json[json_content_element] !== undefined) {
                                content = json[json_content_element];
                            } else if (json) {
                                content = json.component || json.content;
                            } else {
                                content = responseText;
                            }
                            let update_element = (is_string(options.success.update)) ? options.success.update : options.success.update.element;
                            if ($(update_element).length) {
                                let insertion = (is_string(options.success.update)) ? false : (options.success.update.insertion) ? options.success.update.insertion : false;
                                let replace = (is_string(options.success.update)) ? false : (options.success.update.replace) ? options.success.update.replace : false;
                                if (insertion) {
                                    let $form_container = options.success.update.list === true ? $(update_element) : $(update_element).find('> .form-container');
                                    let $no_items_element = $(update_element).find('.no-items');
                                    if ($no_items_element.length) {
                                        $no_items_element.addClass('hidden');
                                    }

                                    if ($form_container.length) {
                                        if (options.success.update.list) {
                                            $form_container.append($(content));
                                        } else {
                                            $(content).insertBefore($form_container);
                                        }
                                    } else {
                                        $(update_element).append(content);
                                    }
                                } else if (replace) {
                                    $(update_element).replaceWith(content);
                                } else {
                                    $(update_element).html(content);
                                }
                            } else {
                                console.error('Trying to update element ' + options.success.update + ' but it does not exist in markup');
                                console.error(options);
                                console.trace();
                            }
                            if (json && json.message) {
                                UI.Message.success(json.message);
                            }
                        } else if (options.success && options.success.replace) {
                            let json_content_element = (is_string(options.success.replace) || options.success.replace.from == undefined) ? 'content' : options.success.replace.from;
                            let content = (json) ? json[json_content_element] : responseText;
                            let replace_element = (is_string(options.success.replace)) ? options.success.replace : options.success.replace.element;
                            if ($(replace_element)) {
                                Element.replace(replace_element, content);
                            }
                            if (json && json.message) {
                                UI.Message.success(json.message);
                            }
                        } else if (json && json.title && json.content) {
                            UI.Message.success(json.title, json.content);
                        } else if (json && (json.message)) {
                            UI.Message.success(json.message);
                        }
                        if (json && options.success && options.success.update_issues_from_json) {
                            if (json.issue !== undefined) {
                                Pachno.trigger(Pachno.EVENTS.issue.updateJson, {json: json.issue});
                            }
                            if (json.issues !== undefined) {
                                for (const json_issue of json.issues) {
                                    Pachno.addIssue(json_issue);
                                    Pachno.trigger(Pachno.EVENTS.issue.updateJson, {json: json_issue});
                                }
                            }
                        }
                        if ($form !== undefined && json.new_values !== undefined) {
                            for (const field in json.new_values) {
                                if (!json.new_values.hasOwnProperty(field))
                                    continue;

                                const $field = $form.find(`.form-row[data-field="${field}"]`);
                                if ($field.length) {
                                    const $input = $field.find('input');
                                    if ($input.prop('type') === 'text') {
                                        $input.val(json.new_values[field]);
                                    }
                                }
                            }
                        }
                        if (options.success) {
                            processCommonAjaxPostEvents(options.success);
                            if (options.success.callback) {
                                options.success.callback(json);
                            }
                        }
                    }
                }
                return json;
            })
            .then((json) => {
                if (fetch_debugger !== undefined) {
                    $('#___PACHNO_DEBUG_INFO___indicator').hide();
                    let d = new Date(),
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
                        let json = (response.responseJSON) ? response.responseJSON : undefined;
                        options.complete.callback(json);
                    }
                }
                if ($form !== undefined && $form.data('reset-backdrop') !== undefined) {
                    UI.Backdrop.reset();
                }
                Pachno.trigger(EVENTS.updated);
                resolve(json);
            })
            .catch(error => {
                console.error(error);
                console.error('OPTIONS', options);
                reject(error);

                clearFormSubmit($form);
            });
    });
};

export const formSubmitHelper = function (url, form_id, options) {
    const fetchOptions = {
        form: form_id,
        method: 'POST'
    };

    if (options !== undefined) {
        if (options.success !== undefined) {
            fetchOptions.success = options.success;
        }
        if (options.data !== undefined) {
            fetchOptions.data = options.data;
        }
    }

    return fetchHelper(url, fetchOptions);
};

export const setFetchDebugger = function (_fetch_debugger) {
    fetch_debugger = _fetch_debugger;
};

export const setupListeners = function () {
};
