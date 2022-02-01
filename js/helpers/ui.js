import $ from "jquery";
import Pachno from "../classes/pachno";
import {fetchHelper, formSubmitHelper} from "./fetch";
import { clearPopupsAndButtons } from "../widgets";

const tabSwitcher = function ($tab, target, $tabSwitcher, change_hash) {
    if (!change_hash) {
        change_hash = false;
    }

    $tabSwitcher.children().removeClass('selected');
    $tab.addClass('selected');
    $('#' + $tabSwitcher.prop('id') + '_panes').children().each(function () {
        const $pane = $(this);
        if ($pane.data('tab-id') == target) {
            $pane.show();
        } else {
            $pane.hide();
        }
    });
    if (change_hash) {
        if (history.replaceState) {
            window.history.replaceState(null, null, '#' + target);
        }
        else {
            window.location.hash = target;
        }
    }
};

const UI = {
    Message: {
        /**
         * Clears all popup messages from the effect queue
         */
        clear: () => {
            $('#pachno_successmessage').hide();
            $('#pachno_failuremessage').hide();
        },

        /**
         * Shows an error popup message
         *
         * @param title string The title to show
         * @param content string Error details
         */
        error: (title, content) => {
            $('#pachno_failuremessage').hide();
            $('#pachno_failuremessage_title').html(title);
            $('#pachno_failuremessage_content').html(content);
            $('#pachno_failuremessage').show();
            $('#pachno_successmessage').hide();
        },

        /**
         * Shows a "success"-style popup message
         *
         * @param title string The title to show
         * @param content string Message details
         */
        success: (title, content) => {
            $('#pachno_successmessage').hide();
            $('#pachno_successmessage_title').html(title);
            $('#pachno_successmessage_content').html(content);
            $('#pachno_successmessage').show();
            $('#pachno_failuremessage').hide();

            setTimeout(() => {
                $('#pachno_successmessage').hide();
            }, 5000);
        }
    },
    Dialog: {
        show: (title, content, options) => {
            UI.Message.clear();
            $('#dialog_title').html(title);
            $('#dialog_content').html(content);
            $('#dialog_yes').attr('href', 'javascript:void(0)');
            $('#dialog_no').attr('href', 'javascript:void(0)');
            $('#dialog_yes').off('click');
            $('#dialog_no').off('click');
            $('#dialog_yes').removeClass('disabled');
            $('#dialog_no').removeClass('disabled');
            $('#dialog_yes').removeAttr('disabled');
            $('#dialog_no').removeAttr('disabled');
            if (options.yes.click) {
                $('#dialog_yes').on('click', options.yes.click);
            }
            if (options.yes.href) {
                $('#dialog_yes').attr('href', options.yes.href);
            }
            if (options.yes.text) {
                $('#dialog_yes').find('span').html(options.yes.text);
            } else {
                $('#dialog_yes').find('span').html(Pachno.T.ui.yes);
            }
            if (options.no.click) {
                $('#dialog_no').on('click', options.no.click);
            }
            if (options.no.href) {
                $('#dialog_no').attr('href', options.no.href);
            }
            if (options.no.text) {
                $('#dialog_no').find('span').html(options.no.text);
            } else {
                $('#dialog_no').find('span').html(Pachno.T.ui.no);
            }
            $('#dialog_backdrop').removeClass('submitting');
            $('#dialog_backdrop_content').show();
            $('#dialog_backdrop').show();
        },

        setSubmitting: () => {
            const $dialogYes = $('#dialog_yes');
            const $dialogNo = $('#dialog_no');

            $dialogYes.blur();
            $dialogYes.addClass('disabled');
            $dialogYes.attr('disabled', true);

            $dialogNo.addClass('disabled');
            $dialogNo.attr('disabled', true);

            $('#dialog_backdrop').addClass('submitting');
        },

        showModal: (title, content, options) => {
            UI.Message.clear();
            $('#dialog_modal_title').html(title);
            $('#dialog_modal_content').html(content);
            $('#dialog_backdrop_modal_content').show();
            $('#dialog_backdrop_modal').show();
            $('#dialog_okay').off('click');
            if (options !== undefined && options.url !== undefined) {
                $('#dialog_okay').attr('href', options.url);
            } else {
                $('#dialog_okay').on('click', UI.Dialog.dismissModal);
            }
        },

        dismiss: () => {
            $('#dialog_backdrop_content').hide();
            $('#dialog_backdrop').hide();
        },

        dismissModal: () => {
            $('#dialog_backdrop_modal_content').hide();
            $('#dialog_backdrop_modal').hide();
        }
    },

    Backdrop: {
        show: (url, callback, docked) => {
            return new Promise(resolve => {
                const showBackdrop = (docked) => new Promise(_resolve => {
                    $('#fullpage_backdrop_content').hide();
                    $('#fullpage_backdrop').removeClass('docked-left');
                    $('#fullpage_backdrop').removeClass('docked-right');
                    if (docked !== undefined) {
                        $('#fullpage_backdrop').addClass('docked-' + docked);
                    }
                    $('#fullpage_backdrop').show();
                    $('body').css({'overflow': 'hidden'});
                    $('#fullpage_backdrop_indicator').show();
                    _resolve();
                });

                showBackdrop(docked)
                    .then(() => {
                        if (url != undefined) {
                            fetchHelper(url, {
                                method: 'GET',
                                loading: {indicator: '#fullpage_backdrop_indicator'},
                                success: {
                                    update: '#fullpage_backdrop_content',
                                    callback: function () {
                                        $('#fullpage_backdrop_content').show();
                                        $('#fullpage_backdrop_indicator').hide();
                                        // Pachno.Helpers.MarkitUp($('#textarea.markuppable'));
                                        if (callback)
                                            setTimeout((callback)(), 300);
                                    }},
                                failure: {hide: '#fullpage_backdrop'}
                            });
                        } else {
                            console.error('Trying to show backdrop but no url');
                            console.trace();
                        }
                    })
                    .then(resolve);
            });
        },

        reset: (callback) => {
            $('body').css({'overflow': 'auto'});
            $('#fullpage_backdrop').hide();
            // Pachno.Core._resizeWatcher();
            if (callback) callback();
        }
    },

    tabSwitcher,

    parseHtmlOptions: function (options) {
        let option_strings = [];
        for (let [key, value] of Object.entries(options))
        {
            if (Array.isArray(value)) {
                value = value.join(' ');
            }
            if (key === 'classes') key = 'class';

            option_strings.push(`${key}="${value}"`);
        }
        return option_strings.join(' ');
    },

    fa_image_tag: function (image, params = {}, mode = 'fas')
    {
        if (params.classes === undefined) {
            params.classes = [];
        } else if (!Array.isArray(params.classes)) {
            params.classes = [params.classes];
        }

        params.classes.push(mode);
        params.classes.push(`fa-${image}`);

        return `<i ${this.parseHtmlOptions(params)}></i>`;
    }

};

const tabSwitchFromHash = function (menu) {
    let hash = window.location.hash;

    if (hash != undefined && hash.indexOf('tab_') == 1) {
        tabSwitcher(hash.substr(1), menu);
    }
};

const loadComponentOptions = function ($item) {
    new Promise(function (resolve, reject) {
        const $container = $item.parents('.configurable-components-container'),
            $optionsContainer = $('body').find('.configurable-component-options').first(),
            url = $item.data('options-url');

        $optionsContainer.html('<div><i class="fas fa-spin fa-spinner"></i></div>');
        $container.addClass('active');
        $container.find('.configurable-component').removeClass('active');
        $item.addClass('active');

        fetchHelper(url, {
            success: { update: '#' + $optionsContainer.attr('id') }
        }).then(resolve);
    });
};

const autoBackdropLink = function (event) {
    if (event.isPropagationStopped()) {
        return;
    }

    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    clearPopupsAndButtons(event);
    const $button = $(this);
    $button.prop('disabled', true);
    $button.addClass('disabled');
    $button.addClass('submitting');

    UI.Backdrop.show($button.data('url'), undefined, $button.data('docked-backdrop'))
        .then(() => {
            $button.prop('disabled', false);
            $button.removeClass('submitting');
            $button.removeClass('disabled');
        })
        .catch(error => {
            console.error(error);
            UI.Backdrop.reset();
        });
};

const autoSubmitForm = function (event) {
    const $form = $(this);
    event.preventDefault();
    return submitForm($form);
};

const submitForm = function ($form, options = {}) {
    const url = options.url || $form.data('url') || $form.attr('action');

    if ($form.attr('id') === undefined) {
        console.error($form);
        throw new Error('Trying to post a form without an id');
    }

    if ($form.data('update-container')) {
        if ($form.data('update-insert') !== undefined) {
            options.success = { update: { element: $form.data('update-container'), insertion: true }};
            if ($form.data('update-insert-form-list') !== undefined) {
                options.success.update.list = true;
            }
        } else if ($form.data('update-replace') !== undefined) {
            options.success = { update: { element: $form.data('update-container'), replace: true }};
        } else {
            options.success = { update: $form.data('update-container') };
        }
    }

    if ($form.data('update-issues') !== undefined) {
        if (options.success === undefined) {
            options.success = {};
        }
        options.success.update_issues_from_json = true;
    }

    return formSubmitHelper(url, $form.attr('id'), options)
        .then((json) => {
            $form.removeClass('submitting');
            $form.find('button[type=submit]').each(function () {
                var $button = $(this);
                $button.removeClass('auto-disabled');
                $button.prop('disabled', false);
            });

            if ($form.data('auto-close') !== undefined) {
                UI.Backdrop.reset();
            } else if ($form.data('auto-close-container') !== undefined) {
                $form.parents('.fullpage_backdrop').addClass('hidden');
            }

            Pachno.trigger(Pachno.EVENTS.formSubmitResponse, { form: $form.attr('id'), json });
        })
        .catch((error) => {
            console.error(error);
        });
};

const submitInteractiveForm = function (event, $form, prevent_default = false) {
    $form.addClass('submitting');
    $form.find('input[type=text]').blur();
    if (prevent_default) {
        event.preventDefault();
    }
    submitForm($form)
        .then(() => {
            $form.removeClass('submitting');
        })
        .catch((error) => {
            console.error(error);
            $form.removeClass('submitting');
        });
}

const submitStandaloneInput = function (event, $element) {
    const $form = $element.parents('.form');

    $form.addClass('submitting');
    $form.find('input[type=text]').blur();
    const key = $element.attr('name');
    const options = {
        data: {
            [key]: $element.val()
        }
    }
    event.preventDefault();

    submitForm($form, options)
        .then(() => {
            $form.removeClass('submitting');
        })
        .catch((error) => {
            console.error(error);
            $form.removeClass('submitting');
        });
}

const setupListeners = function() {
    const $body = $('body');

    $body.on('click', '.tab-switcher .tab-switcher-trigger', function () {
        const $tabSwitcher = $(this).parent('.tab-switcher');
        const $tab = $(this);
        const target = $tab.data('tab-target');

        tabSwitcher($tab, target, $tabSwitcher);
    });

    $body.on('click', '.fullpage_backdrop_content .closer', () => UI.Backdrop.reset());
    $body.on('click', '.trigger-backdrop', autoBackdropLink);

    $body.on('submit', 'form[data-simple-submit]', autoSubmitForm);
    $body.on('blur', 'input[data-verify-on-blur]', function (event) {
        event.preventDefault();
        event.stopPropagation();

        const $input = $(this);
        const $form = $input.parents('form');
        const options = {
            url: $input.data('url')
        };

        return submitForm($form, options);
    });

    $body.on('click', '.trigger-open-component', function(event) {
        event.preventDefault();
        event.stopPropagation();

        const $item = $(this).parents('.configurable-component');
        loadComponentOptions($item);
    });

    $body.on("click", ".collapser", function (e) {
        let collapser_item = $(this),

            is_visible = collapser_item.hasClass('active'),
            collapseItem = function (item) {
                let target = item.data('target');
                if (target) {
                    $(target).removeClass('active');
                }
                item.removeClass('active');
            },

            expandItem = function (item) {
                let target = item.data('target');
                if (target) {
                    $(target).addClass('active');
                }
                item.addClass('active');
            };

        if (collapser_item.data('exclusive')) {
            $('.collapser.active').each(function () {
                collapseItem(collapser_item);
            });
        }
        if (!is_visible) {
            expandItem(collapser_item);
        } else {
            collapseItem(collapser_item);
        }
        e.stopPropagation();
        e.preventDefault();
    });

    Pachno.on(Pachno.EVENTS.configuration.deleteComponent, (_, data) => {
        const $container = $('body').find('.configurable-components-container');
        const $optionsContainer = $('body').find('.configurable-component-options').first();
        $(`[data-${data.type}][data-id=${data.id}]`).remove();
        $container.removeClass('active');
        $optionsContainer.html('');
        Pachno.UI.Dialog.dismiss();

        Pachno.fetch(data.url, { method: 'DELETE' });
    });

    $body.on('blur', '.form-container .form[data-standalone-input]:not(.submitting) input[type=text]', (event) => submitStandaloneInput(event, $(event.target)));
    $body.on('keypress', '.form-container .form[data-standalone-input]:not(.submitting) input[type=text]', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            event.stopPropagation();
            submitStandaloneInput(event, $(event.target));
        }
    });

    $body.on('change', 'input[data-interactive-toggle]', function (event) {
        const $input = $(this),
            value = $input.is(':checked') ? '1' : '0';

        event.preventDefault();
        event.stopPropagation();

        if ($input.hasClass('submitting')) return;

        $input.addClass('submitting');
        $input.prop('disabled', true);

        if ($input.data('event-key')) {
            Pachno.listen($input.data('event-key'), () => {
                $input.removeClass('submitting');
                $input.prop('disabled', false);
            });
            Pachno.trigger($input.data('event-key'), $input);
        } else {
            Pachno.fetch($input.data('url'), {
                method: 'POST',
                data: { value }
            })
                .then((json) => {
                    $input.removeClass('submitting');
                    $input.prop('disabled', false);
                    // response.json().then(resolve);
                    // res = response;
                    // console.log(response);
                    // resolve($form, res);
                    // response.json()
                    //     .then(function (json) {
                    //     });
                })
        }

    });

    $body.on('submit', 'form[data-interactive-form]', (event) => submitInteractiveForm(event, $(event.target), true));
    $body.on('blur', 'form[data-interactive-form]:not(.submitting) input[type=text], form[data-interactive-form]:not(.submitting) textarea', (event) => submitInteractiveForm(event, $(event.target).parents('form')));
    $body.on('change', 'form[data-interactive-form]:not(.submitting) input[type=radio], form[data-interactive-form]:not(.submitting) input[type=checkbox]', (event) => submitInteractiveForm(event, $(event.target).parents('form')));
    $body.on('click', 'form[data-interactive-form]:not(.submitting) input[type=radio], form[data-interactive-form]:not(.submitting) input[type=checkbox]', (event) => submitInteractiveForm(event, $(event.target).parents('form')));

    $body.on('click', '.flexible-table .toggle-line', function (event) {
        event.preventDefault();
        event.stopPropagation();
        const $toggler = $(this);
        const $container = $toggler.parents('.line');
        const $next = $container.next().length ? $container.next() : $container.parents('.column').find('.line').first();
        $container.addClass('hidden');
        $next.removeClass('hidden');
    })
}

export default UI;
export {
    setupListeners
}