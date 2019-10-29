define(['prototype', 'effects', 'controls', 'scriptaculous', 'jquery', 'TweenMax', 'GSDraggable', 'notify', 'calendarview', 'jquery-ui', 'jquery.markitup', 'spectrum'],
    function (prototype, effects, controls, scriptaculous, jQuery, TweenMax, GSDraggable, Notify, Calendar) {

        var Pachno = {
            Core: {
                AjaxCalls: [],
                Pollers: {
                    Callbacks: {},
                    Locks: {}
                }
            }, // The "Core" namespace is for functions used by pachno core, not to be invoked outside the js class
            Tutorial: {
                Stories: {}
            },
            Main: {// The "Main" namespace contains regular functions in use across the site
                Helpers: {
                    Message: {},
                    Dialog: {},
                    Backdrop: {}
                },
                Profile: {},
                Notifications: {
                    Web: {}
                },
                Dashboard: {
                    views: [],
                    View: {}
                },
                Comment: {},
                Link: {},
                Menu: {},
                Login: {},
                parent_articles: []
            },
            Chart: {},
            Modules: {},
            Themes: {},
            Project: {
                Statistics: {},
                Milestone: {},
                Planning: {
                    Whiteboard: {}
                },
                Timeline: {},
                Scrum: {
                    Story: {},
                    Sprint: {}
                },
                Roles: {},
                Build: {},
                Component: {},
                Edition: {
                    Component: {}
                },
                Commits: {}
            },
            Config: {
                Permissions: {},
                Roles: {},
                User: {},
                Collection: {},
                Issuefields: {
                    Options: {},
                    Custom: {}
                },
                Issuetype: {
                    Choices: {}
                },
                IssuetypeScheme: {},
                Workflows: {
                    Workflow: {
                        Step: {}
                    },
                    Transition: {
                        Actions: {},
                        Validations: {}
                    },
                    Scheme: {}
                },
                Group: {},
                Team: {},
                Client: {},
                Import: {}
            }, // The "Config" namespace contains functions used in the configuration section
            Issues: {
                Link: {},
                File: {},
                Field: {
                    Updaters: {}
                },
                ACL: {},
                Affected: {}
            }, // The "Issues" namespace contains functions used in direct relation to issues
            Search: {
                Filter: {},
                ResultViews: {}
            }, // The "Search" namespace contains functions related to searching
            effect_queues: {
                successmessage: 'pachno_successmessage',
                failedmessage: 'pachno_failedmessage'
            },
            debug: false,
            autocompleter_url: undefined,
            autocompleter: undefined,
            available_fields: ['shortname', 'description', 'user_pain', 'reproduction_steps', 'category', 'resolution', 'priority', 'reproducability', 'percent_complete', 'severity', 'edition', 'build', 'component', 'estimated_time', 'spent_time', 'milestone', 'owned_by']
        };

        /**
         * Initializes the autocompleter
         */
        Pachno.Core._initializeAutocompleter = function () {
            if ($('searchfor') == null)
                return;
            Pachno.autocompleter = new Ajax.Autocompleter(
                "searchfor",
                "searchfor_autocomplete_choices",
                Pachno.autocompleter_url,
                {
                    paramName: "fs[text][v]",
                    parameters: "fs[text][o]==",
                    minChars: 2,
                    indicator: 'quicksearch_indicator',
                    callback: function (element, entry) {
                        $('quicksearch_submit').disable();
                        $('quicksearch_submit').removeClassName('button-blue');
                        $('quicksearch_submit').addClassName('button-silver');
                        return entry;
                    },
                    afterUpdateChoices: function () {
                        $('quicksearch_submit').enable();
                        $('quicksearch_submit').removeClassName('button-silver');
                        $('quicksearch_submit').addClassName('button-blue');
                    },
                    afterUpdateElement: Pachno.Core._extractAutocompleteValue,
                    onHide: function () {},
                    forceHide: function () {
                        new Effect.Fade($('searchfor_autocomplete_choices'),{duration:0.15});
                    }
                }
            );
        };

        /**
         * Helper function to extract url from autocomplete response container
         */
        Pachno.Core._extractAutocompleteValue = function (elem, value, event) {
            var elements = value.select('.url');
            if (elements.size() == 1 && value.select('.link').size() == 0) {
                window.location = elements[0].innerHTML.unescapeHTML();
                $('quicksearch_indicator').show();
                $('quicksearch_submit').disable();
                $('quicksearch_submit').removeClassName('button-blue');
                $('quicksearch_submit').addClassName('button-silver');
                $('searchfor').blur();
                $('searchfor').setValue('');
            } else {
                var cb_elements = value.select('.backdrop');
                if (cb_elements.size() == 1) {
                    var elm = cb_elements[0];
                    var backdrop_url = elm.down('.backdrop_url').innerHTML;
                    Pachno.Main.Helpers.Backdrop.show(backdrop_url);
                    $('searchfor').blur();
                    $('searchfor').setValue('');
                    event.stopPropagation();
                    event.preventDefault();
                }
            }
        };

        /**
         * Monitors viewport resize to adapt backdrops
         */
        Pachno.Core._resizeWatcher = function () {
            return;
            // Pachno.Core._vp_width = document.viewport.getWidth();
            // Pachno.Core._vp_height = document.viewport.getHeight();
            // if (($('attach_file') && $('attach_file').visible())) {
            //     var backdropheight = $('backdrop_detail_content').getHeight();
            //     if (backdropheight > (Pachno.Core._vp_height - 100)) {
            //         $('backdrop_detail_content').setStyle({height: Pachno.Core._vp_height - 100 + 'px', overflow: 'scroll'});
            //     } else {
            //         $('backdrop_detail_content').setStyle({height: 'auto', overflow: ''});
            //     }
            // }
            // Pachno.Core.popupVisiblizer();
        };

        Pachno.Core.popupVisiblizer = function () {
            return;
            // var visible_popups = $$('.dropdown_box').findAll(function (el) {
            //     return el.visible();
            // });
            // if (visible_popups.size()) {
            //     visible_popups.each(function (element) {
            //         if ($(element).hasClassName("user_dropdown"))
            //             return;
            //         var max_bottom = document.viewport.getHeight();
            //         var element_height = $(element).getHeight();
            //         var parent_offset = $(element).up().cumulativeOffset().top;
            //         var element_min_bottom = parent_offset + element_height + 35;
            //         if (max_bottom < element_min_bottom) {
            //             if ($(element).getStyle('position') != 'fixed') {
            //                 jQuery(element).data({'top': $(element).getStyle('top')});
            //             }
            //             $(element).setStyle({'position': 'fixed', 'bottom': '5px', 'top': 'auto'});
            //         } else {
            //             $(element).setStyle({'position': 'absolute', 'bottom': 'auto', 'top': jQuery(element).data('top')});
            //         }
            //     });
            // }
        };

        /**
         * Monitors viewport scrolling to adapt fixed positioners
         */
        Pachno.Core._scrollWatcher = function () {
            var vihc = $('viewissue_header_container');
            if (vihc) {
                var iv = $('issue_view');
                var y = document.viewport.getScrollOffsets().top;
                var compare_coord = (vihc.hasClassName('fixed')) ? iv.offsetTop - 15 : vihc.offsetTop;
                if (y >= compare_coord) {
                    $('issue-main-container').addClassName('scroll-top');
                    $('issue_details_container').addClassName('scroll-top');
                    vihc.addClassName('fixed');
                    $('workflow_actions').addClassName('fixed');
                    if ($('votes_additional').visible() && $('votes_additional').hasClassName('visible')) $('votes_additional').hide();
                    if ($('user_pain_additional').visible() && $('user_pain_additional').hasClassName('visible')) $('user_pain_additional').hide();
                    var vhc_layout = vihc.getLayout();
                    var vhc_height = vhc_layout.get('height') + vhc_layout.get('padding-top') + vhc_layout.get('padding-bottom');
                    if (y >= $('viewissue_comment_count').offsetTop) {
                        if ($('comment_add_button') != undefined && !$('comment_add_button').hasClassName('immobile')) {
                            var button = $('comment_add_button').remove();
                            $('workflow_actions').down('ul').insert(button);
                        }
                    } else if ($('comment_add_button') != undefined) {
                        var button = $('comment_add_button').remove();
                        $('add_comment_button_container').update(button);
                    }
                } else {
                    $('issue-main-container').removeClassName('scroll-top');
                    $('issue_details_container').removeClassName('scroll-top');
                    vihc.removeClassName('fixed');
                    $('workflow_actions').removeClassName('fixed');
                    if (! $('votes_additional').visible() && $('votes_additional').hasClassName('visible')) $('votes_additional').show();
                    if (! $('user_pain_additional').visible() && $('user_pain_additional').hasClassName('visible')) $('user_pain_additional').show();
                    if ($('comment_add_button') != undefined && !$('comment_add_button').hasClassName('immobile')) {
                        var button = $('comment_add_button').remove();
                        $('add_comment_button_container').update(button);
                    }
                }
            }
            if ($('search-bulk-action-form')) {
                var y = document.viewport.getScrollOffsets().top;
                var co = $('search-bulk-action-form').up('.bulk_action_container').cumulativeOffset();
                if (y >= co.top) {
                    $('search-bulk-action-form').addClassName('fixed');
                } else {
                    $('search-bulk-action-form').removeClassName('fixed');
                }
            }
            if ($('whiteboard')) {
                var y = document.viewport.getScrollOffsets().top;
                var co = $('whiteboard').cumulativeOffset();
                if (y >= co.top) {
                    $('whiteboard').addClassName('fixedheader');
                } else {
                    $('whiteboard').removeClassName('fixedheader');
                }
            }
            if ($('issues_paginator')) {
                var ip = $('issues_paginator');
                var ipl = ip.getLayout();
                var ip_height = ipl.get('height') + ipl.get('padding-top') + ipl.get('padding-bottom');

                var y = document.viewport.getScrollOffsets().top + document.viewport.getHeight();
                var y2 = $('body').scrollHeight;
                if (y >= y2 - ip_height) {
                    ip.removeClassName('visible');
                } else {
                    ip.addClassName('visible');
                }
            }
        };

        Pachno.Core._detachFile = function (url, file_id, base_id, loading_indicator) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: typeof(loading_indicator) != 'undefined' ? loading_indicator : base_id + file_id + '_remove_indicator',
                    hide: [base_id + file_id + '_remove_link', 'uploaded_files_' + file_id + '_remove_link'],
                    show: 'uploaded_files_' + file_id + '_remove_indicator'
                },
                success: {
                    remove: [base_id + file_id, 'uploaded_files_' + file_id, base_id + file_id + '_remove_confirm', 'uploaded_files_' + file_id + '_remove_confirm'],
                    callback: function (json) {
                        if (json.attachmentcount == 0 && $('viewissue_no_uploaded_files'))
                            $('viewissue_no_uploaded_files').show();
                        if ($('viewissue_uploaded_attachments_count'))
                            $('viewissue_uploaded_attachments_count').update(json.attachmentcount);
                        Pachno.Main.Helpers.Dialog.dismiss();
                    }
                },
                failure: {
                    show: [base_id + file_id + '_remove_link', 'uploaded_files_' + file_id + '_remove_link'],
                    hide: 'uploaded_files_' + file_id + '_remove_indicator'
                }
            });
        };

        Pachno.Core._processCommonAjaxPostEvents = function (options) {
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
                        $(options.enable).enable();
                } else {
                    options.enable.each(function (s) {
                        if ($(s))
                            $(s).enable();
                    });
                }
            }
            if (options.disable) {
                if (is_string(options.disable)) {
                    if ($(options.disable))
                        $(options.disable).disable();
                } else {
                    options.disable.each(function (s) {
                        if ($(s))
                            $(s).disable();
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
        };

        Pachno.Core._escapeWatcher = function (event) {
            if (Event.KEY_ESC != event.keyCode)
                return;
            Pachno.Main.Helpers.Backdrop.reset();
        };

        Pachno.Core.fetchPostHelper = function(form) {
            return new Promise(function (resolve, reject) {
                const $form = jQuery(form),
                    data = new FormData($form[0]);

                if ($form.hasClass('submitting')) return;

                $form.find('.error-container').removeClass('invalid');
                $form.find('.error-container > .error').html('');
                $form.addClass('submitting');
                $form.find('.button.primary').attr('disabled', true);

                fetch($form.attr('action'), {
                    method: 'POST',
                    body: data
                })
                    .then(function(response) {
                        resolve([$form, response]);
                        // response.json().then(resolve);
                        // res = response;
                        // console.log(response);
                        // resolve($form, res);
                        // response.json()
                        //     .then(function (json) {
                        //     });
                    })
                    .catch(reject)
            });
        };

        Pachno.Core.fetchPostDefaultFormHandler = function ([$form, response]) {
            return new Promise(function (resolve, reject) {
                if (!response.ok) {
                    response.json()
                        .then(function (json) {
                            $form.find('.error-container > .error').html(json.error);
                            $form.find('.error-container').addClass('invalid');
                            $form.removeClass('submitting');
                        })
                        .catch(reject);
                }

                resolve([$form, response]);
            });
        };

        /**
         * Main initializer function
         * Sets up and initializes autocompleters, watchers, etc
         *
         * @param {Object} options A {key: value} store with options to set
         */
        Pachno.initialize = function (options) {
            for (var key in options) {
                Pachno[key] = options[key];
            }
            Pachno.Core._initializeAutocompleter();
            Event.observe(window, 'resize', Pachno.Core._resizeWatcher);
            Event.observe(window, 'scroll', Pachno.Core._scrollWatcher);
            Pachno.Core._resizeWatcher();
            Pachno.Core._scrollWatcher();
            if ($$('.dashboard_view_container').size() > 0) {
                $$('.dashboard_view_container').each(function (view) {
                    Pachno.Main.Dashboard.View.init(parseInt(view.dataset.viewId));
                });
            } else {
                $$('html')[0].setStyle({'cursor': 'default'});
            }
            $('fullpage_backdrop_content').observe('click', Pachno.Core._resizeWatcher);
            document.observe('keydown', Pachno.Core._escapeWatcher);

            jQuery('body').on('change', '.fancy-dropdown input[type=checkbox]', Pachno.Main.updateFancyDropdownValues);
            jQuery('body').on('change', '.fancy-dropdown input[type=radio]', Pachno.Main.updateFancyDropdownValues);
            Pachno.Main.updateWidgets();

            Pachno.Core.Pollers.Callbacks.dataPoller();
            Pachno.Main.Profile.toggleNotifications(false);
            Pachno.OpenID.init();
            // Mimick browser scroll to element with id as hash once header get 'fixed' class
            // from _scrollWatcher method.
            setTimeout(function () {
                var hash = window.location.hash;
                if (hash != undefined && hash.indexOf('comment_') == 1 && typeof(window.location.href) == 'string') {
                    window.location.href = window.location.href;
                }
            }, 1000);
        };

        Pachno.Core.Pollers.Callbacks.dataPoller = function (toggled_notification_id) {
            if (!Pachno.Core.Pollers.Locks.datapoller) {
                Pachno.Core.Pollers.Locks.datapoller = true;
                Pachno.Main.Helpers.ajax(Pachno.data_url, {
                    url_method: 'get',
                    success: {
                        callback: function (json) {
                            var unc = $('user_notifications_count');
                            if (unc) {
                                if (parseInt(json.unread_notifications_count) != parseInt(unc.innerHTML)) {
                                    unc.update(json.unread_notifications_count);
                                    if (parseInt(json.unread_notifications_count) > 0) {
                                        unc.addClassName('unread');
                                    } else {
                                        unc.removeClassName('unread');
                                    }
                                }
                                Pachno.Main.Notifications.loadMore(undefined, true);
                            }
                            var un = $('user_notifications');
                            if (un) {
                                for (uni = 0; uni < json.unread_notifications.length; uni++) {
                                    var read_notification_is_unread = jQuery('.read[data-notification-id='+json.unread_notifications[uni]+']', un);

                                    if (read_notification_is_unread != null && ((toggled_notification_id != null && toggled_notification_id != read_notification_is_unread.data('notification_id')) || toggled_notification_id == null)) {
                                        read_notification_is_unread.removeClass('read');
                                        read_notification_is_unread.addClass('unread');
                                    }
                                }
                                un.select('.unread').each(function (li) {
                                    if (((toggled_notification_id != null && toggled_notification_id != li.dataset.notificationId) || toggled_notification_id == null) && json.unread_notifications.indexOf(li.dataset.notificationId) == -1) {
                                        li.removeClassName('unread');
                                        li.addClassName('read');
                                    }
                                });
                            }
                            Pachno.Core.Pollers.Locks.datapoller = false;
                            if (Pachno.Core.Pollers.datapoller != null)
                                Pachno.Core.Pollers.datapoller.stop();
                            var interval = parseInt(json.poll_interval);
                            Pachno.Core.Pollers.datapoller = interval > 0 ? new PeriodicalExecuter(Pachno.Core.Pollers.Callbacks.dataPoller, interval) : null;
                        }
                    },
                    exception: {
                        callback: function () {
                            Pachno.Core.Pollers.Locks.datapoller = false;
                        }
                    }
                });
            }
        };

        Pachno.Main.Profile.toggleNotifications = function (toggle_classes) {
            var un = $('user_notifications');
            var unc = $('user_notifications_container');
            if (! un || ! unc) return false;
            if (toggle_classes == null) toggle_classes = true;
            if (toggle_classes) unc.toggleClassName('active');
            if (un.hasClassName('active')) {
                un.removeClassName('active');
            } else {
                if (toggle_classes) un.addClassName('active');
                if ($('user_notifications_list').childElements().size() == 0) {
                    Pachno.Main.Helpers.ajax($('user_notifications_list').dataset.notificationsUrl, {
                        url_method: 'get',
                        loading: {
                            indicator: 'user_notifications_loading_indicator'
                        },
                        success: {
                            update: 'user_notifications_list',
                            callback: function () {
                                jQuery('#user_notifications_list_wrapper_nano').nanoScroller();
                                jQuery('#user_notifications_list_wrapper_nano').bind('scrollend', Pachno.Main.Notifications.loadMore);
                            }
                        }
                    });
                }
            }
        };

        Pachno.loadDebugInfo = function (debug_id, cb) {
            var url = Pachno.debugUrl.replace('___debugid___', debug_id);
            Pachno.Main.Helpers.ajax(url, {
                url_method: 'get',
                loading: {indicator: '___PACHNO_DEBUG_INFO___indicator'},
                success: {update: '___PACHNO_DEBUG_INFO___'},
                complete: {
                    callback: cb,
                    show: '___PACHNO_DEBUG_INFO___'
                },
                debug: false
            });
        };

        /**
         * Clears all popup messages from the effect queue
         */
        Pachno.Main.Helpers.Message.clear = function () {
            Effect.Queues.get(Pachno.effect_queues.successmessage).each(function (effect) {
                effect.cancel();
            });
            Effect.Queues.get(Pachno.effect_queues.failedmessage).each(function (effect) {
                effect.cancel();
            });
            if ($('pachno_successmessage').visible()) {
                $('pachno_successmessage').fade({duration: 0.2});
            }
            if ($('pachno_failuremessage').visible()) {
                $('pachno_failuremessage').fade({duration: 0.2});
            }
        };

        /**
         * Shows an error popup message
         *
         * @param title string The title to show
         * @param content string Error details
         */
        Pachno.Main.Helpers.Message.error = function (title, content) {
            $('pachno_failuremessage_title').update(title);
            $('pachno_failuremessage_content').update(content);
            if ($('pachno_successmessage').visible()) {
                Effect.Queues.get(Pachno.effect_queues.successmessage).each(function (effect) {
                    effect.cancel();
                });
                new Effect.Fade('pachno_successmessage', {queue: {position: 'end', scope: Pachno.effect_queues.successmessage, limit: 2}, duration: 0.2});
            }
            if ($('pachno_failuremessage').visible()) {
                Effect.Queues.get(Pachno.effect_queues.failedmessage).each(function (effect) {
                    effect.cancel();
                });
                new Effect.Pulsate('pachno_failuremessage', {duration: 1, pulses: 4});
            } else {
                new Effect.Appear('pachno_failuremessage', {queue: {position: 'end', scope: Pachno.effect_queues.failedmessage, limit: 2}, duration: 0.2});
            }
            new Effect.Fade('pachno_failuremessage', {queue: {position: 'end', scope: Pachno.effect_queues.failedmessage, limit: 2}, delay: 30, duration: 0.2});
        };

        /**
         * Shows a "success"-style popup message
         *
         * @param title string The title to show
         * @param content string Message details
         */
        Pachno.Main.Helpers.Message.success = function (title, content) {
            $('pachno_successmessage_title').update(title);
            $('pachno_successmessage_content').update(content);
            if (title || content) {
                if ($('pachno_failuremessage').visible()) {
                    Effect.Queues.get(Pachno.effect_queues.failedmessage).each(function (effect) {
                        effect.cancel();
                    });
                    new Effect.Fade('pachno_failuremessage', {queue: {position: 'end', scope: Pachno.effect_queues.failedmessage, limit: 2}, duration: 0.2});
                }
                if ($('pachno_successmessage').visible()) {
                    Effect.Queues.get(Pachno.effect_queues.successmessage).each(function (effect) {
                        effect.cancel();
                    });
                    new Effect.Pulsate('pachno_successmessage', {duration: 1, pulses: 4});
                } else {
                    new Effect.Appear('pachno_successmessage', {queue: {position: 'end', scope: Pachno.effect_queues.successmessage, limit: 2}, duration: 0.2});
                }
                new Effect.Fade('pachno_successmessage', {queue: {position: 'end', scope: Pachno.effect_queues.successmessage, limit: 2}, delay: 10, duration: 0.2});
            } else if ($('pachno_successmessage').visible()) {
                Effect.Queues.get(Pachno.effect_queues.successmessage).each(function (effect) {
                    effect.cancel();
                });
                new Effect.Fade('pachno_successmessage', {queue: {position: 'end', scope: Pachno.effect_queues.successmessage, limit: 2}, duration: 0.2});
            }
        };

        Pachno.Main.Helpers.Dialog.show = function (title, content, options) {
            Pachno.Main.Helpers.Message.clear();
            $('dialog_title').update(title);
            $('dialog_content').update(content);
            $('dialog_yes').setAttribute('href', 'javascript:void(0)');
            $('dialog_no').setAttribute('href', 'javascript:void(0)');
            $('dialog_yes').stopObserving('click');
            $('dialog_no').stopObserving('click');
            $('dialog_yes').removeClassName('disabled');
            $('dialog_no').removeClassName('disabled');
            if (options.yes.click) {
                $('dialog_yes').observe('click', options.yes.click);
            }
            if (options.yes.href) {
                $('dialog_yes').setAttribute('href', options.yes.href);
            }
            if (options.no.click) {
                $('dialog_no').observe('click', options.no.click);
            }
            if (options.no.href) {
                $('dialog_no').setAttribute('href', options.no.href);
            }
            $('dialog_backdrop_content').show();
            $('dialog_backdrop').appear({duration: 0.2});
        };
        Pachno.Main.Helpers.Dialog.showModal = function (title, content) {
            Pachno.Main.Helpers.Message.clear();
            $('dialog_modal_title').update(title);
            $('dialog_modal_content').update(content);
            $('dialog_backdrop_modal_content').show();
            $('dialog_backdrop_modal').appear({duration: 0.2});
        };

        Pachno.Main.Helpers.Dialog.dismiss = function () {
            $('dialog_backdrop_content').fade({duration: 0.2});
            $('dialog_backdrop').fade({duration: 0.2});
        };
        Pachno.Main.Helpers.Dialog.dismissModal = function () {
            $('dialog_backdrop_modal_content').fade({duration: 0.2});
            $('dialog_backdrop_modal').fade({duration: 0.2});
        };

        /**
         * Convenience function for running an AJAX call and updating / showing / hiding
         * divs on json feedback
         *
         * Available options:
         *   loading: {} Instructions for the onLoading event
         *   success: {} Instructions for the onSuccess event
         *   failure: {} Instructions for the onComplete event
         *   complete: {} Instructions for the onComplete event
         *
         *   Common options for all on* events:
         *     hide: string/array A list of / element id(s) to hide
         *     reset: string/array A list of / element id(s) to reset
         *     show: string/array A list of / element id(s) to show
         *     clear: string/array A list of / element id(s) to clear
         *     remove: string/array A list of / element id(s) to remove
         *     enable: string/array A list of / element id(s) to enable
         *     disable: string/array A list of / element id(s) to disable
         *     callback: a function to call at the end of the event. For
         *		         success/failure/complete events, the callback
         *		         function retrieves the json object
         *
         *   The loading.indicator element will be toggled off in the onComplete event
         *
         *   Options for the onSuccess event instruction set:
         *     update: either an element id which will receive the value of the
         *             json.content property or an object with instructions:
         *     replace: either an element id which will be replace with the value of the
         *             json.content property or an object with instructions:
         *
         *     Available instructions for the success "update" object:
         *       element: the id of the element to update
         *       insertion: true / false / ommitted. If "true" the element will get the
         *                  content inserted after the existing content, instead of
         *                  the content replacing the existing content
         *       from: if the json return value does not contain a "content" key,
         *			   specify which json key should be used
         *
         * @param url The URL to call
         * @param options An associated array of options
         */
        Pachno.Main.Helpers.ajax = function (url, options) {
            var params = (options.params) ? options.params : '';
            if (options.form && options.form != undefined)
                params = Form.serialize(options.form);
            if (options.additional_params)
                params += options.additional_params;
            var url_method = (options.url_method) ? options.url_method : 'post';
            var $form = (options.form) ? jQuery('#' + options.form) : undefined;

            new Ajax.Request(url, {
                asynchronous: true,
                method: url_method,
                parameters: params,
                evalScripts: true,
                onLoading: function () {
                    if (options.loading) {
                        if (Pachno.debug) {
                            $('___PACHNO_DEBUG_INFO___indicator').show();
                        }
                        if ($(options.loading.indicator)) {
                            $(options.loading.indicator).show();
                        }
                        if ($(options.loading.disable)) {
                            $(options.loading.disabled).disable();
                        }
                        Pachno.Core._processCommonAjaxPostEvents(options.loading);
                        if (options.loading.callback) {
                            options.loading.callback();
                        }
                    }
                    if ($form !== undefined) {
                        $form.addClass('submitting');
                        $form.find('button[type=submit]').each(function () {
                            var $button = jQuery(this);
                            $button.addClass('auto-disabled');
                            $button.attr("disabled", true);
                        })
                    }
                },
                onSuccess: function (response) {
                    if (response.responseJSON == null && JSON != null) {
                        try {
                            var json = JSON.parse(response.responseText);
                        }
                        catch (err) {
                            var json = undefined;
                        }
                    }
                    else {
                        var json = (response.responseJSON) ? response.responseJSON : undefined;
                    }
                    if (json || (options.success && options.success.update)) {
                        if (json && json.forward != undefined) {
                            document.location = json.forward;
                        } else {
                            if (options.success && options.success.update) {
                                var json_content_element = (is_string(options.success.update) || options.success.update.from == undefined) ? 'content' : options.success.update.from;
                                var content = (json) ? json[json_content_element] : response.responseText;
                                var update_element = (is_string(options.success.update)) ? options.success.update : options.success.update.element;
                                if ($(update_element)) {
                                    var insertion = (is_string(options.success.update)) ? false : (options.success.update.insertion) ? options.success.update.insertion : false;
                                    if (insertion) {
                                        $(update_element).insert(content, 'after');
                                    } else {
                                        $(update_element).update(content);
                                    }
                                }
                                if (json && json.message) {
                                    Pachno.Main.Helpers.Message.success(json.message);
                                }
                            } else if (options.success && options.success.replace) {
                                var json_content_element = (is_string(options.success.replace) || options.success.replace.from == undefined) ? 'content' : options.success.replace.from;
                                var content = (json) ? json[json_content_element] : response.responseText;
                                var replace_element = (is_string(options.success.replace)) ? options.success.replace : options.success.replace.element;
                                if ($(replace_element)) {
                                    Element.replace(replace_element, content);
                                }
                                if (json && json.message) {
                                    Pachno.Main.Helpers.Message.success(json.message);
                                }
                            } else if (json && (json.title || json.content)) {
                                Pachno.Main.Helpers.Message.success(json.title, json.content);
                            } else if (json && (json.message)) {
                                Pachno.Main.Helpers.Message.success(json.message);
                            }
                            if (options.success) {
                                Pachno.Core._processCommonAjaxPostEvents(options.success);
                                if (options.success.callback) {
                                    options.success.callback(json);
                                }
                            }
                        }
                    }
                },
                onFailure: function (response) {
                    if (response.responseJSON == null && JSON != null) {
                        response.responseJSON = JSON.parse(response.responseText);
                    }
                    Pachno.clearFormSubmit($form);
                    var json = (response.responseJSON) ? response.responseJSON : undefined;
                    if (response.responseJSON && (json.error || json.message)) {
                        Pachno.Main.Helpers.Message.error(json.error, json.message);
                    } else if (response.responseText) {
                        Pachno.Main.Helpers.Message.error(response.responseText);
                    }
                    if (options.failure) {
                        Pachno.Core._processCommonAjaxPostEvents(options.failure);
                        if (options.failure.callback) {
                            options.failure.callback(json);
                        }
                    }
                },
                onException: function (response) {
                    var json = (response.responseJSON) ? response.responseJSON : undefined;
                    if (response.responseJSON && (json.error || json.message)) {
                        Pachno.Main.Helpers.Message.error(json.error, json.message);
                    } else if (response.responseText) {
                        Pachno.Main.Helpers.Message.error(response.responseText);
                    }
                    if (options.exception) {
                        Pachno.Core._processCommonAjaxPostEvents(options.exception);
                        if (options.exception.callback) {
                            options.exception.callback(json);
                        }
                    }
                    Pachno.clearFormSubmit($form);
                },
                onComplete: function (response) {
                    if (Pachno.debug) {
                        $('___PACHNO_DEBUG_INFO___indicator').hide();
                        var d = new Date(),
                            d_id = response.getHeader('x-pachno-debugid'),
                            d_time = response.getHeader('x-pachno-loadtime'),
                            d_session_time = response.getHeader('x-pachno-sessiontime'),
                            d_calculated_time = response.getHeader('x-pachno-calculatedtime');

                        Pachno.Core.AjaxCalls.push({location: url, time: d, debug_id: d_id, loadtime: d_time, session_loadtime: d_session_time, calculated_loadtime: d_calculated_time });
                        Pachno.updateDebugInfo();
                    }
                    if (options.loading) {
                        $(options.loading.indicator).hide();
                        if ($(options.loading.disable)) {
                            $(options.loading.disabled).enable();
                        }
                    }
                    if (options.complete) {
                        Pachno.Core._processCommonAjaxPostEvents(options.complete);
                        if (options.complete.callback) {
                            var json = (response.responseJSON) ? response.responseJSON : undefined;
                            options.complete.callback(json);
                        }
                    }
                    Pachno.Main.updateWidgets();
                }
            });
        };

        Pachno.clearFormSubmit = function ($form) {
            if ($form !== undefined) {
                $form.removeClass('submitting');
                $form.find('button[type=submit].auto-disabled').each(function () {
                    var $button = jQuery(this);
                    $button.prop("disabled", false);
                    $button.removeClass('auto-disabled');
                })
            }
        };

        Pachno.updateDebugInfo = function () {
            var lai = $('log_ajax_items');
            if (lai) {
                $('log_ajax_items').update('');
                if ($('debug_ajax_count'))
                    $('debug_ajax_count').update(Pachno.Core.AjaxCalls.size());
                var ct = function (time) {
                    return (time < 10) ? '0' + time : time;
                };
                Pachno.Core.AjaxCalls.each(function (info) {
                    var content = '<li><span class="badge timestamp">' + ct(info.time.getHours()) + ':' + ct(info.time.getMinutes()) + ':' + ct(info.time.getSeconds()) + '.' + ct(info.time.getMilliseconds()) + '</span><span class="badge timing"><i class="far fa-clock"></i>' + info.loadtime + '</span><span class="badge timing session" title="Time spent by php loading session data"><i class="far fa-hdd"></i>' + info.session_loadtime + '</span><span class="badge timing calculated" title="Calculated load time, excluding session load time"><i class="fa fa-calculator"></i>' + info.calculated_loadtime + '</span><span class="partial">' + info.location + '</span> <a class="button" style="float: right;" href="javascript:void(0);" onclick="Pachno.loadDebugInfo(\'' + info.debug_id + '\');">Debug</a></li>';
                    lai.insert(content, 'top');
                });
            }
        };

        Pachno.Main.Helpers.formSubmit = function (url, form_id) {
            Pachno.Main.Helpers.ajax(url, {
                form: form_id,
                loading: {indicator: form_id + '_indicator', disable: form_id + '_button'},
                success: {enable: form_id + '_button'},
                failure: {enable: form_id + '_button'}
            });
        };

        Pachno.Main.Helpers.Backdrop.show = function (url, callback) {
            $('fullpage_backdrop_content').fade({duration: 0});
            $('fullpage_backdrop').appear({duration: 0.2});
            $$('body')[0].setStyle({'overflow': 'hidden'});
            $('fullpage_backdrop_indicator').show();

            if (url != undefined) {
                Pachno.Main.Helpers.ajax(url, {
                    url_method: 'get',
                    loading: {indicator: 'fullpage_backdrop_indicator'},
                    success: {
                        update: 'fullpage_backdrop_content',
                        callback: function () {
                            $('fullpage_backdrop_content').appear({duration: 0.2});
                            $('fullpage_backdrop_indicator').fade({duration: 0.2});
                            Pachno.Main.Helpers.MarkitUp($$('textarea.markuppable'));
                            if (callback)
                                setTimeout((callback)(), 300);
                        }},
                    failure: {hide: 'fullpage_backdrop'}
                });
            }
        };

        Pachno.Main.Helpers.Backdrop.reset = function (callback) {
            $$('body')[0].setStyle({'overflow': 'auto'});
            $('fullpage_backdrop').fade({duration: 0.2});
            Pachno.Core._resizeWatcher();
            if (callback) callback();
        };

        Pachno.Main.Helpers.tabSwitcher = function (visibletab, menu, change_hash) {
            if (change_hash == null) change_hash = false;

            if ($(menu)) {
                $(menu).childElements().each(function (item) {
                    item.removeClassName('selected');
                });
                if ($(visibletab)) {
                    $(visibletab).addClassName('selected');
                    $(menu + '_panes').childElements().each(function (item) {
                        item.hide();
                    });
                }
                if ($(visibletab + '_pane')) {
                    $(visibletab + '_pane').show();
                }
                if (change_hash) {
                    if (history.replaceState) {
                        window.history.replaceState(null, null, '#' + visibletab);
                    }
                    else {
                        window.location.hash = visibletab;
                    }
                }
            }
        };

        Pachno.Main.Helpers.tabSwitchFromHash = function (menu) {
            var hash = window.location.hash;

            if (hash != undefined && hash.indexOf('tab_') == 1) {
                Pachno.Main.Helpers.tabSwitcher(hash.substr(1), menu);
            }
        };

        Pachno.Main.Helpers.MarkitUp = function (element) {
            var elements = (element.hasClassName) ? [element] : element;

            elements.each(function (elm) {
                if ($(elm).hasClassName('syntax_mw')) {
                    var ms = [
                        {name: 'Headings', dropMenu: [
                                {name: 'Heading 1', key: '1', openWith: '== ', closeWith: ' ==', placeHolder: 'Your title here...'},
                                {name: 'Heading 2', key: '2', openWith: '=== ', closeWith: ' ===', placeHolder: 'Your title here...'},
                                {name: 'Heading 3', key: '3', openWith: '==== ', closeWith: ' ====', placeHolder: 'Your title here...'},
                                {name: 'Heading 4', key: '4', openWith: '===== ', closeWith: ' =====', placeHolder: 'Your title here...'},
                                {name: 'Heading 5', key: '5', openWith: '====== ', closeWith: ' ======', placeHolder: 'Your title here...'}
                            ]
                        },
                        {separator: '---------------'},
                        {name: '<i class="fas fa-bold"></i>', title:'Bold', key: 'B', openWith: "'''", closeWith: "'''"},
                        {name: '<i class="fas fa-italic"></i>', title: 'Italic', key: 'I', openWith: "''", closeWith: "''"},
                        {name: '<i class="fas fa-strikethrough"></i>', title: 'Strike through', key: 'S', openWith: '<strike>', closeWith: '</strike>'},
                        {separator: '---------------'},
                        {name: '<i class="fas fa-list-ul"></i>', title: 'Bulleted list', openWith: '(!(* |!|*)!)'},
                        {name: '<i class="fas fa-list-ol"></i>', title: 'Numeric list', openWith: '(!(# |!|#)!)'},
                        {separator: '---------------'},
                        {name: '<i class="fas fa-link"></i>', title: 'Attach', dropMenu: [
                                {name: 'Simple link', openWith: "[[![Url:!:http://]!] ", closeWith: ']', placeHolder: 'Your text to link here...'},
                                {name: 'Link with title', key: 'L', openWith: "[[[![Url:!:http://]!]|", closeWith: ']]', placeHolder: 'Your text to link here...'},
                                {name: 'Link to picture', key: 'P', replaceWith: '[[Image:[![Url:!:http://]!]|[![name]!]]]'},
                            ]},
                        {separator: '---------------'},
                        {name: '<i class="fas fa-quote-right"></i>', title: 'Quotes', openWith: '(!(> |!|>)!)', placeHolder: ''},
                        {name: '<i class="fas fa-code"></i>', title: 'Code', openWith: '(!(<source lang="[![Language:!:php]!]">|!|<pre>)!)', closeWith: '(!(</source>|!|</pre>)!)'}
                    ];
                } else {
                    var ms = [
                        {name: '<i class="fas fa-heading"></i>', title: 'Headings', dropMenu: [
                                {name: 'Heading 1', key: '1', placeHolder: 'Your title here...', closeWith: function (markItUp) {
                                    return Pachno.Main.Helpers.miu.markdownTitle(markItUp, '=')
                                }},
                                {name: 'Heading 2', key: '2', placeHolder: 'Your title here...', closeWith: function (markItUp) {
                                    return Pachno.Main.Helpers.miu.markdownTitle(markItUp, '-')
                                }},
                                {name: 'Heading 3', key: '3', openWith: '### ', placeHolder: 'Your title here...'},
                                {name: 'Heading 4', key: '4', openWith: '#### ', placeHolder: 'Your title here...'},
                                {name: 'Heading 5', key: '5', openWith: '##### ', placeHolder: 'Your title here...'},
                            ]
                        },
                        {separator: '---------------'},
                        {name: '<i class="fas fa-bold"></i>', title:'Bold', key: 'B', openWith: '**', closeWith: '**'},
                        {name: '<i class="fas fa-italic"></i>', title: 'Italic', key: 'I', openWith: '_', closeWith: '_'},
                        {name: '<i class="fas fa-strikethrough"></i>', title: 'Strike through', key: 'S', openWith: '~~', closeWith: '~~'},
                        {separator: '---------------'},
                        {name: '<i class="fas fa-list-ul"></i>', title: 'Bulleted List', openWith: '- '},
                        {name: '<i class="fas fa-list-ol"></i>', title: 'Numeric List', openWith: function (markItUp) {
                            return markItUp.line + '. ';
                        }},
                        {separator: '---------------'},
                        {name: '<i class="fas fa-link"></i>', title: 'Attach', dropMenu: [
                            {name: 'Simple link', openWith: '[', closeWith: ']([![Url:!:http://]!])', placeHolder: 'Your text to link here...'},
                            {name: 'Link with title', key: 'L', openWith: '[', closeWith: ']([![Url:!:http://]!] "[![Title]!]")', placeHolder: 'Your text to link here...'},
                            {name: 'Link to picture', key: 'P', replaceWith: '![[![Alternative text]!]]([![Url:!:http://]!] "[![Title]!]")'},
                        ]},
                        {separator: '---------------'},
                        {name: '<i class="fas fa-quote-right"></i>', title: 'Quotes', openWith: '> '},
                        {name: '<i class="fas fa-code"></i>', title: 'Code', openWith: '(!(\t|!|`)!)', closeWith: '(!(`)!)'}
                    ];
                }
                jQuery(elm).markItUpRemove();
                jQuery(elm).markItUp({
                    previewParserPath: '', // path to your Wiki parser
                    onShiftEnter: {keepDefault: false, replaceWith: '\n\n'},
                    markupSet: ms
                });
            });
        };

    // mIu nameSpace to avoid conflict.
        Pachno.Main.Helpers.miu = {
            markdownTitle: function (markItUp, char) {
                heading = '';
                n = jQuery.trim(markItUp.selection || markItUp.placeHolder).length;
                for (i = 0; i < n; i++) {
                    heading += char;
                }
                return '\n' + heading + '\n';
            }
        };

        Pachno.Main.Helpers.setSyntax = function (base_id, syntax) {
            var ce = $(base_id);
            var cec = $(base_id).up('.textarea_container');

            ['mw', 'md', 'pt'].each(function (sntx) {
                ce.removeClassName('syntax_' + sntx);
                cec.removeClassName('syntax_' + sntx);
            });

            ce.addClassName('syntax_' + syntax);
            cec.addClassName('syntax_' + syntax);

            $(base_id + '_syntax').setValue(syntax);

            $(base_id + '_syntax_picker').childElements().each(function (elm) {
                if (elm.hasClassName(syntax)) {
                    elm.addClassName('selected');
                    $(base_id + '_selected_syntax').update(elm.dataset.syntaxName);
                } else {
                    elm.removeClassName('selected');
                }
            });

            Pachno.Main.Helpers.MarkitUp(ce);
        };

        Pachno.Main.findIdentifiable = function (url, field) {
            Pachno.Main.Helpers.ajax(url, {
                form: field + '_form',
                loading: {indicator: field + '_spinning'},
                success: {
                    update: field + '_results',
                    show: field + '_results_container'
                }
            });
        };

        Pachno.Main.updatePercentageLayout = function (arg1, arg2) {
            if (isNaN(arg1))
            {
                $(arg1).style.width = arg2 + "%";
            } else {
                $('percent_complete_content').select('.percent_filled').first().style.width = arg1 + '%';
            }
        };

        Pachno.Main.showUploader = function (url) {
            if (window.File && window.FileList && window.FileReader) {
                url += '&uploader=dynamic';
            } else {
                url += '&uploader=legacy';
            }
            Pachno.Main.Helpers.Backdrop.show(url);
        };

        Pachno.Main.updateAttachments = function (form) {
            var url = form.action;
            Pachno.Main.Helpers.ajax(url, {
                form: form,
                url_method: 'post',
                loading: {
                    indicator: 'attachments_indicator',
                    callback: function () {
                        $('dynamic_uploader_submit').addClassName('disabled');
                        $('dynamic_uploader_submit').disable();
                        $('report_issue_submit_button').addClassName('disabled');
                        $('report_issue_submit_button').disable();
                    }
                },
                success: {
                    callback: function (json) {
                        Pachno.Main.Helpers.Backdrop.reset();
                        var base = $(json.container_id);
                        if (base !== undefined) {
                            base.update('');
                            json.files.each(function (file_elm) {
                                base.insert(file_elm);
                            });
                            if (json.files.length) {
                                if ($('viewissue_uploaded_attachments_count')) $('viewissue_uploaded_attachments_count').update(json.files.length);
                                $('viewissue_no_uploaded_files').hide();
                            }
                        }
                        $('comments_box').insert({top: json.comments});
                    }
                },
                complete: {
                    callback: function () {
                        $('dynamic_uploader_submit').addClassName('disabled');
                        $('dynamic_uploader_submit').enable();
                        $('report_issue_submit_button').addClassName('disabled');
                        $('report_issue_submit_button').enable();
                    }
                }
            });

        };

        Pachno.Main.uploadFile = function (url, file, is_last) {
            var is_last = is_last != undefined ? is_last : true;
            var fileSize = 0;
            if (file.size > 1024 * 1024) {
                fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
            } else {
                fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';
            }
            var ful = $('file_upload_list');
            var elm = '<li><span class="imagepreview"><img src="' + ful.dataset.previewSrc + '"></span>';
            var isimage = false;
            if (file.type.indexOf("image") == 0) {
                isimage = true;
            }
            elm += '<label>' + ful.dataset.filenameLabel + '</label><span class="filename">' + file.name + '</span> <span class="filesize">' + fileSize + '</span><br><label>' + ful.dataset.descriptionLabel + '</label><input type="text" class="file_description" value="" placeholder="' + ful.dataset.descriptionPlaceholder + '"> <div class="progress_container"><span class="progress"></span></div></li>';
            ful.insert({top: elm});
            var inserted_elm = $('file_upload_list').childElements().first();
            if (isimage) {
                var image_elm = inserted_elm.down('img');
                var reader = new FileReader();
                reader.onload = function (e) {
                    image_elm.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
            var progress_elm = inserted_elm.down('.progress');
            var formData = new FormData();
            formData.append(file.name.replace('[', '(').replace(']', ')'), file);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', url, true);
            xhr.onload = function (e) {
                var data = JSON.parse(this.response);
                if (data.file_id != undefined) {
                    inserted_elm.insert('<input type="hidden" name="files[' + data.file_id + ']" value="' + data.file_id + '">');
                    inserted_elm.down('.file_description').name = "file_description[" + data.file_id + ']';
                } else {
                    inserted_elm.remove();
                    Pachno.Main.Helpers.Message.error(json.error);
                }
                if (is_last && $('dynamic_uploader_submit') && $('dynamic_uploader_submit').disabled) $('dynamic_uploader_submit').enable();
                if (is_last && $('report_issue_submit_button') && $('report_issue_submit_button').disabled) $('report_issue_submit_button').enable();
            };

            xhr.upload.onprogress = function (e) {
                if (e.lengthComputable) {
                    var percent = (e.loaded / e.total) * 100;
                    progress_elm.setStyle({width: percent + '%'});
                    if (percent == 100) {
                        progress_elm.addClassName('completed');
    //					progressBar.textContent = progressBar.value; // Fallback for unsupported browsers.
                        $('file_upload_dummy').value = null;
                    }
                }
            };

            if ($('dynamic_uploader_submit') && !$('dynamic_uploader_submit').disabled) $('dynamic_uploader_submit').disable();
            if ($('report_issue_submit_button') && !$('report_issue_submit_button').disabled) $('report_issue_submit_button').disable();
            xhr.send(formData);
        };

        Pachno.Main.selectFiles = function (elm) {
            var files = $(elm).files;
            var url = elm.dataset.uploadUrl;
            if (files.length > 0) {
                for (var i = 0, file; file = files[i]; i++) {
                    Pachno.Main.uploadFile(url, file, i == files.length - 1);
                }
            }
        };

        Pachno.Main.dragOverFiles = function (evt) {
            evt.stopPropagation();
            evt.preventDefault();
            if (evt.type == "dragover") {
                $(evt.target).addClassName("file_hover");
            } else {
                $(evt.target).removeClassName("file_hover");
            }
            evt.dataTransfer.dropEffect = 'copy'; // Explicitly show this is a copy.
        };

        Pachno.Main.dropFiles = function (evt) {
            var elm = $('file_upload_dummy');
            var url = elm.dataset.uploadUrl;
            var files = evt.target.files || evt.dataTransfer.files;
            Pachno.Main.dragOverFiles(evt);
            if (files.length > 0) {
                for (var i = 0, file; file = files[i]; i++) {
                    Pachno.Main.uploadFile(url, file, i == files.length - 1);
                }
            }
        };

        Pachno.Main.submitIssue = function (url) {
            if ($('report_issue_submit_button').hasClassName('disabled') || $('report_issue_submit_button').hasAttribute('disabled'))
                return;

            $('report_issue_submit_button').addClassName('disabled');
            $('report_issue_submit_button').writeAttribute('disabled', true);

            Pachno.Main.Helpers.ajax(url, {
                form: 'report_issue_form',
                url_method: 'post',
                loading: {
                    indicator: 'report_issue_indicator'
                },
                success: {
                    update: 'fullpage_backdrop_content',
                    callback: function () {
                        $('reportissue_container').removeClassName('large');
                        $('reportissue_container').removeClassName('huge');
                    }
                },
                complete: {
                    callback: function () {
                        $('report_issue_submit_button').removeClassName('disabled');
                        $('report_issue_submit_button').writeAttribute('disabled', false);
                    }
                }
            });
        };

        Pachno.Main.Link.add = function (url, target_type, target_id) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'attach_link_' + target_type + '_' + target_id + '_form',
                loading: {
                    indicator: 'attach_link_' + target_type + '_' + target_id + '_indicator',
                    callback: function () {
                        $('attach_link_' + target_type + '_' + target_id + '_submit').disable();
                    }
                },
                success: {
                    reset: 'attach_link_' + target_type + '_' + target_id + '_form',
                    hide: ['attach_link_' + target_type + '_' + target_id, target_type + '_' + target_id + '_no_links'],
                    update: {element: target_type + '_' + target_id + '_links', insertion: true},
                    callback: function () {
                        if ($(target_type + '_' + target_id + '_container').hasClassName('menu_editing')) {
                            jQuery('#toggle_' + target_type + '_' + target_id +'_edit_mode').trigger('click');
                            jQuery('#toggle_' + target_type + '_' + target_id +'_edit_mode').trigger('click');
                        }
                    }
                },
                complete: {
                    callback: function () {
                        $('attach_link_' + target_type + '_' + target_id + '_submit').enable();
                    }
                }
            });
        };

        Pachno.Main.Link.remove = function (url, target_type, target_id, link_id) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    hide: target_type + '_' + target_id + '_links_' + link_id + '_remove_link',
                    indicator: 'dialog_indicator'
                },
                success: {
                    remove: [target_type + '_' + target_id + '_links_' + link_id, target_type + '_' + target_id + '_links_' + link_id + '_remove_confirm'],
                    callback: function (json) {
                        Pachno.Main.Helpers.Dialog.dismiss();
                        if ($(json.target_type + '_' + json.target_id + '_links').childElements().size() == 0) {
                            $(json.target_type + '_' + json.target_id + '_no_links').show();
                        }
                    }
                },
                failure: {
                    show: target_type + '_' + target_id + '_links_' + link_id + '_remove_link'
                }
            });
        };

        Pachno.Main.Menu.toggleEditMode = function (target_type, target_id, url) {
            if ($(target_type + '_' + target_id + '_container').hasClassName('menu_editing')) {
                Sortable.destroy(target_type + '_' + target_id + '_links');
            } else {
                Sortable.create(target_type + '_' + target_id + '_links', {constraint: '', onUpdate: function (container) {
                    Pachno.Main.Menu.saveOrder(container, target_type, target_id, url);
                }});
            }
            $(target_type + '_' + target_id + '_container').toggleClassName('menu_editing');
        };

        Pachno.Main.Menu.saveOrder = function (container, target_type, target_id, url) {
            Pachno.Main.Helpers.ajax(url, {
                additional_params: Sortable.serialize(container),
                loading: {
                    indicator: target_type + '_' + target_id + '_indicator'
                }
            });
        };

        Pachno.Main.detachFileFromArticle = function (url, file_id, article_id) {
            Pachno.Core._detachFile(url, file_id, 'article_' + article_id + '_files_', 'dialog_indicator');
        };

        Pachno.Main.toggleFavouriteArticle = function (url, article_id)
        {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'article_favourite_indicator_' + article_id,
                    hide: ['article_favourite_normal_' + article_id, 'article_favourite_faded_' + article_id]
                },
                success: {
                    callback: function (json) {
                        if ($('article_favourite_faded_' + article_id)) {
                            if (json.starred) {
                                $('article_favourite_faded_' + article_id).hide();
                                $('article_favourite_indicator_' + article_id).hide();
                                $('article_favourite_normal_' + article_id).show();
                            } else {
                                $('article_favourite_normal_' + article_id).hide();
                                $('article_favourite_indicator_' + article_id).hide();
                                $('article_favourite_faded_' + article_id).show();
                            }
                        } else if (json.subscriber != '') {
                            $('subscribers_list').insert(json.subscriber);
                        }
                    }
                }
            });
        };

        Pachno.Main.deleteArticle = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                method: 'post',
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop']
                },
                success: {
                    callback: function () {
                        location.reload();
                    }
                }
            });
        };

        Pachno.Main.reloadImage = function (id) {
            var src = $(id).src;
            var date = new Date();

            src = (src.indexOf('?') != -1) ? src.substr(0, pos) : src;
            $(id).src = src + '?v=' + date.getTime();

            return false;
        };

        Pachno.Main.Profile.updateInformation = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'profile_information_form',
                loading: {indicator: 'profile_save_indicator'},
                success: {callback: function () {
                    ($('profile_use_gravatar_yes').checked) ? $('gravatar_change').show() : $('gravatar_change').hide();
                }}
            });
        };

        Pachno.Main.Profile.updateModuleSettings = function (url, module_name) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'profile_' + module_name + '_form',
                loading: {indicator: 'profile_' + module_name + '_save_indicator'}
            });
        };

        Pachno.Main.Profile.updateSettings = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'profile_settings_form',
                loading: {indicator: 'profile_settings_save_indicator'}
            });
        };

        Pachno.Main.Profile.updateNotificationSettings = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'profile_notificationsettings_form',
                loading: {indicator: 'profile_notificationsettings_save_indicator'}
            });
        };

        Pachno.Main.Profile.changePassword = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'change_password_form',
                loading: {indicator: 'change_password_indicator'},
                success: {reset: 'change_password_form', hide: 'change_password_div'}
            });
        };

        Pachno.Main.Profile.addApplicationPassword = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'add_application_password_form',
                loading: {indicator: 'add_application_password_indicator'},
                success: {
                    hide: 'add_application_password_container',
                    update: {element: 'application_password_preview', from: 'password'},
                    show: 'add_application_password_response'
                }
            });
        };

        Pachno.Main.Profile.removeApplicationPassword = function (url, p_id) {
            Pachno.Main.Helpers.ajax(url, {
                method: 'post',
                loading: {
                    callback: function () {
                        $('application_password_' + p_id).down('button').disable();
                    }
                },
                success: {
                    remove: 'application_password_' + p_id,
                    callback: function () {
                        Pachno.Main.Helpers.Dialog.dismiss();
                    }
                },
                failure: {
                    callback: function () {
                        $('application_password_' + p_id).down('button').enable();
                    }
                }
            });
        };

        Pachno.Main.Profile.checkUsernameAvailability = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'check_username_form',
                loading: {
                    indicator: 'pick_username_indicator',
                    hide: 'username_unavailable'
                },
                complete: {
                    callback: function (json) {
                        if (json.available) {
                            Pachno.Main.Helpers.Backdrop.show(json.url);
                        } else {
                            $('username_unavailable').show();
                            $('username_unavailable').pulsate({pulses: 3, duration: 1});
                        }
                    }
                }
            });
        };

        Pachno.Main.Profile.toggleNotificationSettings = function (preset) {
            if (preset == 'custom') {
                $('notification_settings_selectors').show();
            } else {
                $('notification_settings_selectors').hide();
            }
        };

        Pachno.Main.Profile.removeOpenIDIdentity = function (url, oid) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {indicator: 'dialog_indicator'},
                success: {
                    remove: 'openid_account_' + oid,
                    callback: function () {
                        if ($('openid_accounts_list').childElements().size() == 0)
                            $('no_openid_accounts').show();
                        if ($('openid_accounts_list').childElements().size() == 1 && $('pick_username_button'))
                            $('openid_accounts_list').down('.button').remove();
                        Pachno.Main.Helpers.Dialog.dismiss();
                    }
                }
            });
        };

        Pachno.Main.Profile.cancelScopeMembership = function (url, sid) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {indicator: 'dialog_indicator'},
                success: {
                    remove: 'account_scope_' + sid,
                    callback: function () {
                        if ($('pending_scope_memberships').childElements().size() == 0)
                            $('no_pending_scope_memberships').show();
                        Pachno.Main.Helpers.Dialog.dismiss();
                    }
                }
            });
        };

        Pachno.Main.Profile.confirmScopeMembership = function (url, sid) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {indicator: 'dialog_indicator'},
                success: {
                    callback: function () {
                        $('confirmed_scope_memberships').insert({'bottom': $('account_scope_' + sid).remove()});
                        $('account_scope_' + sid).down('.button-green').remove();
                        $('account_scope_' + sid).down('.button-red').show();
                        if ($('pending_scope_memberships').childElements().size() == 0)
                            $('no_pending_scope_memberships').show();
                        Pachno.Main.Helpers.Dialog.dismiss();
                    }
                }
            });
        };

        Pachno.Main.Profile.clearPopupsAndButtons = function (event) {
            jQuery('.dropper.active').each(function () {
                jQuery(this).removeClass('active');
            });

            jQuery('.fancy-dropdown.active').each(function () {
                jQuery(this).removeClass('active');
            });
        };

        Pachno.Main.Dashboard.View.init = function (view_id) {
            var dashboard_element = $('dashboard_container_' + view_id),
                dashboard_container = dashboard_element.up('.dashboard'),
                url = dashboard_container.dataset.url.replace('{view_id}', view_id);

            if (dashboard_element.dataset.preloaded == "0") {
                Pachno.Main.Helpers.ajax(url, {
                    url_method: 'get',
                    loading: {indicator: 'dashboard_view_' + view_id + '_indicator'},
                    success: {update: 'dashboard_view_' + view_id},
                    complete: {
                        callback: function () {
                            Pachno.Core._resizeWatcher();
                            Pachno.Main.Dashboard.views.splice(0, 1);
                            if (Pachno.Main.Dashboard.views.size() == 0) {
                                $$('html')[0].setStyle({'cursor': 'default'});
                            }
                        }
                    }
                });
            }
        };

        Pachno.Main.Dashboard.sort = function (event) {
            var list = $(event.target);
            var url = list.up('.dashboard').dataset.sortUrl;
            var items = '&column=' + list.dataset.column;
            list.childElements().each(function (view) {
                if (view.dataset.viewId !== undefined) {
                    items += '&view_ids[]=' + view.dataset.viewId;
                }
            });
            Pachno.Main.Helpers.ajax(url, {
                additional_params: items,
                loading: {indicator: list.down('.dashboard_indicator')}
            });
        };

        Pachno.Main.Dashboard.initializeSorting = function ($) {
            $('.dashboard_column.jsortable').sortable({
                handle: '.dashboardhandle',
                connectWith: '.dashboard_column',
                items: '.dashboard_view_container',
                helper: function(event, ui){
                    var $clone =  $(ui).clone();
                    $clone .css('position','absolute');
                    return $clone.get(0);
                }
            }).bind('sortupdate', Pachno.Main.Dashboard.sort);
        };

        Pachno.Main.Dashboard.addView = function (element) {
            var dashboard_element = element.up('.dashboard_view');
            element.disable();
            var dashboard_views_container = dashboard_element.up('.available_views_container');
            var dashboard_container = $('dashboard_' + dashboard_views_container.dataset.dashboardId);
            var url = dashboard_container.dataset.postUrl;
            var column = dashboard_views_container.dataset.column;
            Pachno.Main.Helpers.ajax(url, {
                url_method: 'post',
                params: 'mode=add_view&view_type=' + dashboard_element.dataset.viewType + '&view_subtype=' + dashboard_element.dataset.viewSubtype + '&column=' + column,
                loading: {
                    indicator: dashboard_element.down('.view_indicator'),
                },
                success: {
                    callback: function (json) {
                        var column_container = dashboard_container.down('.dashboard_column.column_' + column);
                        column_container.insert({bottom: json.view_content});
                        Pachno.Main.Dashboard.views.push(json.view_id);
                        Pachno.Main.Dashboard.View.init(json.view_id);
                        element.enable();
                        Pachno.Main.Dashboard.initializeSorting(jQuery);
                    }
                }
            });
        };

        Pachno.Main.Dashboard.removeView = function (event, element) {
            var view_id = element.up('.dashboard_view_container').dataset.viewId;
            var column = element.up('.dashboard_column');
            var dashboard_container = element.up('.dashboard');
            var url = dashboard_container.dataset.postUrl;
            Pachno.Main.Helpers.ajax(url, {
                params: '&mode=remove_view&view_id=' + view_id,
                loading: {indicator: element.up('.dashboard_view_container').down('.dashboard_indicator')},
                success: {
                    remove: 'dashboard_container_' + view_id
                }
            });
        };

        Pachno.Main.Dashboard.addViewPopup = function (event, element) {
            event.stopPropagation();
            var backdrop_url = element.up('.dashboard').dataset.addViewUrl;
            backdrop_url += '&column=' + element.up('.dashboard_column').dataset.column;
            Pachno.Main.Helpers.Backdrop.show(backdrop_url);
        };

        Pachno.Main.Dashboard.toggleMenu = function (link) {
            var section = $(link).dataset.section;
            $(link).up('ul').childElements().each(function (menu_elm) {
                menu_elm.removeClassName('selected');
            })
            $(link).up('li').addClassName('selected');
            $(link).up('.backdrop_detail_content').down('.available_views_container').childElements().each(function (view_list) {
                ($(view_list).dataset.section == section) ? $(view_list).show() : $(view_list).hide();
            });

        };

        Pachno.Main.Dashboard.sidebar = function (url, id)
        {
            Pachno.Main.setToggleState(url, !$(id).hasClassName('collapsed'));
            $(id).toggleClassName('collapsed');
            Pachno.Core._resizeWatcher();
            Pachno.Core._scrollWatcher();
        }

        Pachno.Main.Profile.setState = function (url, ind) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {indicator: ind},
                success: {
                    callback: function (json) {
                        $$('.current_userstate').each(function (element) {
                            $(element).update(json.userstate);
                        });
                    }
                }
            });
        }

        Pachno.Main.Profile.addFriend = function (url, user_id, rnd_no) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'toggle_friend_' + user_id + '_' + rnd_no + '_indicator',
                    hide: ['add_friend_' + user_id + '_' + rnd_no, 'user_' + user_id + '_more_actions']
                },
                success: {
                    show: ['remove_friend_' + user_id + '_' + rnd_no, 'user_' + user_id + '_more_actions']
                },
                failure: {
                    show: ['add_friend_' + user_id + '_' + rnd_no, 'user_' + user_id + '_more_actions']
                }
            });
        }

        Pachno.Main.Profile.removeFriend = function (url, user_id, rnd_no) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'toggle_friend_' + user_id + '_' + rnd_no + '_indicator',
                    hide: ['remove_friend_' + user_id + '_' + rnd_no, 'user_' + user_id + '_more_actions']
                },
                success: {
                    show: ['add_friend_' + user_id + '_' + rnd_no, 'user_' + user_id + '_more_actions']
                },
                failure: {
                    show: ['remove_friend_' + user_id + '_' + rnd_no, 'user_' + user_id + '_more_actions']
                }
            });
        };

        Pachno.Main.hideInfobox = function (url, boxkey) {
            if ($('close_me_' + boxkey).checked) {
                var $form = jQuery('#close_me_' + boxkey + '_form');
                $form.addClass('submitting');
                $form.find('.button.primary').attr('disabled', true);

                fetch(url)
                    .then(function (response) {
                        setTimeout(function () {
                            $form.removeClass('submitting');
                            $form.find('.button.primary').attr('disabled', false);
                        }, 300);
                        $('infobox_' + boxkey).fade({duration: 0.25});
                    });
            } else {
                $('infobox_' + boxkey).fade({duration: 0.3});
            }
        };

        Pachno.Main.setToggleState = function (url, state) {
            url += '/' + (state ? '1' : 0);
            Pachno.Main.Helpers.ajax(url, {});
        };

        Pachno.Main.Comment.showPost = function () {
            $$('.comment-editor').each(Element.hide);
            $('comment_add_button').hide();
            $('comment_add').show();
            $('comment_bodybox').focus();
        };

        Pachno.Main.Comment.toggleOrder = function (target_type, target_id) {
            Pachno.Main.Helpers.ajax($('main_container').dataset.url, {
                url_method: 'post',
                loading: {
                    indicator: 'comments_loading_indicator'
                },
                params: '&say=togglecommentsorder',
                success: {
                    callback: function () {
                        Pachno.Main.Comment.reloadAll(target_type, target_id);
                    }
                }
            });
        };

        Pachno.Main.Comment.reloadAll = function (target_type, target_id) {
            Pachno.Main.Helpers.ajax($('main_container').dataset.url, {
                url_method: 'get',
                loading: {
                    indicator: 'comments_loading_indicator'
                },
                params: '&say=loadcomments&target_type='+target_type+'&target_id='+target_id,
                success: {
                    callback: function (json) {
                        $('comments_box').update(json.comments);
                    }
                }
            });
        };

        Pachno.Main.Comment.remove = function (url, comment_id, commentcount_span) {
            jQuery('#dialog_indicator').show();
            fetch(url, {
                method: 'DELETE'
            })
                .then(function (response) {
                    response.json()
                        .then(function () {
                            if (response.ok) {
                                jQuery('#comment_' + comment_id).remove();
                                Pachno.Main.Helpers.Dialog.dismiss();
                                jQuery('#dialog_indicator').hide();
                                if ($('comments_box').childElements().size() == 0) {
                                    $('comments-list-none').show();
                                }
                                $(commentcount_span).update($('comments_box').childElements().size());
                            }
                        });
                });
            // Pachno.Main.Helpers.ajax(url, {
            //     method: 'DELETE'
            //     loading: {
            //         indicator: 'dialog_indicator'
            //     },
            //     success: {
            //         remove: 'comment_' + comment_id,
            //         callback: function () {
            //             Pachno.Main.Helpers.Dialog.dismiss();
            //             if ($('comments_box').childElements().size() == 0) {
            //                 $('comments-list-none').show();
            //             }
            //             $(commentcount_span).update($('comments_box').childElements().size());
            //         }
            //     }
            // });
        };

        Pachno.Main.Comment.update = function (comment_id) {
            var $form = jQuery('#comment_edit_form_' + comment_id),
                data = new FormData($form[0]),
                $comment_container = jQuery('#comment_' + comment_id + '_content');

            $form.find('.error-container').removeClass('invalid');
            $form.find('.error-container > .error').html('');
            $form.addClass('submitting');
            $form.find('.button.primary').attr('disabled', true);

            fetch($form.attr('action'), {
                method: 'POST',
                body: data
            })
                .then(function (response) {
                    response.json().then(function (json) {
                        if (response.ok) {
                            $comment_container.html(json.comment_data);
                            jQuery('#comment_edit_' + comment_id).removeClass('active');
                            jQuery('#comment_' + comment_id + '_body').show();
                            jQuery('#comment_view_' + comment_id).show();
                        } else {
                            $form.find('.error-container > .error').html(json.error);
                            $form.find('.error-container').addClass('invalid');
                        }

                        $form.removeClass('submitting');
                        $form.find('.button.primary').attr('disabled', false);
                    });
                });

            // Pachno.Main.Helpers.ajax(url, {
            //     form: 'comment_edit_form_' + comment_id,
            //     loading: {
            //         indicator: 'comment_edit_indicator_' + comment_id,
            //         hide: 'comment_edit_controls_' + comment_id
            //     },
            //     success: {
            //         hide: ['comment_edit_indicator_' + comment_id],
            //         show: ['comment_view_' + comment_id, 'comment_edit_controls_' + comment_id, 'comment_add_button'],
            //         update: {element: 'comment_' + comment_id + '_content', from: 'comment_body'},
            //         callback: function () {
            //             $('comment_edit_' + comment_id).removeClassName('active');
            //             $('comment_' + comment_id + '_body').show();
            //         }
            //     },
            //     failure: {
            //         show: ['comment_edit_controls_' + comment_id]
            //     }
            // });
        };

        Pachno.Main.Comment.add = function (url, commentcount_span) {
            var $form = jQuery('#add-comment-form'),
                data = new FormData($form[0]),
                $count_span = jQuery('#' + commentcount_span),
                $comments_container = jQuery('#comments_box');

            $form.find('.error-container').removeClass('invalid');
            $form.find('.error-container > .error').html('');
            $form.addClass('submitting');
            $form.find('.button.primary').attr('disabled', true);

            fetch($form.attr('action'), {
                method: 'POST',
                body: data
            })
                .then(function (response) {
                    response.json().then(function (json) {
                        if (response.ok) {
                            $comments_container.append(json.comment_data);
                            jQuery('#comments-list-none').remove();
                            window.location.hash = "#comment_" + json.comment_id;
                            $count_span.html(json.commentcount);
                            $form[0].reset();

                            jQuery('#comment_add').hide();
                            jQuery('#comment_add_button').show();
                        } else {
                            $form.find('.error-container > .error').html(json.error);
                            $form.find('.error-container').addClass('invalid');
                        }

                        $form.removeClass('submitting');
                        $form.find('.button.primary').attr('disabled', false);
                    });
                });
        };

        Pachno.Main.Comment.reply = function (reply_comment_id) {
            var $form = jQuery('#comment_reply_form_' + reply_comment_id),
                data = new FormData($form[0]),
                $comments_container = jQuery('#comment_' + reply_comment_id + '_replies');

            $form.find('.error-container').removeClass('invalid');
            $form.find('.error-container > .error').html('');
            $form.addClass('submitting');
            $form.find('.button.primary').attr('disabled', true);

            fetch($form.attr('action'), {
                method: 'POST',
                body: data
            })
                .then(function (response) {
                    response.json().then(function (json) {
                        if (response.ok) {
                            $comments_container.append(json.comment_data);
                            window.location.hash = "#comment_" + json.comment_id;
                            $form[0].reset();

                            jQuery('#comment_reply_controls_' + reply_comment_id).show();
                            jQuery('#comment_reply_' + reply_comment_id).removeClass('active');
                        } else {
                            $form.find('.error-container > .error').html(json.error);
                            $form.find('.error-container').addClass('invalid');
                        }

                        $form.removeClass('submitting');
                        $form.find('.button.primary').attr('disabled', false);
                    });
                });
        };

        Pachno.Main.Login.register = function (url)
        {
            Pachno.Main.Helpers.ajax(url, {
                form: 'register_form',
                loading: {
                    indicator: 'register_indicator',
                    hide: 'register_button',
                    callback: function () {
                        $$('input.required').each(function (field) {
                            $(field).setStyle({backgroundColor: ''});
                        });
                    }
                },
                success: {
                    hide: 'register_form',
                    update: {element: 'register_message', from: 'loginmessage'},
                    callback: function (json) {
                        if (json.activated) {
                            $('register_username_hidden').setValue($('fieldusername').getValue());
                            $('register_password_hidden').setValue(json.one_time_password);
                            $('register_auto_form').show();
                        } else {
                            $('register_confirm_back').show();
                        }
                        $('register_confirmation').show();
                    }
                },
                failure: {
                    show: 'register_button',
                    callback: function (json) {
                        json.fields.each(function (field) {
                            $(field).setStyle({backgroundColor: '#FBB'});
                        });
                    }
                }
            });
        };

        Pachno.Main.Login.checkUsernameAvailability = function (url)
        {
            var $username_row = jQuery('#row-register-username'),
                data = new FormData();

            data.append('username', jQuery('#fieldusername').val());
            $username_row.addClass('submitting');

            fetch(url, {
                method: 'POST',
                body: data
            })
                .then((_) => _.json())
                .then(function (json) {
                    $username_row.removeClass('submitting');
                    if (json.available) {
                        $username_row.removeClass('invalid');
                    } else {
                        $username_row.addClass('invalid');
                    }
                });
        };

        Pachno.Main.Login.registerAutologin = function (url)
        {
            Pachno.Main.Helpers.ajax(url, {
                form: 'register_auto_form',
                loading: {
                    indicator: 'register_autologin_indicator',
                    callback: function () {
                        $('register_autologin_button').disable();
                        $('register_autologin_indicator').show();
                    }
                },
                complete: {
                    callback: function () {
                        $('register_autologin_indicator').hide();
                        $('register_autologin_button').enable();
                    }
                }
            });
        };

        Pachno.Main.Login.login = function ()
        {
            var $form = jQuery('#login_form'),
                $login_button = jQuery('#login_button'),
                url = $form.attr('action');

            jQuery('#login-error-container').removeClass('invalid');
            $login_button.addClass('submitting');
            $login_button.attr('disabled', true);

            fetch(url, {
                method: 'POST',
                body: new FormData($form[0])
            })
                .then(function (response) {
                    response.json().then(function (json) {
                        $login_button.removeClass('submitting');
                        $login_button.attr('disabled', false);

                        if (response.ok) {
                            if (json.forward) {
                                window.location = json.forward;
                            } else {
                                window.location.reload();
                            }
                        } else {
                            console.error(json);
                            jQuery('#login-error-message').html(json.error);
                            jQuery('#login-error-container').addClass('invalid');
                        }
                    });
                })
                .catch(function (error) {
                    jQuery('#login-error-message').html(error);
                    jQuery('#login-error-container').addClass('invalid');
                    console.error(error);
                });

            // Pachno.Main.Helpers.ajax(url, {
            //     form: 'login_form',
            //     loading: {
            //         indicator: 'login_indicator',
            //         callback: function () {
            //             $('login_button').disable();
            //             $('login_indicator').show();
            //         }
            //     },
            //     complete: {
            //         callback: function () {
            //             $('login_indicator').hide();
            //             $('login_button').enable();
            //         }
            //     }
            // });
        };

        Pachno.Main.Login.verify2FaTokenWithLogin = function (form) {
            Pachno.Core.fetchPostHelper(form)
                .then(Pachno.Core.fetchPostDefaultFormHandler)
                .then(([$form, response]) => {
                    if (response.ok) {
                        response.json().then(function (json) {
                            window.location = json.forward;
                        });
                    }
                })
        };

        Pachno.Main.Login.verify2FaToken = function (form) {
            Pachno.Core.fetchPostHelper(form)
                .then(Pachno.Core.fetchPostDefaultFormHandler)
                .then(([$form, response]) => {
                    if (response.ok) {
                        $('#account_2fa_enabled').show();
                        $('#account_2fa_disabled').hide();
                    }
                    $form.find('.button.primary').attr('disabled', false);
                    Pachno.Main.Helpers.Dialog.dismiss();
                });
        };

        Pachno.Main.Login.disable2Fa = function (url) {
            fetch(url, {method: 'POST'})
                .then(function(response) {
                    if (response.ok) {
                        $('#account_2fa_enabled').hide();
                        $('#account_2fa_disabled').show();
                    }
                })
                .catch(Pachno.Main.Helpers.Dialog.error);
        };

        Pachno.Main.Login.elevatedLogin = function (url)
        {
            Pachno.Main.Helpers.ajax(url, {
                form: 'login_form',
                loading: {
                    indicator: 'elevated_login_indicator',
                    callback: function () {
                        $('login_button').disable();
                        $('elevated_login_indicator').show();
                    }
                },
                complete: {
                    callback: function (json) {
                        $('elevated_login_indicator').hide();
                        if (json.elevated) {
                            window.location.reload(true);
                        } else {
                            Pachno.Main.Helpers.Message.error(json.error);
                            $('login_button').enable();
                        }
                    }
                }
            });
        };

        Pachno.Main.Login.resetForgotPassword = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'forgot_password_form',
                loading: {
                    indicator: 'forgot_password_indicator',
                    hide: 'forgot_password_button'
                },
                failure: {
                    reset: 'forgot_password_form'
                },
                complete: {
                    show: 'forgot_password_button',
                    callback: function () {
                        $('regular_login_container').up().select('.logindiv').each(function (elm) {
                            elm.removeClassName('active');
                        });
                        $('regular_login_container').addClassName('active');
                    }
                }
            });
        };

        Pachno.Main.Login.showLogin = function (section) {
            $('login_backdrop').select('.logindiv').each(function (elm) {
                elm.removeClassName('active');
            });
            $(section).addClassName('active');
            if (section != 'register' && $('registration-button-container')) {
                $('registration-button-container').addClassName('active');
            }
            $('login_backdrop').show();
            setTimeout(function () {
                if (section == 'register') {
                    $('fieldusername').focus();
                } else if (section == 'regular_login_container') {
                    $('pachno_username').focus();
                }
            }, 250);
        };

        Pachno.Main.Login.forgotToggle = function () {
            $('regular_login_container').up().select('.logindiv').each(function (elm) {
                elm.removeClassName('active');
            });
            $('forgot_password_container').addClassName('active');
        };

        Pachno.Project.Statistics.get = function (url, section) {
            $('statistics_selector').childElements().each(function (elm) {
                elm.removeClassName('selected');
            });
            $('statistics_per_' + section + '_selector').addClassName('selected');
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    show: 'statistics_main',
                    hide: 'statistics_help',
                    callback: function () {
                        $('statistics_main_image').src = '';
                        for (var cc = 1; cc <= 3; cc++) {
                            $('statistics_mini_image_' + cc).src = '';
                        }
                    }
                },
                success: {
                    callback: function (json) {
                        $('statistics_main_image').src = json.images.main;
                        ecc = 1;
                        for (var cc = 1; cc <= 3; cc++) {
                            var small_name = 'mini_' + cc + '_small';
                            var large_name = 'mini_' + cc + '_large';
                            if (json.images[small_name]) {
                                $('statistics_mini_image_' + cc).show();
                                $('statistics_mini_image_' + cc).src = json.images[small_name];
                                $('statistics_mini_' + cc + '_main').setValue(json.images[large_name]);
                            } else {
                                $('statistics_mini_image_' + cc).hide();
                                $('statistics_mini_' + cc + '_main').setValue('');
                                ecc++;
                            }
                        }
                        if (ecc == cc) {
                            $('statistics_main_image_div').next().hide();
                            $('statistics_main_image_div').next().next().hide();
                        }
                        else {
                            $('statistics_main_image_div').next().show();
                            $('statistics_main_image_div').next().next().show();
                        }
                    }
                },
                failure: {show: 'statistics_help'}
            });
        };

        Pachno.Project.Statistics.toggleImage = function (image) {
            $('statistics_main_image').src = '';
            $('statistics_main_image').src = $('statistics_mini_' + image + '_main').getValue();
        };

        Pachno.Project.Milestone.refresh = function (url, milestone_id) {
            var m_id = milestone_id;
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'milestone_' + milestone_id + '_indicator'
                },
                success: {
                    callback: function (json) {
                        var must_reload_issue_list = false;
                        if (json.percent) {
                            Pachno.Main.updatePercentageLayout('milestone_' + m_id + '_percent', json.percent);
                            delete json.percent;
                        }
                        for (var item in json)
                        {
                            var existing = $('milestone_' + m_id + '_' + item);
                            if (existing)
                            {
                                if (existing.innerHTML != json[item])
                                {
                                    existing.update(json[item]);
                                    must_reload_issue_list = true;
                                }
                            }
                        }
                        if (must_reload_issue_list) {
                            $('milestone_' + m_id + '_changed').show();
                            $('milestone_' + m_id + '_issues').update('');
                        }

                    }
                }
            });
        };

        Pachno.Project.Timeline.update = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                url_method: 'get',
                additional_params: "offset=" + $('timeline_offset').getValue(),
                loading: {
                    indicator: 'timeline_indicator',
                    hide: 'timeline_more_link'
                },
                success: {
                    update: {element: 'timeline', insertion: true},
                    show: 'timeline_more_link',
                    callback: function (json) {
                        $('timeline_offset').setValue(json.offset)
                    }
                }
            });
        };

        Pachno.Project.showBranchCommits = function (url, branch) {
            $$('body')[0].setStyle({'overflow': 'auto'});

            Pachno.Main.Helpers.ajax(url, {
                url_method: 'post',
                additional_params: "branch=" + branch,
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'project_commits_box']
                },
                success: {
                    show: 'project_commits_box',
                    update: 'project_commits'
                }
            });
        }

        Pachno.Project.Commits.update = function (url, branch) {
            Pachno.Main.Helpers.ajax(url, {
                url_method: 'post',
                additional_params: "from_commit=" + $('from_commit').getValue() + "&branch=" + branch,
                loading: {
                    indicator: 'commits_indicator',
                    hide: 'commits_more_link'
                },
                success: {
                    update: {element: 'commits', insertion: true},
                    show: 'commits_more_link',
                    callback: function (json) {
                        $('from_commit').setValue(json.last_commit)
                    }
                }
            });
        };

        Pachno.Project.Commits.viewIssueUpdate = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                url_method: 'post',
                additional_params: "offset=" + $('commits_offset').getValue() + "&limit=" + $('commits_limit').getValue(),
                loading: {
                    indicator: 'commits_indicator',
                    hide: 'commits_more_link'
                },
                success: {
                    update: {element: 'viewissue_vcs_integration_commits', insertion: true}
                }
            });
        };

        Pachno.Project.Scrum.Sprint.add = function (url, assign_url)
        {
            Pachno.Main.Helpers.ajax(url, {
                form: 'add_sprint_form',
                loading: {indicator: 'sprint_add_indicator'},
                success: {
                    reset: 'add_sprint_form',
                    hide: 'no_sprints',
                    update: {element: 'scrum_sprints', insertion: true}
                }
            });
        }

        Pachno.Project.Scrum.Story.setColor = function (url, story_id, color, event)
        {
            event.stopPropagation();
            Pachno.Main.Helpers.ajax(url, {
                params: {color: color},
                loading: {indicator: 'color_selector_' + story_id + '_indicator'},
                success: {
                    callback: function (json) {
                        $('story_color_' + story_id).style.backgroundColor = color;
                        $('story_color_' + story_id).style.color = json.text_color;
                        $$('.epic_badge').each(function (badge) {
                            if (badge.dataset.parentEpicId == story_id) {
                                badge.style.backgroundColor = color;
                                badge.style.color = json.text_color;
                            }
                        });
                    }
                },
                complete: {
                    callback: function () {
                        Pachno.Main.Profile.clearPopupsAndButtons();
                    }
                }
            });
        }

        Pachno.Project.updateLinks = function (json) {
            if ($('current_project_num_count'))
                $('current_project_num_count').update(json.total_count);
            (json.more_available) ? $('add_project_div').show() : $('add_project_div').hide();
        }

        Pachno.Project.resetIcons = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                url_method: 'post',
                additional_params: '&clear_icons=1'
            });
        };

        Pachno.Project.initializeFilterSearch = function () {
            var si = filter.down('input[type=search]');
            if (si != undefined)
            {
                si.dataset.previousValue = '';
                if (si.dataset.callbackUrl !== undefined) {
                    var fk = filter.dataset.filterKey;
                    si.on('keyup', function (event, element) {
                        if (Pachno.ift_observers[fk])
                            clearTimeout(Pachno.ift_observers[fk]);
                        if ((si.getValue().length >= 3 || si.getValue().length == 0) && si.getValue() != si.dataset.lastValue) {
                            Pachno.ift_observers[fk] = setTimeout(function () {
                                Pachno.Search.getFilterValues(si);
                                si.dataset.lastValue = si.getValue();
                            }, 1000);
                        }
                    });
                } else {
                    si.on('keyup', Pachno.Search.filterFilterOptions);
                }
                si.on('click', function (event, element) {
                    event.stopPropagation();
                    event.preventDefault();
                });
                filter.addClassName('searchable');
            }
        };

        Pachno.Project.add = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'add_project_form',
                loading: {indicator: 'project_add_indicator'},
                success: {
                    reset: 'add_project_form',
                    update: {element: 'project_table', insertion: true},
                    hide: 'noprojects_tr',
                    callback: Pachno.Project.updateLinks
                }
            });
        };

        Pachno.Project.remove = function (url, pid) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop', 'project_delete_controls_' + pid]
                },
                success: {
                    remove: 'project_box_' + pid,
                    callback: function (json) {
                        if ($('project_table').childElements().size() == 0)
                            $('noprojects_tr').show();
                        if ($('project_table_archived').childElements().size() == 0)
                            $('noprojects_tr_archived').show();
                        Pachno.Project.updateLinks(json);
                        Pachno.Main.Helpers.Dialog.dismiss();
                    }
                },
                failure: {
                    show: 'project_delete_error_' + pid
                },
                complete: {
                    show: 'project_delete_controls_' + pid
                }
            });
        }

        Pachno.Project.archive = function (url, pid) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'project_' + pid + '_archive_indicator'
                },
                success: {
                    remove: 'project_box_' + pid,
                    hide: 'noprojects_tr_archived',
                    callback: function (json) {
                        if ($('project_table').childElements().size() == 0)
                            $('noprojects_tr').show();
                        $('project_table_archived').insert({top: json.box});
                        Pachno.Main.Helpers.Dialog.dismiss();
                    }
                }
            });
        }

        Pachno.Project.unarchive = function (url, pid) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'project_' + pid + '_archive_indicator'
                },
                success: {
                    remove: 'project_box_' + pid,
                    hide: 'noprojects_tr',
                    callback: function (json) {
                        if ($('project_table_archived').childElements().size() == 0)
                            $('noprojects_tr_archived').show();
                        if (json.parent_id != 0) {
                            $('project_' + json.parent_id + '_children').insert({bottom: json.box});
                        } else {
                            $('project_table').insert({bottom: json.box});
                        }
                    }
                },
                failure: {
                    show: 'project_' + pid + '_unarchive'
                }
            });
        };

        Pachno.Project.loadList = function (key, url) {
            Pachno.Main.Helpers.tabSwitcher('tab_' + key, 'projects_list_tabs', true);

            if ($('tab_' + key + '_pane').innerHTML === '') {
                Pachno.Main.Helpers.ajax(url, {
                    loading: {indicator: 'project_list_tab_' + key + '_indicator'},
                    success: {
                        update: {element: 'tab_' + key + '_pane'},
                    }
                });
            }
        };

        Pachno.Project.Planning.initializeMilestoneDragDropSorting = function (milestone) {
            var milestone_issues = jQuery(milestone).find('.milestone-issues.jsortable');
            if (milestone_issues.hasClass('ui-sortable')) {
                milestone_issues.sortable('destroy');
            }
            milestone_issues.sortable({
                handle: '.draggable',
                connectWith: '.jsortable.intersortable',
                update: Pachno.Project.Planning.sortMilestoneIssues,
                receive: Pachno.Project.Planning.moveIssue,
                sort: Pachno.Project.Planning.calculateNewBacklogMilestoneDetails,
                start: function (event) {
                    jQuery('.milestone-issues-container').each(function (index) {
                        jQuery(this).addClass('issue-drop-target');
                    })
                },
                stop: function (event) {
                    jQuery('.milestone-issues-container').each(function (index) {
                        jQuery(this).removeClass('issue-drop-target');
                    })
                },
                over: function (event) { jQuery(this).addClass('drop-hover'); },
                out: function (event) { jQuery(this).removeClass('drop-hover'); },
                tolerance: 'pointer',
                helper: function(event, ui) {
                    var $clone =  $(ui).clone();
                    $clone .css('position','absolute');
                    return $clone.get(0);
                }
            });
        };

        Pachno.Project.Planning.initializeReleaseDroptargets = function () {
            jQuery('#builds-list .release').not('ui-droppable').droppable({
                drop: Pachno.Project.Planning.assignRelease,
                accept: '.milestone-issue',
                tolerance: 'pointer',
                hoverClass: 'drop-hover'
            });
        };

        Pachno.Project.Planning.initializeEpicDroptargets = function () {
            jQuery('#epics-list .epic').not('.ui-droppable').droppable({
                drop: Pachno.Project.Planning.assignEpic,
                accept: '.milestone-issue',
                tolerance: 'pointer',
                hoverClass: 'drop-hover'
            });
        };

        Pachno.Project.Planning.toggleReleaseFilter = function (release) {
            if (release !== 'auto' && $('epics-list') && $('epics-list').hasClassName('filtered'))
                Pachno.Project.Planning.toggleEpicFilter('auto');
            if ($('builds-list').hasClassName('filtered') && (release == 'auto' || ($(release) && $(release).hasClassName('selected')))) {
                $('builds-list').removeClassName('filtered');
                $('builds-list').childElements().each(function (rel) {
                    rel.removeClassName('selected');
                });
                $$('.milestone-issue').each(function (issue) {
                    issue.removeClassName('filtered');
                });
            } else if ($(release)) {
                $('builds-list').addClassName('filtered');
                $('builds-list').childElements().each(function (rel) {
                    rel.removeClassName('selected');
                });
                $(release).addClassName('selected');
                var release_id = $(release).dataset.releaseId;
                $$('.milestone-issue').each(function (issue) {
                    (issue.dataset['release-' + release_id] === undefined) ? issue.addClassName('filtered') : issue.removeClassName('filtered');
                });
            }

            Pachno.Project.Planning.calculateAllMilestonesVisibilityDetails();
        };

        Pachno.Project.Planning.toggleEpicFilter = function (epic) {
            if (epic !== 'auto' && $('builds-list') && $('builds-list').hasClassName('filtered'))
                Pachno.Project.Planning.toggleReleaseFilter('auto');
            if ($('epics-list').hasClassName('filtered') && (epic == 'auto' || ($(epic) && $(epic).hasClassName('selected')))) {
                $('epics-list').removeClassName('filtered');
                $('epics-list').childElements().each(function (ep) {
                    ep.removeClassName('selected');
                });
                $$('.milestone-issue').each(function (issue) {
                    issue.removeClassName('filtered');
                });
            } else if ($(epic)) {
                $('epics-list').addClassName('filtered');
                $('epics-list').childElements().each(function (ep) {
                    ep.removeClassName('selected');
                });
                $(epic).addClassName('selected');
                var epic_id = $(epic).dataset.issueId;
                $$('.milestone-issue').each(function (issue) {
                    (issue.dataset['parent-' + epic_id] === undefined) ? issue.addClassName('filtered') : issue.removeClassName('filtered');
                });
            }

            Pachno.Project.Planning.calculateAllMilestonesVisibilityDetails();
        };

        Pachno.Project.Planning.toggleClosedIssues = function () {
            $('milestones-list').toggleClassName('show_closed');
            Pachno.Project.Planning.calculateAllMilestonesVisibilityDetails();
            Pachno.Project.Planning.calculateNewBacklogMilestoneDetails();
            Pachno.Main.Profile.clearPopupsAndButtons();
        };

        Pachno.Project.Planning.assignRelease = function (event, ui) {
            var issue = $(ui.draggable[0]);
            issue.dataset.sortCancel = true;
            if (issue.hasClassName('milestone-issue')) {
                var release = $(event.target);
                var release_id = $(event.target).dataset.releaseId;
                var url = release.dataset.assignIssueUrl;
                Pachno.Main.Helpers.ajax(url, {
                    additional_params: 'issue_id=' + issue.dataset.issueId,
                    loading: {indicator: release.down('.planning_indicator')},
                    complete: {
                        callback: function (json) {
                            $('release_' + release_id + '_percentage_filler').setStyle({width: json.closed_pct + '%'});
                            Pachno.Core.Pollers.Callbacks.planningPoller();
                            issue.dataset['release-' + release_id] = true;
                        }
                    }
                });
            }
        };

        Pachno.Project.Planning.updateNewMilestoneIssues = function () {
            var num_issues = jQuery('.milestone-issue.included').size();
            $('milestone_include_num_issues').update(num_issues);
            $('milestone_include_issues').show();
            $('include_selected_issues').setValue(1);
        };

        Pachno.Project.Planning.addEpic = function (form) {
            var url = form.action;
            Pachno.Main.Helpers.ajax(url, {
                form: form,
                loading: {indicator: 'new_epic_indicator'},
                success: {
                    callback: function (json) {
                        $(form).reset();
                        $(form).up('li').removeClassName('selected');
                        Pachno.Core.Pollers.Callbacks.planningPoller();
                    }
                }
            });
        };

        Pachno.Project.Planning.assignEpic = function (event, ui) {
            var issue = $(ui.draggable[0]);
            issue.dataset.sortCancel = true;
            if (issue.hasClassName('milestone-issue')) {
                var epic = $(event.target);
                var epic_id = $(event.target).dataset.issueId;
                var url = epic.dataset.assignIssueUrl;
                Pachno.Main.Helpers.ajax(url, {
                    additional_params: 'issue_id=' + issue.dataset.issueId,
                    loading: {indicator: epic.down('.planning_indicator')},
                    complete: {
                        callback: function (json) {
                            $('epic_' + epic_id + '_percentage_filler').setStyle({width: json.closed_pct + '%'});
                            $('epic_' + epic_id + '_estimate').update(json.estimate);
                            $('epic_' + epic_id + '_child_issues_count').update(json.num_child_issues);
                            issue.dataset['parent-' + epic_id] = true;
                            Pachno.Core.Pollers.Callbacks.planningPoller();
                        }
                    }
                });
            }
        };

        Pachno.Project.Planning.destroyMilestoneDropSorting = function (milestone) {
            if (milestone === undefined) {
                jQuery('.milestone-issues.ui-sortable').sortable('destroy');
            } else {
                jQuery(milestone).select('.milestone-issues.ui-sortable').sortable('destroy');
            }
        };

        Pachno.Project.Planning.getMilestoneIssues = function (milestone) {
            if (milestone.hasClassName('initialized')) {
                return Promise.resolve();
            }

            let updateMilestoneIssuesContent = function (response) {
                $('milestone_' + milestone_id + '_issues').update(response.content);
                return response;
            };

            let ti_button = milestone.down('.toggle-issues');

            if (ti_button) {
                ti_button.addClassName('disabled');
                ti_button.addClassName('submitting');
            }

            var milestone_id = milestone.dataset.milestoneId;

            return new Promise(function (resolve, reject) {
                fetch(milestone.dataset.issuesUrl)
                    .then((_) => _.json())
                    .then(updateMilestoneIssuesContent)
                    .then(function (response) {
                        milestone.addClassName('initialized');

                        if (Pachno.Project.Planning.options.dragdrop) {
                            Pachno.Project.Planning.initializeMilestoneDragDropSorting(milestone);
                        }

                        if (milestone.hasClassName('available')) {
                            var completed_milestones = $$('.milestone-box.available.initialized');
                            var multiplier = 100 / Pachno.Project.Planning.options.milestone_count;
                            var pct = Math.floor(completed_milestones.size() * multiplier);
                            $('planning_percentage_filler').setStyle({width: pct + '%'});

                            if (completed_milestones.size() == (Pachno.Project.Planning.options.milestone_count - 1)) {
                                $('planning_loading_progress_indicator').hide();
                                if (!Pachno.Core.Pollers.planningpoller)
                                    Pachno.Core.Pollers.planningpoller = new PeriodicalExecuter(Pachno.Core.Pollers.Callbacks.planningPoller, 15);

                                $('planning_indicator').hide();
                                $('planning_filter_title_input').enable();
                            }
                        }

                        if (! milestone.down('.planning_indicator').hidden) milestone.down('.planning_indicator').hide();
                    })
                    .then(Pachno.Project.Planning.calculateAllMilestonesVisibilityDetails)
                    .then(function () {
                        if (ti_button) {
                            ti_button.removeClassName('disabled');
                            ti_button.removeClassName('submitting');
                        }

                        resolve();
                    })
                    .catch(function (error) {
                        milestone.addClassName('initialized');
                        milestone.select('.milestone_error_issues').each(Element.show);

                        reject(error);
                    });
            });
        };

        Pachno.Project.Planning.Whiteboard.addColumn = function(button) {
            Pachno.Main.Helpers.ajax(button.dataset.url, {
                loading: {
                    indicator: 'planning_indicator'
                },
                url_method: 'post',
                success: {
                    callback: function(json) {
                        $('planning_whiteboard_columns_form_row').insert({bottom: json.component});
                        Pachno.Project.Planning.Whiteboard.setSortOrder();
                    }
                }
            });
        };

        Pachno.Project.Planning.Whiteboard.toggleEditMode = function() {
            $('project_planning').toggleClassName('edit-mode');
            var $onboarding = $('onboarding-no-board-columns');
            if ($onboarding) {
                $onboarding.hide();
            }
            Pachno.Main.Profile.clearPopupsAndButtons();
        };

        Pachno.Project.Planning.Whiteboard.saveColumns = function() {
            var url = $('planning_whiteboard_columns_form').action;

            $('planning_indicator').show();
            Pachno.Main.Helpers.ajax(url, {
                url_method: 'post',
                form: 'planning_whiteboard_columns_form',
                failure: {
                    hide: 'planning_indicator'
                }
            });
        };

        Pachno.Project.Planning.Whiteboard.calculateColumnCounts = function() {
            $$('#whiteboard-headers .td').each(function (column, index) {
                var counts = 0;
                var status_counts = [];
                column.select('.status-badge').each(function (status) {
                    status_counts[parseInt(status.dataset.statusId)] = 0;
                });
                $$('#whiteboard .tbody .tr').each(function (row) {
                    row.childElements().each(function (subcolumn, subindex) {
                        if (subindex == index) {
                            var issues = subcolumn.select('.whiteboard-issue');
                            issues.each(function (issue) {
                                status_counts[parseInt(issue.dataset.statusId)]++;
                            });
                            counts += issues.size();
                        }
                    });
                });
                if (column.down('.column_count.primary')) column.down('.column_count.primary').update(counts);
                if (column.down('.column_count .count')) column.down('.column_count .count').update(counts);
                column.select('.status-badge').each(function (status) {
                    status.update(status_counts[parseInt(status.dataset.statusId)]);
                });
                if ($('project_planning').hasClassName('type-kanban')) {
                    var min_wi = parseInt(column.dataset.minWorkitems);
                    var max_wi = parseInt(column.dataset.maxWorkitems);
                    if (min_wi !== 0 && counts < min_wi) {
                        column.down('.under_count').update(counts);
                        column.removeClassName('over-workitems');
                        column.addClassName('under-workitems');
                        $$('#whiteboard .tbody .tr').each(function (row) {
                            row.childElements().each(function (subcolumn, subindex) {
                                if (!subcolumn.hasClassName('swimlane-header') && subindex == index) {
                                    subcolumn.removeClassName('over-workitems');
                                    subcolumn.addClassName('under-workitems');
                                }
                            });
                        });
                    }
                    if (max_wi !== 0 && counts > max_wi) {
                        column.down('.over_count').update(counts);
                        column.removeClassName('under-workitems');
                        column.addClassName('over-workitems');
                        $$('#whiteboard .tbody .tr').each(function (row) {
                            row.childElements().each(function (subcolumn, subindex) {
                                if (!subcolumn.hasClassName('swimlane-header') && subindex == index) {
                                    subcolumn.removeClassName('under-workitems');
                                    subcolumn.addClassName('over-workitems');
                                }
                            });
                        });
                    }
                }
            });
        }

        Pachno.Project.Planning.Whiteboard.calculateSwimlaneCounts = function(new_issue_retrieved) {
            var new_issue_retrieved = new_issue_retrieved || false;

            $$('#whiteboard .tbody').each(function (swimlane) {
                swimlane_rows = swimlane.select('.tr');

                if (swimlane_rows.size() != 2) return;

                swimlane_rows[0].down('.swimlane_count').update(swimlane_rows[1].select('.whiteboard-issue').size());

                if (swimlane_rows[1].select('.whiteboard-issue').size() == 0) {
                    swimlane.addClassName('collapsed');
                }
                else if (new_issue_retrieved && swimlane_rows[1].select('.whiteboard-issue').size() > 0) {
                    swimlane.removeClassName('collapsed');
                }
            });
        }

        Pachno.Project.Planning.Whiteboard.retrieveWhiteboard = function() {
            var wb = $('whiteboard');
            if (!wb) {
                $('whiteboard_indicator').hide();
                return;
            }

            wb.removeClassName('initialized');
            var mi = $('selected_milestone_input');
            var milestone_id = (mi.dataset.selectedValue) ? parseInt(mi.dataset.selectedValue) : 0;

            Pachno.Main.Helpers.ajax(wb.dataset.whiteboardUrl, {
                additional_params: '&milestone_id=' + milestone_id,
                url_method: 'get',
                loading: {
                    indicator: 'whiteboard_indicator',
                    callback: function() {
                        $('whiteboard').select('.thead .column_count.primary').each(function (cc) {
                            cc.update('-');
                        });
                        wb.dataset.milestoneId = milestone_id;
                    }
                },
                success: {
                    callback: function(json) {
                        if (json.swimlanes) {
                            wb.removeClassName('no-swimlanes');
                            wb.addClassName('swimlanes');
                        }
                        else {
                            wb.removeClassName('swimlanes');
                            wb.addClassName('no-swimlanes');
                        }
                        wb.addClassName('initialized');
                        wb.select('.tbody').each(Element.remove);
                        $('whiteboard-headers').insert({after: json.component});
                        setTimeout(function () {
                            Pachno.Project.Planning.Whiteboard.calculateColumnCounts();
                            Pachno.Project.Planning.Whiteboard.calculateSwimlaneCounts();
                            Pachno.Project.Planning.Whiteboard.initializeDragDrop();
                        }, 250);
                    }
                }
            });
        };

        Pachno.Project.Planning.Whiteboard.retrieveMilestoneStatus = function(event, item) {
            var mi = $('selected_milestone_input');
            var milestone_id = (event) ? $(item).dataset.inputValue : mi.dataset.selectedValue;
            var board_id = (event) ? $(item).dataset.boardValue : mi.dataset.selectedBoardValue;
            Pachno.Main.Helpers.ajax(mi.dataset.statusUrl, {
                additional_params: '&milestone_id=' + parseInt(milestone_id) + '&board_id=' + parseInt(board_id),
                url_method: 'get',
                loading: {
                    hide: 'selected_milestone_status_details',
                    indicator: 'selected_milestone_status_indicator'
                },
                success: {
                    update: 'selected_milestone_status_details',
                    show: 'selected_milestone_status_details',
                    callback: function () {
                        $('reportissue_button').dataset.milestoneId = milestone_id;
                    }
                }
            });
        };

        Pachno.Project.Planning.Whiteboard.setSortOrder = function() {
            $('planning_whiteboard_columns_form_row').childElements().each(function(column, index) {
                column.down('input.sortorder').setValue(index + 1);
            });
        };

        Pachno.Project.Planning.Whiteboard.setViewMode = function(button, mode) {
            $(button).up('.button-group').childElements().each(function (elm) {
                elm.removeClassName('button-pressed');
            });
            $(button).addClassName('button-pressed');
            var wb = $('whiteboard');
            ['simple', 'detailed'].each(function (viewmode) {
                wb.removeClassName('viewmode-'+viewmode);
            });
            wb.addClassName('viewmode-'+mode);
        };

        Pachno.Project.Planning.Whiteboard.updateIssueColumn = function(event, issue, column, startCoordinates) {
            Pachno.Project.Planning.Whiteboard.moveIssueColumn(issue, column, undefined, undefined, undefined, startCoordinates);
        };

        Pachno.Project.Planning.Whiteboard.moveIssueColumn = function(issue, column, transition_id, original_column, issue_index, startCoordinates) {
            if (! original_column) var original_column = issue.parents('.column');
            if (! issue_index) var issue_index = issue.index();

            if (issue) {
                issue.detach().css({left: '0', top: '0', transform: 'inherit'}).prependTo(column);
            }

            var wb = jQuery('#whiteboard');
            var parameters = '&issue_id=' + parseInt(issue.data('issue-id')) + '&column_id=' + parseInt(column.data('column-id')) + '&milestone_id=' + parseInt(jQuery('#selected_milestone_input').data('selected-value')) + '&swimlane_identifier=' + issue.parents('.tbody').data('swimlane-identifier');
            var revertIssuePosition = function () {
                TweenMax.to(issue, .3, startCoordinates);

                if (issue_index <= 0) {
                    issue.prependTo(original_column);
                }
                else {
                    issue.insertAfter(original_column.children().eq(issue_index - 1));
                }
            };
            var customEscapeWatcher = function (event) {
                if (event.keyCode != undefined && event.keyCode != 0 && Event.KEY_ESC != event.keyCode) return;
                Pachno.Main.Helpers.Backdrop.reset(revertIssuePosition);
                if ($('workflow_transition_fullpage')) $('workflow_transition_fullpage').hide();
                setTimeout(function() {
                    document.stopObserving('keydown', customEscapeWatcher);
                    document.observe('keydown', Pachno.Core._escapeWatcher);
                }, 350);
            };

            if (transition_id) parameters += '&transition_id=' + transition_id;

            Pachno.Main.Helpers.ajax($('whiteboard').dataset.whiteboardUrl, {
                additional_params: parameters,
                url_method: 'post',
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop']
                },
                success: {
                    callback: function(json) {
                        if (json.transition_id && json.component) {
                            document.stopObserving('keydown', Pachno.Core._escapeWatcher);
                            document.observe('keydown', customEscapeWatcher);
                            $('fullpage_backdrop').appear({duration: 0.2});
                            $('fullpage_backdrop_content').update(json.component);
                            $('fullpage_backdrop_content').appear({duration: 0.2});
                            $('fullpage_backdrop_indicator').fade({duration: 0.2});
                            Pachno.Issues.showWorkflowTransition(json.transition_id);
                            $('transition_working_' + json.transition_id + '_cancel').observe('click', function (event) {
                                Event.stop(event);
                                customEscapeWatcher(event);
                            });
                            $('transition_working_' + json.transition_id + '_submit').observe('click', function (event) {
                                Event.stop(event);
                                Pachno.Issues.submitWorkflowTransition($('workflow_transition_' + json.transition_id + '_form'), function () {
                                    Pachno.Core.Pollers.Callbacks.whiteboardPlanningPoller();
                                });
                            });
                        } else if (json.component) {
                            document.stopObserving('keydown', Pachno.Core._escapeWatcher);
                            document.observe('keydown', customEscapeWatcher);
                            $('fullpage_backdrop').appear({duration: 0.2});
                            $('fullpage_backdrop_content').update(json.component);
                            $('fullpage_backdrop_content').appear({duration: 0.2});
                            $('fullpage_backdrop_indicator').fade({duration: 0.2});
                            $('transition-selector-close-link').observe('click', customEscapeWatcher);
                            $$('.transition-selector-button').each(function (elem) {
                                elem.observe('click', function (event) {
                                    Pachno.Project.Planning.Whiteboard.moveIssueColumn(jQuery('#whiteboard_issue_' + elem.dataset.issueId), jQuery('#swimlane_' + elem.dataset.swimlaneIdentifier + '_column_' + elem.dataset.columnId), elem.dataset.transitionId, original_column, issue_index, startCoordinates);
                                });
                            });
                        } else {
                            $('fullpage_backdrop_content').update('');
                            $('fullpage_backdrop').fade({duration: 0.2});
                            if (!issue) {
                                jQuery(json.issue).prependTo(column);
                            }
                            Pachno.Core.Pollers.Callbacks.whiteboardPlanningPoller();
                        }
                    }
                },
                failure: {
                    show: issue,
                    callback: function(json) {
                        if (json.error != undefined && typeof(json.error) == 'string' && json.error.length) {
                            revertIssuePosition();
                        }
                    }
                }
            });

        };

        Pachno.Project.Planning.Whiteboard.resetAvailableDropColumns = function(event) {
            jQuery('.column.drop-valid').each(function (index) {
                jQuery(this).removeClass('drop-valid');
                jQuery(this).removeClass('drop-hover');
            });
        };

        Pachno.Project.Planning.Whiteboard.detectAvailableDropColumns = function(event, issue) {
            var issue = $(issue);
            var issue_statuses = issue.dataset.validStatusIds.split(',');
            issue.up('.row').childElements().each(function (column) {
                var column_statuses = column.dataset.statusIds.split(',');
                var has_status = false;
                issue_statuses.each(function (status) {
                    if (column_statuses.indexOf(status) != -1) {
                        has_status = true;
                    }
                });

                if (!has_status) {
                    jQuery(column).removeClass('gs-droppable');
                } else {
                    column.addClassName('drop-valid');
                    column.addClassName('gs-droppable');
                }
            });
        };

        Pachno.Project.Planning.Whiteboard.initializeDragDrop = function () {
            if (jQuery('.whiteboard-issue').length > 0) {
                var overlapThreshold = '30%';
                var droppablesSelector = '.gs-droppable';
                GSDraggable.create(jQuery('.whiteboard-issue'), {
                    type: 'x',
                    bounds: jQuery('#whiteboard'),
                    onPress: function() {
                        this.startX = this.x;
                        this.startY = this.y;
                    },
                    onDragStart: function(ev) {
                        jQuery(this.target).addClass('gs-draggable');
                        Pachno.Project.Planning.Whiteboard.detectAvailableDropColumns(ev, this.target);
                    },
                    onDrag: function(ev) {
                        var droppables = jQuery(droppablesSelector);
                        var i = droppables.length;
                        while (--i > -1) {
                            if (this.hitTest(droppables[i], overlapThreshold)) {
                                jQuery(droppables[i]).addClass('drop-hover');
                            } else {
                                jQuery(droppables[i]).removeClass('drop-hover');
                            }
                        }
                    },
                    onDragEnd:function(ev) {
                        jQuery(this.target).removeClass('gs-draggable');
                        var droppables = jQuery(droppablesSelector);
                        var i = droppables.length;
                        var column_found = false;
                        while (--i > -1) {
                            if (this.hitTest(droppables[i], overlapThreshold)) {
                                Pachno.Project.Planning.Whiteboard.updateIssueColumn(ev, jQuery(this.target), jQuery(droppables[i]), {x: this.startX, y: this.startY});
                                column_found = true;
                            }
                        }
                        if (! column_found) TweenMax.to(this.target, .3, {x: this.startX, y: this.startY});
                        Pachno.Project.Planning.Whiteboard.resetAvailableDropColumns(ev);
                    },
                    zIndexBoost: false
                });
                var highZIndex = 1010;
                jQuery('#whiteboard .whiteboard-issue').each(function () {
                    jQuery(this).css('z-index', highZIndex--);
                });
            }

            if (!Pachno.Core.Pollers.planningpoller)
                Pachno.Core.Pollers.planningpoller = new PeriodicalExecuter(Pachno.Core.Pollers.Callbacks.whiteboardPlanningPoller, 6);
        };

        Pachno.Project.Planning.Whiteboard.retrieveIssue = function (issue_id, url, existing_element) {
            var milestone_id = $('whiteboard').dataset.milestoneId;
            var swimlane_type = $('whiteboard').dataset.swimlaneType;
            var column_id = ($(existing_element) != null && $(existing_element).dataset.columnId != undefined) ? $(existing_element).dataset.columnId : '';

            if ($(existing_element) != null) {
                if ($(existing_element).hasClassName('tbody')) {
                    var swimlane_identifier = $(existing_element).dataset.swimlaneIdentifier;
                }
                else {
                    var swimlane_identifier = $(existing_element).up('.tbody').dataset.swimlaneIdentifier;
                }
            }
            else {
                var swimlane_identifier = $('whiteboard').down('.tbody').dataset.swimlaneIdentifier;
            }

            Pachno.Main.Helpers.ajax(url, {
                params: 'issue_id=' + issue_id + '&milestone_id=' + milestone_id + '&swimlane_type=' + swimlane_type + '&column_id=' + column_id + '&swimlane_identifier=' + swimlane_identifier,
                url_method: 'get',
                loading: {indicator: (!$(existing_element)) ? 'retrieve_indicator' : 'issue_' + issue_id + '_indicator'},
                success: {
                    callback: function (json) {
                        if (swimlane_type != json.swimlane_type) {
                            Pachno.Project.Planning.Whiteboard.retrieveMilestoneStatus();
                            Pachno.Project.Planning.Whiteboard.retrieveWhiteboard();
                            return;
                        }
                        if (json.deleted == '1') {
                            if ($(existing_element)) $(existing_element).remove();
                        }
                        else if (!$(existing_element)) {
                            if (json.issue_details.milestone && json.issue_details.milestone.id == milestone_id && json.component != '') {
                                if ($('whiteboard').hasClassName('initialized')) {
                                    if ($('swimlane_'+json.swimlane_identifier+'_column_'+json.column_id)) {
                                        $('swimlane_'+json.swimlane_identifier+'_column_'+json.column_id).insert({top: json.component});
                                    } else {
                                        if (json.child_issue == '0') {
                                            $('whiteboard-headers').insert({after: json.component});
                                        }
                                    }
                                    Pachno.Project.Planning.Whiteboard.initializeDragDrop();
                                    Pachno.Project.Planning.Whiteboard.calculateColumnCounts();
                                    Pachno.Project.Planning.Whiteboard.calculateSwimlaneCounts(true);
                                    Pachno.Project.Planning.Whiteboard.retrieveMilestoneStatus();
                                }
                            }
                        } else {
                            var json_milestone_id = (json.issue_details.milestone && json.issue_details.milestone.id != undefined) ? parseInt(json.issue_details.milestone.id) : 0;
                            if (json_milestone_id == 0 || json.component == '') {
                                $(existing_element).remove();
                                Pachno.Project.Planning.Whiteboard.calculateColumnCounts();
                                Pachno.Project.Planning.Whiteboard.calculateSwimlaneCounts();
                                Pachno.Project.Planning.Whiteboard.retrieveMilestoneStatus();
                            } else if (json_milestone_id != milestone_id || json.swimlane_identifier != swimlane_identifier || json.column_id != column_id) {
                                $(existing_element).remove();
                                if ($('whiteboard').hasClassName('initialized')) {
                                    if ($('swimlane_'+json.swimlane_identifier+'_column_'+json.column_id)) {
                                        $('swimlane_'+json.swimlane_identifier+'_column_'+json.column_id).insert({top: json.component});
                                    } else {
                                        if (json.child_issue == '0') {
                                            $('whiteboard-headers').insert({after: json.component});
                                        }
                                    }
                                    Pachno.Project.Planning.Whiteboard.initializeDragDrop();
                                }
                                Pachno.Project.Planning.Whiteboard.calculateColumnCounts();
                                Pachno.Project.Planning.Whiteboard.calculateSwimlaneCounts();
                                Pachno.Project.Planning.Whiteboard.retrieveMilestoneStatus();
                            } else {
                                $(existing_element).replace(json.component);
                                Pachno.Project.Planning.Whiteboard.initializeDragDrop();
                                Pachno.Project.Planning.Whiteboard.calculateColumnCounts();
                                Pachno.Project.Planning.Whiteboard.calculateSwimlaneCounts();
                                Pachno.Project.Planning.Whiteboard.retrieveMilestoneStatus();
                            }
                        }
                    }
                }
            });
        };

        Pachno.Core.Pollers.Callbacks.whiteboardPlanningPoller = function () {
            if (!Pachno.Core.Pollers.Locks.planningpoller && $('whiteboard').hasClassName('initialized')) {
                Pachno.Core.Pollers.Locks.planningpoller = true;
                var pc = $('project_planning');
                var wb = $('whiteboard');
                var data_url = pc.dataset.pollUrl;
                var retrieve_url = pc.dataset.retrieveIssueUrl;
                var last_refreshed = pc.dataset.lastRefreshed;
                Pachno.Main.Helpers.ajax(data_url, {
                    url_method: 'get',
                    params: 'last_refreshed=' + last_refreshed + '&milestone_id=' + wb.dataset.milestoneId,
                    success: {
                        callback: function (json) {
                            if (parseInt(json.milestone_id) == parseInt(wb.dataset.milestoneId)) {
                                for (var i in json.ids) {
                                    if (json.ids.hasOwnProperty(i)) {
                                        var issue_details = json.ids[i];
                                        var issue_element = $('whiteboard_issue_' + issue_details.issue_id);
                                        if (!issue_element || parseInt(issue_element.dataset.lastUpdated) < parseInt(issue_details.last_updated)) {
                                            Pachno.Project.Planning.Whiteboard.retrieveIssue(issue_details.issue_id, retrieve_url, 'whiteboard_issue_' + issue_details.issue_id);
                                        }
                                    }
                                }
                                for (var i in json.backlog_ids) {
                                    if (json.backlog_ids.hasOwnProperty(i)) {
                                        var issue_details = json.backlog_ids[i];
                                        var issue_element = $('whiteboard_issue_' + issue_details.issue_id);
                                        if (!issue_element || parseInt(issue_element.dataset.lastUpdated) < parseInt(issue_details.last_updated)) {
                                            Pachno.Project.Planning.Whiteboard.retrieveIssue(issue_details.issue_id, retrieve_url, 'whiteboard_issue_' + issue_details.issue_id);
                                        }
                                    }
                                }
                            }

                            pc.dataset.lastRefreshed = get_current_timestamp();
                            wb.dataset.whiteboardUrl = json.whiteboard_url;
                            Pachno.Core.Pollers.Locks.planningpoller = false;
                        }
                    },
                    exception: {
                        callback: function () {
                            Pachno.Core.Pollers.Locks.planningpoller = false;
                        }
                    }
                });
            }
        };

        Pachno.Project.Planning.Whiteboard.checkNav = function() {
            if (window.location.hash) {
                if (parseInt($('selected_milestone_input').dataset.selectedValue) != parseInt(window.location.hash)) {
                    var hasharray = window.location.hash.substr(1).split('/');
                    var milestone_id = parseInt(hasharray[0]);
                    $('selected_milestone_input').childElements().each(function(milestone_li) {
                        if (parseInt(milestone_li.dataset.inputValue) == milestone_id) {
                            Pachno.Main.setFancyDropdownValue(milestone_li);
                            setTimeout(function () {
                                Pachno.Project.Planning.Whiteboard.retrieveMilestoneStatus();
                                Pachno.Project.Planning.Whiteboard.retrieveWhiteboard();
                            }, 150);
                        }
                    });
                }
            }
        }

        Pachno.Project.Planning.Whiteboard.initialize = function (options) {
            $('body').on('click', '#selected_milestone_input li', Pachno.Project.Planning.Whiteboard.retrieveMilestoneStatus);
            Event.observe(window, 'hashchange', Pachno.Project.Planning.Whiteboard.checkNav);
            Pachno.Project.Planning._initializeFilterSearch(true);
            if (window.location.hash) {
                Pachno.Project.Planning.Whiteboard.checkNav();
            } else {
                Pachno.Project.Planning.Whiteboard.retrieveMilestoneStatus();
                Pachno.Project.Planning.Whiteboard.retrieveWhiteboard();
            }

            jQuery('#planning_whiteboard_columns_form_row').sortable({
                handle: '.draggable',
                axis: 'x',
                update: Pachno.Project.Planning.Whiteboard.setSortOrder
            });

            $('planning_indicator').hide();
            $('planning_filter_title_input').enable();
        };

        Pachno.Project.Planning._initializeFilterSearch = function(whiteboard) {
            Pachno.ift_observers = {};
            var pfti = $('planning_filter_title_input');
            pfti.dataset.previousValue = '';
            var fk = 'pfti';
            if (whiteboard == undefined) whiteboard = false;
            pfti.on('keyup', function (event, element) {
                if (Pachno.ift_observers[fk])
                    clearTimeout(Pachno.ift_observers[fk]);
                if ((pfti.getValue().length >= 3 || pfti.getValue().length == 0) && pfti.getValue() != pfti.dataset.lastValue) {
                    Pachno.ift_observers[fk] = setTimeout(function () {
                        Pachno.Project.Planning.filterTitles(pfti.getValue(), whiteboard);
                        pfti.dataset.lastValue = pfti.getValue();
                    }, 500);
                }
            });
        };

        Pachno.Project.Planning.toggleMilestoneIssues = function(milestone_id) {
            var mi_issues = $('milestone_'+milestone_id+'_issues');
            var mi = $('milestone_'+milestone_id);
            mi.down('.toggle-issues').toggleClassName('button-pressed');
            if (!mi.hasClassName('initialized')) {
                mi.down('.toggle-issues').disable();
                mi_issues.removeClassName('collapsed');
                Pachno.Project.Planning.getMilestoneIssues(mi);
            } else {
                $('milestone_'+milestone_id+'_issues').toggleClassName('collapsed');
            }
        };

        Pachno.Project.Planning.toggleMilestoneSorting = function() {
            if ($('project_planning').hasClassName('milestone-sort')) {
                $('project_planning').removeClassName('milestone-sort left_toggled');
                jQuery('#milestones-list').sortable("destroy");
                jQuery('.milestone-issues.ui-sortable').sortable('enable');
            } else {
                $('project_planning').addClassName('milestone-sort left_toggled');

                jQuery('.milestone-issues.ui-sortable').sortable('disable');

                jQuery('#milestones-list').sortable({
                    update: Pachno.Project.Planning.sortMilestones,
                    axis: 'y',
                    items: '> .milestone-box',
                    helper: 'original',
                    tolerance: 'intersect'
                });
            }
        };

        Pachno.Project.Planning.initialize = function (options) {
            Pachno.Project.Planning.options = options;

            $$('.milestone-box.unavailable').each(Pachno.Project.Planning.initializeMilestoneDragDropSorting);
            var milestone_boxes = $$('.milestone-box.available');
            Pachno.Project.Planning.options.milestone_count = milestone_boxes.size() + 1;
            milestone_boxes.each(Pachno.Project.Planning.getMilestoneIssues);

            Pachno.Project.Planning._initializeFilterSearch();

            if ($('epics-list')) {
                Pachno.Main.Helpers.ajax($('epics-list').dataset.epicsUrl, {
                    url_method: 'get',
                    success: {
                        update: 'epics-list',
                        callback: function (json) {
                            var completed_milestones = $$('.milestone-box.available.initialized');
                            var multiplier = 100 / Pachno.Project.Planning.options.milestone_count;
                            var pct = Math.floor((completed_milestones.size() + 1) * multiplier);
                            $('planning_percentage_filler').setStyle({width: pct + '%'});

                            $('epics_toggler_button').enable();
                            Pachno.Project.Planning.initializeEpicDroptargets();
                            jQuery('body').on('click', '.epic', function (e) {
                                Pachno.Project.Planning.toggleEpicFilter(this);
                            });
                        }
                    }
                });
            }

            if ($('builds-list')) {
                Pachno.Main.Helpers.ajax($('builds-list').dataset.releasesUrl, {
                    url_method: 'get',
                    success: {
                        update: 'builds-list',
                        callback: function (json) {
                            Pachno.Project.Planning.initializeReleaseDroptargets();
                            jQuery('body').on('click', '.release', function (e) {
                                Pachno.Project.Planning.toggleReleaseFilter(this);
                            });
                        }
                    }
                });
            }
        };

        Pachno.Project.Planning.filterTitles = function (title, whiteboard) {
            $('planning_indicator').show();
            if (title !== '') {
                var matching = new RegExp(title, "i");
                $('project_planning').addClassName('issue_title_filtered');
                $$(whiteboard ? '.whiteboard-issue' : '.milestone-issue').each(function (issue) {
                    if (whiteboard) {
                        if (issue.down('.issue_header').innerHTML.search(matching) !== -1) {
                            issue.addClassName('title_unfiltered');
                        } else {
                            issue.removeClassName('title_unfiltered');
                        }
                    }
                    else {
                        if (issue.down('.issue_link').down('a').innerHTML.search(matching) !== -1) {
                            issue.addClassName('title_unfiltered');
                        } else {
                            issue.removeClassName('title_unfiltered');
                        }
                    }
                });
            } else {
                $('project_planning').removeClassName('issue_title_filtered');
                $$(whiteboard ? '.whiteboard-issue' : '.milestone-issue').each(function (issue) {
                    issue.removeClassName('title_unfiltered');
                });
            }
            $('planning_indicator').hide();
        };

        Pachno.Project.Planning.insertIntoMilestone = function (milestone_id, content, recalculate) {
            var milestone_list = $('milestone_' + milestone_id + '_issues');
            var $milestone_list_container = milestone_list.up('.milestone-issues-container');
            $milestone_list_container.removeClassName('empty');
            $('milestone_' + milestone_id + '_unassigned').hide();
            if (milestone_id == 0) {
                milestone_list.insert({bottom: content});
            } else {
                milestone_list.insert({top: content});
            }
            if (recalculate == 'all') {
                Pachno.Project.Planning.calculateAllMilestonesVisibilityDetails();
            } else {
                Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails(milestone_list);
            }
            Pachno.Project.Planning.calculateNewBacklogMilestoneDetails();
            if (milestone_id != 0) {
                setTimeout(Pachno.Project.Planning.sortMilestoneIssues({target: 'milestone_' + milestone_id + '_issues'}), 250);
            }
        };

        Pachno.Project.Planning.retrieveIssue = function (issue_id, url, existing_element) {
            Pachno.Main.Helpers.ajax(url, {
                params: 'issue_id=' + issue_id,
                url_method: 'get',
                loading: {indicator: (!$(existing_element)) ? 'retrieve_indicator' : 'issue_' + issue_id + '_indicator'},
                success: {
                    callback: function (json) {
                        if (json.deleted == '1') {
                            if ($(existing_element)) $(existing_element).up('.milestone-issue').remove();
                        }
                        else if (json.epic) {
                            if (!$(existing_element)) {
                                $('add_epic_container').insert({before: json.component});
                                setTimeout(Pachno.Project.Planning.initializeEpicDroptargets, 250);
                            } else {
                                $(existing_element).up('.milestone-issue').replace(json.component);
                            }
                        } else {
                            if (!$(existing_element)) {
                                if (json.issue_details.milestone && json.issue_details.milestone.id) {
                                    if ($('milestone_'+json.issue_details.milestone.id).hasClassName('initialized')) {
                                        Pachno.Project.Planning.insertIntoMilestone(json.issue_details.milestone.id, json.component);
                                    }
                                } else {
                                    Pachno.Project.Planning.insertIntoMilestone(0, json.component);
                                }
                            } else {
                                var json_milestone_id = (json.issue_details.milestone && json.issue_details.milestone.id != undefined) ? parseInt(json.issue_details.milestone.id) : 0;
                                if (parseInt($(existing_element).up('.milestone-box').dataset.milestoneId) == json_milestone_id) {
                                    $(existing_element).up('.milestone-issue').replace(json.component);
                                    Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails($('milestone_' + json_milestone_id + '_issues'));
                                    Pachno.Project.Planning.calculateNewBacklogMilestoneDetails();
                                } else {
                                    $(existing_element).up('.milestone-issue').remove();
                                    Pachno.Project.Planning.insertIntoMilestone(json_milestone_id, json.component, 'all');
                                }
                            }
                        }
                        if (json.issue_details.milestone && json.issue_details.milestone.id && json.milestone_percent_complete != null) {
                            $('milestone_' + json.issue_details.milestone.id + '_percentage_filler').setStyle({width: json.milestone_percent_complete + '%'});
                        }
                        Pachno.Project.Planning.filterTitles($('planning_filter_title_input').getValue());
                    }
                }
            });
        };

        Pachno.Core.Pollers.Callbacks.planningPoller = function () {
            var pc = $('project_planning');
            if (!Pachno.Core.Pollers.Locks.planningpoller && pc) {
                Pachno.Core.Pollers.Locks.planningpoller = true;
                var data_url = pc.dataset.pollUrl;
                var retrieve_url = pc.dataset.retrieveIssueUrl;
                var last_refreshed = pc.dataset.lastRefreshed;
                Pachno.Main.Helpers.ajax(data_url, {
                    url_method: 'get',
                    params: 'last_refreshed=' + last_refreshed,
                    success: {
                        callback: function (json) {
                            pc.dataset.lastRefreshed = get_current_timestamp();
                            for (var i in json.ids) {
                                if (json.ids.hasOwnProperty(i)) {
                                    var issue_details = json.ids[i];
                                    var issue_element = $('issue_' + issue_details.issue_id);
                                    if (!issue_element || parseInt(issue_element.dataset.lastUpdated) < parseInt(issue_details.last_updated)) {
                                        Pachno.Project.Planning.retrieveIssue(issue_details.issue_id, retrieve_url, 'issue_' + issue_details.issue_id);
                                    }
                                }
                            }
                            for (var i in json.backlog_ids) {
                                if (json.backlog_ids.hasOwnProperty(i)) {
                                    var issue_details = json.backlog_ids[i];
                                    var issue_element = $('issue_' + issue_details.issue_id);
                                    if (!issue_element || parseInt(issue_element.dataset.lastUpdated) < parseInt(issue_details.last_updated)) {
                                        Pachno.Project.Planning.retrieveIssue(issue_details.issue_id, retrieve_url, 'issue_' + issue_details.issue_id);
                                    }
                                }
                            }
                            for (var i in json.epic_ids) {
                                if (json.epic_ids.hasOwnProperty(i)) {
                                    var issue_details = json.epic_ids[i];
                                    var issue_element = $('epic_' + issue_details.issue_id);
                                    if (!issue_element || parseInt(issue_element.dataset.lastUpdated) < parseInt(issue_details.last_updated)) {
                                        Pachno.Project.Planning.retrieveIssue(issue_details.issue_id, retrieve_url, 'epic_' + issue_details.issue_id);
                                    }
                                }
                            }
                            Pachno.Core.Pollers.Locks.planningpoller = false;
                        }
                    },
                    exception: {
                        callback: function () {
                            Pachno.Core.Pollers.Locks.planningpoller = false;
                        }
                    }
                });
            }
        };

        Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails = function (list) {
            var list_issues = jQuery(list).find('.issue-container').not('.child_issue');
            var closed_issues = jQuery(list).find('.issue-container.issue_closed').not('.child_issue');
            var visible_issues = list_issues.filter(':visible');
            var sum_estimated_points = 0;
            var sum_estimated_hours = 0;
            var sum_estimated_minutes = 0;
            var sum_spent_points = 0;
            var sum_spent_hours = 0;
            var sum_spent_minutes = 0;
            visible_issues.each(function (index) {
                var elm = $(this);
                if (!elm.hasClassName('child_issue')) {
                    if (elm.dataset.estimatedPoints !== undefined)
                        sum_estimated_points += parseInt(elm.dataset.estimatedPoints);
                    if (elm.dataset.estimatedHours !== undefined)
                        sum_estimated_hours += parseInt(elm.dataset.estimatedHours);
                    if (elm.dataset.estimatedMinutes !== undefined)
                        sum_estimated_minutes += parseInt(elm.dataset.estimatedMinutes);
                    if (elm.dataset.spentPoints !== undefined)
                        sum_spent_points += parseInt(elm.dataset.spentPoints);
                    if (elm.dataset.spentHours !== undefined)
                        sum_spent_hours += parseInt(elm.dataset.spentHours);
                    if (elm.dataset.spentMinutes !== undefined)
                        sum_spent_minutes += parseInt(elm.dataset.spentMinutes);
                }
            });
            var num_visible_issues = visible_issues.size();
            var milestone_id = $(list).up('.milestone-box').dataset.milestoneId;

            if (num_visible_issues === 0) {
                if (list_issues.size() > 0) {
                    $('milestone_' + milestone_id + '_unassigned').hide();
                    $('milestone_' + milestone_id + '_unassigned_filtered').show();
                } else {
                    $('milestone_' + milestone_id + '_unassigned').show();
                    $('milestone_' + milestone_id + '_unassigned_filtered').hide();
                }
                $(list).up('.milestone-issues-container').addClassName('empty');
            } else {
                $('milestone_' + milestone_id + '_unassigned').hide();
                $('milestone_' + milestone_id + '_unassigned_filtered').hide();
                $(list).up('.milestone-issues-container').removeClassName('empty');
            }
            if (num_visible_issues !== list_issues.size() && milestone_id != '0') {
                $('milestone_' + milestone_id + '_issues_count').update(num_visible_issues + ' (' + list_issues.size() + ')');
            } else {
                $('milestone_' + milestone_id + '_issues_count').update(num_visible_issues);
            }
            sum_spent_hours += Math.floor(sum_spent_minutes / 60);
            sum_estimated_hours += Math.floor(sum_estimated_minutes / 60);
            sum_spent_minutes = sum_spent_minutes % 60;
            sum_estimated_minutes = sum_estimated_minutes % 60;
            $('milestone_' + milestone_id + '_points_count').update(sum_spent_points + ' / ' + sum_estimated_points);
            if (sum_spent_minutes != 0) {
                sum_spent_hours += ':' + ((sum_spent_minutes.toString().length == 1) ? '0' : '') + sum_spent_minutes;
            }
            if (sum_estimated_minutes != 0) {
                sum_estimated_hours += ':' + ((sum_estimated_minutes.toString().length == 1) ? '0' : '') + sum_estimated_minutes;
            }
            $('milestone_' + milestone_id + '_hours_count').update(sum_spent_hours + ' / ' + sum_estimated_hours);
        };

        Pachno.Project.Planning.calculateAllMilestonesVisibilityDetails = function () {
            jQuery('.milestone-box.initialized').find('.milestone-issues').each(function (index) {
                var was_collapsed = $(this).hasClassName('collapsed');
                $(this).removeClassName('collapsed');
                Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails(this);
                if (was_collapsed && parseInt($(this).up('.milestone-box').dataset.milestoneId) !== 0) $(this).addClassName('collapsed');
            });
        };

        Pachno.Project.Planning.calculateNewBacklogMilestoneDetails = function (event, ui) {
            if (event === undefined || jQuery(ui.item).hasClass('new_milestone_marker')) {
                var nbmm = (event === undefined) ? $('new_backlog_milestone_marker') : $(ui.placeholder[0]);
                var num_issues = 0;
                var sum_points = 0;
                var sum_hours = 0;
                var sum_minutes = 0;
                var include_closed = $('milestones-list').hasClassName('show_closed');
                jQuery('.milestone-issue').removeClass('included');
                nbmm.up('.milestone-issues').childElements().each(function (elm) {
                    elm.addClassName('included');
                    if (!(elm.hasClassName('new_milestone_marker') && !elm.hasClassName('ui-sortable-helper')) && !elm.hasClassName('ui-element-placeholder')) {
                        if (!elm.hasClassName('new_milestone_marker')) {
                            if (include_closed || !elm.hasClassName('issue_closed'))
                                num_issues++;
                            if (!elm.hasClassName('child_issue')) {
                                if (elm.down('.issue-container').dataset.estimatedPoints !== undefined)
                                    sum_points += parseInt(elm.down('.issue-container').dataset.estimatedPoints);
                                if (elm.down('.issue-container').dataset.estimatedHours !== undefined)
                                    sum_hours += parseInt(elm.down('.issue-container').dataset.estimatedHours);
                                if (elm.down('.issue-container').dataset.estimatedMinutes !== undefined)
                                    sum_minutes += parseInt(elm.down('.issue-container').dataset.estimatedMinutes);
                            }
                        }
                    } else {
                        throw $break;
                    }
                });
                sum_hours += Math.floor(sum_minutes / 60);
                sum_minutes = sum_minutes % 60;
                $('new_backlog_milestone_issues_count').update(num_issues);
                $('new_backlog_milestone_points_count').update(sum_points);
                if (sum_minutes != 0) {
                    sum_hours += ':' + ((sum_minutes.toString().length == 1) ? '0' : '') + sum_minutes;
                }
                $('new_backlog_milestone_hours_count').update(sum_hours);
            }
        };

        Pachno.Project.Planning.sortMilestones = function (event, ui) {
            var list = $(event.target);
            var url = list.dataset.sortUrl;
            var items = '';
            list.childElements().each(function (milestone, index) {
                if (milestone.dataset.milestoneId !== undefined) {
                    items += '&milestone_ids['+index+']=' + milestone.dataset.milestoneId;
                }
            });
            Pachno.Main.Helpers.ajax(url, {
                url_method: 'post',
                additional_params: items,
                loading: {indicator: 'planning_indicator'}
            });
        };

        Pachno.Project.Planning.doSortMilestoneIssues = function (list) {
            var url = list.up('.milestone-box').dataset.issuesUrl;
            var items = '';
            list.childElements().each(function (issue) {
                if (issue.dataset.issueId !== undefined) {
                    items += '&issue_ids[]=' + issue.dataset.issueId;
                }
            });
            Pachno.Main.Helpers.ajax(url, {
                url_method: 'post',
                additional_params: items,
                loading: {indicator: list.up('.milestone-box').down('.planning_indicator')}
            });
        };

        Pachno.Project.Planning.sortMilestoneIssues = function (event, ui) {
            var list = $(event.target);
            var issue = $(ui.item[0]);
            if (issue.dataset.sortCancel) {
                issue.dataset.sortCancel = null;
                jQuery(this).sortable("cancel");
            } else {
                if (ui !== undefined && ui.item.hasClass('new_milestone_marker')) {
                    Pachno.Project.Planning.calculateNewBacklogMilestoneDetails();
                } else {
                    Pachno.Project.Planning.doSortMilestoneIssues(list);
                }
            }
        };

        Pachno.Project.Planning.moveIssue = function (event, ui) {
            var issue = $(ui.item[0]);
            if (issue.dataset.sortCancel) {
                issue.dataset.sortCancel = null;
                jQuery(this).sortable("cancel");
            } else {
                if (issue.hasClassName('milestone-issue')) {
                    var list = $(event.target);
                    var url = list.up('.milestone-box').dataset.assignIssueUrl;
                    var original_list = $(ui.sender[0]);
                    Pachno.Main.Helpers.ajax(url, {
                        additional_params: 'issue_id=' + issue.dataset.issueId,
                        loading: {indicator: list.up('.milestone-box').down('.planning_indicator')},
                        complete: {
                            callback: function (json) {
                                if (list.up('.milestone-box').hasClassName('initialized')) {
                                    issue.down('.issue-container').dataset.lastUpdated = get_current_timestamp();
                                    Pachno.Project.Planning.doSortMilestoneIssues(list);
                                    Pachno.Core.Pollers.Callbacks.planningPoller();
                                    Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails(list);
                                    Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails(original_list);
                                } else {
                                    issue.remove();
                                    var milestone_id = list.up('.milestone-box').dataset.milestoneId;
                                    $('milestone_' + milestone_id + '_issues_count').update(json.issues);
                                    $('milestone_' + milestone_id + '_points_count').update(json.points);
                                    $('milestone_' + milestone_id + '_hours_count').update(json.hours);
                                }
                            }
                        }
                    });
                }
            }
        };

        Pachno.Project.Planning.toggleSwimlaneDetails = function (selected_item) {
            $('agileboard-swimlane-details-container').childElements().each(Element.hide);
            $('agileboard_swimlane_' + jQuery(selected_item).val() + '_container').show();
        };

        Pachno.Project.Planning.toggleSwimlaneExpediteDetails = function(selected_item) {
            $('agileboard_swimlane_expedite_container_details').childElements().each(Element.hide);
            $('swimlane_expedite_identifier_' + jQuery(selected_item).val() + '_values').show();
        };

        Pachno.Project.Planning.removeAgileBoard = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                url_method: 'delete',
                loading: {
                    indicator: 'dialog_indicator',
                    callback: function () {
                        ['dialog_yes', 'dialog_no'].each(function (elm) {
                            elm.addClassName('disabled');
                        });
                    }
                },
                success: {
                    callback: function (json) {
                        $('agileboard_' + json.board_id).remove();
                        Pachno.Main.Helpers.Dialog.dismiss();
                        if ($('agileboards').childElements().size() == 0) {
                            $('onboarding-no-boards').show();
                        }
                    }
                }
            });
        };

        Pachno.Project.Planning.saveAgileBoard = function (item) {
            var url = item.action;
            Pachno.Main.Helpers.ajax(url, {
                form: 'edit-agileboard-form',
                success: {
                    callback: function (json) {
                        if ($('agileboards')) {
                            if ($('agileboard_' + json.id)) {
                                $('agileboard_' + json.id).replace(json.component);
                            } else {
                                $('onboarding-no-boards').hide();
                                var container = $('agileboards');
                                container.insert(json.component);
                            }
                            Pachno.clearFormSubmit(jQuery(item));
                            Pachno.Main.Helpers.Backdrop.reset();
                        } else if ($('project_planning') && parseInt($('project_planning').dataset.boardId) == parseInt(json.id) && $('project_planning').hasClassName('whiteboard')) {
                            Pachno.Main.Helpers.Backdrop.reset();
                            Pachno.Project.Planning.Whiteboard.retrieveMilestoneStatus();
                            Pachno.Project.Planning.Whiteboard.retrieveWhiteboard();
                        } else if ($('project_planning') && parseInt($('project_planning').dataset.boardId) == parseInt(json.id)) {
                            var backlog = $('milestone_0');
                            Pachno.Main.Helpers.Backdrop.reset();
                            if (backlog.dataset.backlogSearch != json.backlog_search) {
                                $('planning_indicator').show();
                                window.location.reload(true);
                            } else {
                                backlog.removeClassName('initialized');
                                $('milestone_0_issues').update('');
                                $('milestone_0_issues').removeClassName('ui-sortable');
                                backlog.down('.planning_indicator').show();
                                Pachno.Project.Planning.initialize(Pachno.Project.Planning.options);
                            }
                        }
                    }
                }
            });
        };

        Pachno.Main.updateFancyDropdownLabel = function ($dropdown) {
            var $label = $dropdown.find('> .value');
            if ($label.length > 0) {
                var auto_close = false;
                var values = [];
                $dropdown.find('input[type=checkbox],input[type=radio]').each(function () {
                    var $input = jQuery(this);

                    if ($input.attr('type') == 'radio') {
                        auto_close = true;
                    }

                    if ($input.is(':checked')) {
                        var $label = jQuery($input.next('label')),
                            $value = jQuery($label.find('.value')[0]);

                        if ($value.text() != '') {
                            values.push($value.text());
                        }
                    }
                });

                if (values.length > 0) {
                    $dropdown.removeClass('no-value');
                    $label.html(values.join(', '));
                } else {
                    $dropdown.addClass('no-value');
                    $label.html($dropdown.data('default-label'));
                }

                if (auto_close) {
                    $dropdown.removeClass('active');
                }
            }
        };

        Pachno.Main.updateFancyDropdownValues = function (event) {
            event.stopPropagation();
            event.stopImmediatePropagation();
            event.preventDefault();
            var $dropdown = jQuery(this).closest('.fancy-dropdown');
            Pachno.Main.updateFancyDropdownLabel($dropdown);
        };

        Pachno.Main.updateWidgets = function () {
            return new Promise(function (resolve, reject) {
                jQuery("img[data-src]:not([data-src-processed])").each(function(){
                    var $img = jQuery(this);
                    $img.attr('src', $img.data('src')).data('src-processed', true);
                });
                jQuery('.fancy-dropdown').each(function () {
                    Pachno.Main.updateFancyDropdownLabel(jQuery(this));
                });
                jQuery('.fancy-tag-input-container').each(function () {
                    let $container = jQuery(this);

                    let $input = jQuery($container.find('input[type=text]')[0]);
                    let values = $input.val().split(',');
                    values.forEach((value) => {
                        let real_value = value.trim();
                    })
                });

                resolve();
            });
        };

        Pachno.Project.Milestone.markFinished = function (form) {
            var url = form.action;
            var milestone_id = form.dataset.milestoneId;
            Pachno.Main.Helpers.ajax(url, {
                form: form,
                loading: {
                    indicator: 'milestone_edit_indicator',
                    callback: function () {
                        $('mark_milestone_finished_form').select('input.button').each(Element.disable);
                    }
                },
                success: {
                    remove: 'milestone_' + milestone_id,
                    callback: function (json) {
                        Pachno.Main.Helpers.Backdrop.reset();
                        if (json.component) {
                            $('milestones-list').insert(json.component);
                            setTimeout(function () {
                                Pachno.Project.Planning.getMilestoneIssues($('milestone_' + json.new_milestone_id), Pachno.Project.Planning.initializeDragDropSorting);
                            }, 250);
                        } else {
                            Pachno.Core.Pollers.Callbacks.planningPoller();
                        }
                    }
                },
                failure: {
                    callback: function () {
                        $('mark_milestone_finished_form').select('input.button').each(Element.enable);
                    }
                }
            });
        };

        Pachno.Project.Milestone.save = function (form, on_board) {
            var submit_button = jQuery(form).find('.form-row.submit-container button[type=submit]');

            if (submit_button.length) {
                submit_button.prop('disabled', true);
                submit_button.addClass('submitting');
            }

            var url = form.action;
            var include_selected_issues = $('include_selected_issues').getValue() == 1;

            var data = new FormData(form);
            if (include_selected_issues) {
                $$('.milestone-issue.included').each(function (issue) {
                    data.append( "issues[]", issue.dataset.issueId);
                });
            }

            return new Promise(function (resolve, reject) {
                fetch(url, {
                        method: 'POST',
                        body: data
                    })
                    .then((_) => _.json())
                    .then(function (json) {
                        if ($('no_milestones')) {
                            $('no_milestones').hide();
                        }

                        $$('.milestone-issue.included').each(function (issue) { issue.remove(); });
                        Pachno.Main.Helpers.Backdrop.reset();
                        if (jQuery('#milestones-list').length) {
                            jQuery('#milestones-list').append(json.component);
                        }

                        if (on_board) {
                            if (!include_selected_issues) {
                                setTimeout(function () {
                                    Pachno.Project.Planning.getMilestoneIssues($('milestone_' + json.milestone_id));
                                }, 250);
                            } else {
                                Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails($('milestone_0_issues'));
                                // Pachno.Project.Planning.initializeDragDropSorting();
                            }
                        }
                    });
            });
            // Pachno.Main.Helpers.ajax(url, {
            //     form: form,
            //     additional_params: issues,
            //     loading: {indicator: 'milestone_edit_indicator'},
            //     success: {
            //         reset: 'edit_milestone_form',
            //         hide: 'no_milestones',
            //         callback: function (json) {
            //             $$('.milestone-issue.included').each(function (issue) { issue.remove(); });
            //             Pachno.Main.Helpers.Backdrop.reset();
            //             if ($('milestone_' + json.milestone_id)) {
            //                 $('milestone_' + json.milestone_id).replace(json.component);
            //             } else {
            //                 $('milestones-list').insert(json.component);
            //             }
            //             if (on_board) {
            //                 if (!include_selected_issues) {
            //                     setTimeout(function () {
            //                         Pachno.Project.Planning.getMilestoneIssues($('milestone_' + json.milestone_id), Pachno.Project.Planning.initializeDragDropSorting);
            //                     }, 250);
            //                 } else {
            //                     Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails($('milestone_0_issues'));
            //                     Pachno.Project.Planning.initializeDragDropSorting();
            //                 }
            //             }
            //             Pachno.Project.Milestone.selectFromHash();
            //         }
            //     }
            // });
        }

        Pachno.Project.Milestone.selectFromHash = function () {
            var hash = window.location.hash;

            if (hash != undefined && hash.indexOf('roadmap_milestone_') == 1) {
                jQuery(hash + '_details_link').eq(0).find('> a:first-child').trigger('click');
            }
        }

        Pachno.Project.Milestone.remove = function (url, milestone_id) {
            Pachno.Main.Helpers.ajax(url, {
                url_method: 'delete',
                loading: {
                    indicator: 'dialog_indicator',
                },
                success: {
                    callback: function (json) {
                        $('milestone_' + milestone_id).remove();
                        Pachno.Main.Helpers.Dialog.dismiss();
                        Pachno.Main.Helpers.Backdrop.reset();
                        if ($('milestones-list').childElements().size() == 0)
                            $('no_milestones').show();
                        Pachno.Core.Pollers.Callbacks.planningPoller();
                    }
                }
            });
        }

        Pachno.Project.Build.doAction = function (url, bid, action, update) {
            var update_elm = (update == 'all') ? 'build_table' : 'build_list_' + bid;
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'build_' + bid + '_indicator',
                    hide: 'build_' + bid + '_info'
                },
                success: {
                    update: update_elm
                },
                complete: {
                    show: 'build_' + bid + '_info'
                }
            });
        }

        Pachno.Project.Build.update = function (url, bid) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'edit_build_' + bid,
                loading: {
                    indicator: 'build_' + bid + '_indicator',
                    hide: 'build_' + bid + '_info'
                },
                success: {
                    update: 'build_list_' + bid
                },
                complete: {
                    show: 'build_' + bid + '_info'
                }
            });
        }

        Pachno.Project.Build.addToOpenIssues = function (url, bid) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'addtoopen_build_' + bid,
                loading: {
                    indicator: 'build_' + bid + '_indicator',
                    hide: 'build_' + bid + '_info'
                },
                success: {
                    hide: 'addtoopen_build_' + bid
                },
                complete: {
                    show: 'build_' + bid + '_info'
                }
            });
        }

        Pachno.Project.Build.remove = function (url, bid, b_type, edition_id) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    show: 'fullpage_backdrop_indicator',
                    indicator: 'fullpage_backdrop',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop', 'build_' + bid + '_info'],
                    callback: function () {
                        $('build_' + bid + '_indicator').addClassName('selected_red');
                    }
                },
                success: {
                    remove: ['show_build_' + bid],
                    callback: function () {
                        Pachno.Main.Helpers.Dialog.dismiss();
                        if ($(b_type + '_builds_' + edition_id).childElements().size() == 0) {
                            $('no_' + b_type + '_builds_' + edition_id).show();
                        }
                    }
                },
                failure: {
                    show: 'build_' + bid + '_info',
                    hide: 'del_build_' + bid,
                    callback: function () {
                        $('build_' + bid + '_indicator').removeClassName('selected_red');
                    }
                }
            });
        };

        Pachno.Project.Build.add = function (url, edition_id) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'add_build_form',
                loading: {indicator: 'build_add_indicator'},
                success: {
                    reset: 'add_build_form',
                    hide: 'no_builds_' + edition_id,
                    update: {element: 'builds_' + edition_id, insertion: true, from: 'html'}
                }
            });
        };

        Pachno.Project.Component.save = function (form) {
            Pachno.Core.fetchPostHelper(form)
                .then(Pachno.Core.fetchPostDefaultFormHandler)
                .then(([$form, response]) => {
                    if (response.ok) {
                        response.json().then(function (json) {
                            const $component_container = jQuery('[data-component][data-id='+json.item.id+']');
                            if ($component_container.length > 0) {
                                $component_container.replaceWith(json.component);
                            } else {
                                const $components_container = jQuery('#project-components-list');
                                if ($components_container.length > 0) {
                                    $components_container.append(json.component);
                                }
                            }
                            $form[0].reset();
                        })
                    }
                });
        };

        Pachno.Project.Component.remove = function (url, id) {
            fetch(url, { method: 'DELETE' })
                .then(function (response) {
                    response.json().then(function (json) {
                        Pachno.Main.Helpers.Dialog.dismiss();
                        if (response.ok) {
                            jQuery('[data-component][data-id=' + id + ']').remove();
                        } else {
                            Pachno.Main.Helpers.Message.error(json.error);
                        }
                    })
                        .catch(function (error) {
                            Pachno.Main.Helpers.Dialog.dismiss();
                            Pachno.Main.Helpers.Message.error(error);
                        });
                });
        }

        Pachno.Project.Edition.showOptions = function ($item) {
            Pachno.Config.loadComponentOptions(
                {
                    container: '#project-editions-list-container',
                    options: '#selected-edition-options',
                    component: '.project-edition'
                },
                $item
            );
        };

        Pachno.Project.Edition.save = function (form) {
            Pachno.Core.fetchPostHelper(form)
                .then(Pachno.Core.fetchPostDefaultFormHandler)
                .then(([$form, response]) => {
                    if (response.ok) {
                        response.json().then(function (json) {
                            const $edition_container = jQuery('[data-edition][data-id='+json.item.id+']');
                            if ($edition_container.length > 0) {
                                $edition_container.replaceWith(json.edition);
                            } else {
                                const $editions_container = jQuery('#project-editions-list');
                                if ($editions_container.length > 0) {
                                    $editions_container.append(json.edition);
                                }
                            }
                            $form[0].reset();
                            jQuery('#project-editions-list-container').removeClass('active');
                            jQuery('#selected-edition-options').html('');
                        })
                    }
                });
        };

        Pachno.Project.Edition.remove = function (url, id) {
            fetch(url, { method: 'DELETE' })
                .then(function (response) {
                    response.json().then(function (json) {
                        Pachno.Main.Helpers.Dialog.dismiss();
                        if (response.ok) {
                            jQuery('[data-edition][data-id=' + id + ']').remove();
                            jQuery('#project-editions-list-container').removeClass('active');
                            jQuery('#selected-edition-options').html('');
                        } else {
                            Pachno.Main.Helpers.Message.error(json.error);
                        }
                    })
                        .catch(function (error) {
                            Pachno.Main.Helpers.Dialog.dismiss();
                            Pachno.Main.Helpers.Message.error(error);
                        });
                });
        }

        Pachno.Project.saveOther = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'project_other',
                loading: {indicator: 'settings_save_indicator'}
            });
        };

        Pachno.Project.submitAdvancedSettings = function (url) {
            Pachno.Project._submitDetails(url, 'project_settings');
        }

        Pachno.Project.submitDisplaySettings = function (url) {
            Pachno.Project._submitDetails(url, 'project_other');
        }

        Pachno.Project.submitInfo = function (url, pid) {
            Pachno.Project._submitDetails(url, 'project_info', pid);
        }

        Pachno.Project.submitLinks = function (url, pid) {
            Pachno.Project._submitDetails(url, 'project_links', pid);
        }

        Pachno.Project._submitDetails = function (url, form_id, pid) {
            Pachno.Main.Helpers.ajax(url, {
                form: form_id,
                success: {
                    callback: function (json) {
                        if ($('project_name_span'))
                            $('project_name_span').update($('project_name_input').getValue());
                        if ($('project_description_span')) {
                            if ($('project_description_input').getValue()) {
                                $('project_description_span').update(json.project_description);
                                $('project_no_description').hide();
                            } else {
                                $('project_description_span').update('');
                                $('project_no_description').show();
                            }
                        }
                        if ($('project_key_span'))
                            $('project_key_span').update(json.project_key);
                        if ($('sidebar_link_scrum') && $('use_scrum').getValue() == 1)
                            $('sidebar_link_scrum').show();
                        else if ($('sidebar_link_scrum'))
                            $('sidebar_link_scrum').hide();

                        ['edition', 'component'].each(function (element) {
                            if ($('enable_' + element + 's').getValue() == 1) {
                                $('add_' + element + '_button').show();
                                $('project_' + element + 's').show();
                                $('project_' + element + 's_disabled').hide();
                            } else {
                                $('add_' + element + '_button').hide();
                                $('project_' + element + 's').hide();
                                $('project_' + element + 's_disabled').show();
                            }
                        });

                        if (pid != undefined && $('project_box_' + pid) != undefined)
                            $('project_box_' + pid).update(json.content);
                    }
                }
            });
        }

        Pachno.Project.findDevelopers = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'find_dev_form',
                loading: {indicator: 'find_dev_indicator'},
                success: {
                    update: 'find_dev_results',
                    callback: function () {
                        let $form = jQuery('#find_dev_form');
                        $form.removeClass('submitting');
                        $form.find('button[type=submit]').each(function () {
                            var $button = jQuery(this);
                            $button.removeClass('auto-disabled');
                            $button.attr("disabled", false);
                        })
                    }
                }
            });
        }

        Pachno.Project._updateUserFromJSON = function (object, field) {
            if (object.id == 0) {
                $(field + '_name').hide();
                $('no_' + field).show();
            } else {
                $(field + '_name').update(object.name);
                $('no_' + field).hide();
                $(field + '_name').show();
            }
        }

        Pachno.Project.setUser = function (url, field) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {indicator: field + '_spinning'},
                success: {
                    hide: field + '_change',
                    callback: function (json) {
                        Pachno.Project._updateUserFromJSON(json.field, field);
                    }
                }
            });
        }

        Pachno.Project.assign = function (url, container_id) {
            var role_id = $(container_id).down('select').getValue();
            var parameters = "&role_id=" + role_id;
            Pachno.Main.Helpers.ajax(url, {
                params: parameters,
                loading: {indicator: 'assign_dev_indicator'},
                success: {update: 'assignees_list'}
            });
        }

        Pachno.Project.removeAssignee = function (url, type, id) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'remove_assignee_' + type + '_' + id + '_indicator',
                    hide: 'assignee_' + type + '_' + id + '_link'
                },
                success: {
                    remove: 'assignee_' + type + '_' + id + '_row',
                    callback: function () {
                        if ($('project_team_' + type + 's').childElements().size() == 0) {
                            $('project_team_' + type + 's').hide();
                            $('no_project_team_' + type + 's').show();
                        }
                    }
                }
            });
        }

        Pachno.Project.workflow = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'workflow_form2',
                loading: {indicator: 'update_workflow_indicator'},
                success: {callback: function () {
                    Pachno.Main.Helpers.Backdrop.reset();
                }}
            });
        };

        Pachno.Project.workflowtable = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'workflow_form',
                loading: {
                    indicator: 'change_workflow_indicator'
                },
                success: {
                    update: 'change_workflow_table',
                    hide: 'change_workflow_box',
                    show: 'change_workflow_table'
                }
            });
        };

        Pachno.Project.updatePrefix = function (url, project_id) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'project_info',
                success: {
                    update: 'project_key_input',
                    callback: function () {
                        Pachno.clearFormSubmit(jQuery('#project_info'));
                    }
                }
            });
        };

        Pachno.Project.clearReleaseCenterFilters = function () {
            var prcc = $('project_release_center_container');
            ['only_archived', 'only_active', 'only_downloads'].each(function (cn) {
                prcc.removeClassName(cn);
            });
        };

        Pachno.Project.checkAndToggleNoBuildsMessage = function () {
            $$('.simple-list').each(function (elem) {
                // If this list does not contain builds continue.
                if (elem.id.indexOf('active_builds_') !== 0) return;

                // We assume no build is visible.
                var one_build_visible = false;

                $(elem).childElements().each(function (elem) {
                    // If this child - build is not visible continue.
                    if (! jQuery('#' + elem.id).is(':visible')) return;

                    // Once we find visible build set flag and break this loop.
                    one_build_visible = true;
                    return false;
                });

                // Hide or show no builds message based on one build visible flag.
                if (one_build_visible) {
                    $('no_' + elem.id).hide();
                }
                else {
                    $('no_' + elem.id).show();
                }
            });
        };

        Pachno.Project.clearRoadmapFilters = function () {
            var prp = $('project_roadmap_page');
            ['upcoming', 'past'].each(function (cn) {
                prp.removeClassName(cn);
            });

            var hash = window.location.hash;

            if (hash != undefined && hash.indexOf('roadmap_milestone_') == 1) {
                window.location.hash = '';
            }
        };

        Pachno.Project.showRoadmap = function () {
            $('milestone_details_overview').hide();
            $('project_roadmap').show();
            jQuery('#planning_board_settings_gear').show();
        }

        Pachno.Project.showMilestoneDetails = function (url, milestone_id, force) {
            $$('body')[0].setStyle({'overflow': 'auto'});

            var force = force || false;

            if (force && $('milestone_details_' + milestone_id)) {
                $('milestone_details_' + milestone_id).remove();
            }

            jQuery('#project_planning_action_strip .more_actions_dropdown, #planning_board_settings_gear').hide();

            if (!$('milestone_details_' + milestone_id)) {
                window.location.hash = 'roadmap_milestone_' + milestone_id;

                Pachno.Main.Helpers.ajax(url, {
                    url_method: 'get',
                    loading: {
                        indicator: 'fullpage_backdrop',
                        show: 'fullpage_backdrop_indicator',
                        hide: ['fullpage_backdrop_content', 'project_roadmap']
                    },
                    success: {
                        show: 'milestone_details_overview',
                        update: 'milestone_details_overview'
                    }
                });
            } else {
                $('project_roadmap').hide();
                $('milestone_details_overview').show();
            }
        }

        Pachno.Project.toggleLeftSelection = function (item) {
            $(item).up('ul').childElements().each(function (elm) {
                elm.removeClassName('selected');
            });
            $(item).up('li').addClassName('selected');
        };

        Pachno.Config.Import.importCSV = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'import_csv_form',
                loading: {
                    indicator: 'csv_import_indicator',
                    hide: 'csv_import_error'
                },
                failure: {
                    show: 'csv_import_error',
                    callback: function (json) {
                        $('csv_import_error_detail').update(json.errordetail);
                    }
                }
            });
        }

        Pachno.Config.Import.getImportCsvIds = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'id_zone_indicator',
                    hide: 'id_zone_content'
                },
                success: {
                    update: 'id_zone_content',
                    show: 'id_zone_content'
                }
            });
        }

        Pachno.Config.updateCheck = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'update_spinner',
                    hide: 'update_button'
                },
                success: {
                    callback: function (json) {
                        (json.uptodate) ?
                            Pachno.Main.Helpers.Message.success(json.title, json.message) :
                            Pachno.Main.Helpers.Message.error(json.title, json.message);
                    }
                },
                complete: {
                    show: 'update_button'
                }
            });
        }

        Pachno.Config.Issuetype.save = function (form) {
            var $form = jQuery(form),
                data = new FormData($form[0]);

            $form.find('.error-container').removeClass('invalid');
            $form.find('.error-container > .error').html('');
            $form.addClass('submitting');
            $form.find('.button.primary').attr('disabled', true);

            fetch($form.attr('action'), {
                method: 'POST',
                body: data
            })
                .then(function (response) {
                    response.json().then(function (json) {
                        if (response.ok) {
                            const $issue_type_container = jQuery('[data-issue-type][data-id='+json.issue_type.id+']');
                            if ($issue_type_container.length > 0) {
                                $issue_type_container.find('[data-name]').html(json.issue_type.name);
                            } else {
                                const $issue_types_container = jQuery('#issue-types-list');
                                if ($issue_types_container.length > 0) {
                                    $issue_types_container.append(json.component);
                                }
                            }
                            Pachno.Main.Helpers.Backdrop.reset();
                        } else {
                            $form.find('.error-container > .error').html(json.error);
                            $form.find('.error-container').addClass('invalid');
                        }

                        $form.removeClass('submitting');
                        $form.find('.button.primary').attr('disabled', false);
                    });
                });
        };

        Pachno.Config.Issuetype.remove = function (url, id) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop']
                },
                success: {
                    remove: 'issuetype_' + id + '_box',
                    callback: Pachno.Main.Helpers.Dialog.dismiss
                }
            });
        }

        Pachno.Config.Issuetype.add = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'add_issuetype_form',
                loading: {
                    reset: 'add_issuetype_form',
                    indicator: 'add_issuetype_indicator'
                },
                success: {
                    update: {element: 'issuetypes_list', insertion: true}
                }
            });
        }

        Pachno.Config.Issuetype.toggleForScheme = function (url, issuetype_id, scheme_id, action) {
            var hide_element = 'type_toggle_' + issuetype_id + '_' + action;
            var show_element = 'type_toggle_' + issuetype_id + '_' + ((action == 'enable') ? 'disable' : 'enable');
            var cb;
            if (action == 'enable') {
                cb = function (json) {
                    $('issuetype_' + json.issuetype_id + '_box').addClassName("greenbox");
                    $('issuetype_' + json.issuetype_id + '_box').removeClassName("greybox");
                };
            } else {
                cb = function (json) {
                    $('issuetype_' + json.issuetype_id + '_box').removeClassName("greenbox");
                    $('issuetype_' + json.issuetype_id + '_box').addClassName("greybox");
                };
            }
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'issuetype_' + issuetype_id + '_indicator',
                    hide: hide_element
                },
                success: {
                    show: show_element,
                    callback: cb
                }
            });
        }

        Pachno.Config.IssuetypeScheme.save = function (form) {
            const $form = jQuery(form),
                data = new FormData($form[0]);

            $form.find('.error-container').removeClass('invalid');
            $form.find('.error-container > .error').html('');
            $form.addClass('submitting');

            fetch($form.attr('action'), {
                method: 'POST',
                body: data
            })
                .then(function (response) {
                    response.json().then(function (json) {
                        if (!response.ok) {
                            $form.find('.error-container > .error').html(json.error);
                            $form.find('.error-container').addClass('invalid');
                        }
                        $form.removeClass('submitting');
                    });
                });
        };

        Pachno.Config.IssuetypeScheme.showOptions = function ($item) {
            Pachno.Config.loadComponentOptions(
                {
                    container: '#issue-type-configuration-container',
                    options: '#selected-issue-type-options',
                    component: '.issue-type-scheme-issue-type'
                },
                $item
            );
        };

        Pachno.Config.IssuetypeScheme.addField = function (url, key) {
            const $container = jQuery('#issue-type-fields-list'),
                $add_list = jQuery('#add-issue-field-list');

            fetch(url, {
                method: 'GET'
            })
                .then(function (response) {
                    response.json().then(function (json) {
                        if (response.ok) {
                            $container.append(json.content);
                            jQuery('.list-item[data-issue-field][data-id=' + key + ']').addClass('disabled');
                        } else {
                            Pachno.Main.Helpers.Message.error(json.error);
                        }
                    });
                });
        };

        Pachno.Config.IssuetypeScheme.saveOptions = function (form) {
            const $container = jQuery('#issue-type-configuration-container'),
                $form = jQuery(form),
                data = new FormData($form[0]),
                $options = jQuery('#selected-issue-type-options');

            $form.find('.error-container').removeClass('invalid');
            $form.find('.error-container > .error').html('');
            $form.addClass('submitting');
            $form.find('.button.primary').attr('disabled', true);

            fetch($form.attr('action'), {
                method: 'POST',
                body: data
            })
                .then(function (response) {
                    response.json().then(function (json) {
                        if (response.ok) {
                            $container.removeClass('active');
                            $container.find('.issue-type-scheme-issue-type').removeClass('active');
                            $options.html('');
                        } else {
                            $form.find('.error-container > .error').html(json.error);
                            $form.find('.error-container').addClass('invalid');
                        }
                    });
                });
        };

        Pachno.Config.IssuetypeScheme.copy = function (url, scheme_id) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'copy_issuetype_scheme_' + scheme_id + '_form',
                loading: {
                    indicator: 'copy_issuetype_scheme_' + scheme_id + '_indicator'
                },
                success: {
                    hide: 'copy_scheme_' + scheme_id + '_popup',
                    update: {element: 'issuetype_schemes_list', insertion: true}
                }
            });
        }

        Pachno.Config.IssuetypeScheme.remove = function (url, scheme_id) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'delete_issuetype_scheme_' + scheme_id + '_form',
                loading: {
                    indicator: 'delete_issuetype_scheme_' + scheme_id + '_indicator'
                },
                success: {
                    remove: ['delete_scheme_' + scheme_id + '_popup', 'copy_scheme_' + scheme_id + '_popup', 'issuetype_scheme_' + scheme_id],
                    update: {element: 'issuetype_schemes_list', insertion: true},
                    callback: function () {
                        Pachno.Main.Helpers.Dialog.dismiss();
                    }
                }
            });
        }

        Pachno.Config.Issuefields.saveOrder = function (container, type, url) {
            Pachno.Main.Helpers.ajax(url, {
                additional_params: Sortable.serialize(container),
                loading: {
                    indicator: type + '_sort_indicator'
                }
            });
        };

        Pachno.Config.Issuefields.showOptions = function ($item) {
            Pachno.Config.loadComponentOptions(
                {
                    container: '#issue-fields-configuration-container',
                    options: '#selected-issue-field-options',
                    component: '.issue-field'
                },
                $item
            );
        };

        Pachno.Config.Issuefields.Options.save = function (form) {
            var $form = jQuery(form),
                data = new FormData($form[0]);

            if ($form.hasClass('submitting')) return;

            $form.find('.error-container').removeClass('invalid');
            $form.find('.error-container > .error').html('');
            $form.addClass('submitting');

            fetch($form.attr('action'), {
                method: 'POST',
                body: data
            })
                .then(function (response) {
                    response.json().then(function (json) {
                        if (response.ok) {
                            const $issue_option_container = jQuery('[data-issue-field-option][data-id='+json.item.id+']');
                            if ($issue_option_container.length > 0) {
                                $issue_option_container.replaceWith(json.component);
                            } else {
                                const $issue_options_container = jQuery('#field-options-list');
                                if ($issue_options_container.length > 0) {
                                    $issue_options_container.append(json.component);
                                }
                                if (sortable_options != undefined) {
                                    Sortable.destroy('field-options-list');
                                    Sortable.create('field-options-list', sortable_options);
                                }
                                Pachno.Main.Helpers.initializeColorPicker();
                            }
                            $form[0].reset();
                        } else {
                            $form.find('.error-container > .error').html(json.error);
                            $form.find('.error-container').addClass('invalid');
                        }

                        $form.removeClass('submitting');
                        $form.find('.button.primary').attr('disabled', false);
                    })
                        .catch(function (error) {
                            $form.find('.error-container > .error').html(error);
                            $form.find('.error-container').addClass('invalid');

                            $form.removeClass('submitting');
                        });
                });
        }

        Pachno.Config.Issuefields.Options.update = function (url, type, id) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'edit_' + type + '_' + id + '_form',
                loading: {indicator: 'edit_' + type + '_' + id + '_indicator'},
                success: {
                    show: 'item_option_' + type + '_' + id + '_content',
                    hide: 'edit_item_option_' + id,
                    callback: function (json) {
                        $(type + '_' + id + '_name').update($(type + '_' + id + '_name_input').getValue());
                        if ($(type + '_' + id + '_itemdata_input') && $(type + '_' + id + '_itemdata'))
                            $(type + '_' + id + '_itemdata').style.backgroundColor = $(type + '_' + id + '_itemdata_input').getValue();
                        if ($(type + '_' + id + '_value_input') && $(type + '_' + id + '_value'))
                            $(type + '_' + id + '_value').update($(type + '_' + id + '_value_input').getValue());
                    }
                }
            });
        }

        Pachno.Config.Issuefields.Options.remove = function (url, id) {
            fetch(url, { method: 'POST' })
                .then(function (response) {
                    response.json().then(function (json) {
                        Pachno.Main.Helpers.Dialog.dismiss();
                        if (response.ok) {
                            jQuery('[data-issue-field-option][data-id=' + id + ']').remove();
                        } else {
                            Pachno.Main.Helpers.Message.error(json.error);
                        }
                    })
                    .catch(function (error) {
                        Pachno.Main.Helpers.Dialog.dismiss();
                        Pachno.Main.Helpers.Message.error(error);
                    });
                });
        }

        Pachno.Config.Issuefields.Custom.save = function (form) {
            var $form = jQuery(form),
                data = new FormData($form[0]);

            if ($form.hasClass('submitting')) return;

            $form.find('.error-container').removeClass('invalid');
            $form.find('.error-container > .error').html('');
            $form.addClass('submitting');
            $form.find('.button.primary').attr('disabled', true);

            fetch($form.attr('action'), {
                method: 'POST',
                body: data
            })
                .then(function (response) {
                    response.json().then(function (json) {
                        if (response.ok) {
                            const $issue_option_container = jQuery('[data-issue-field][data-id='+json.item.id+']');
                            if ($issue_option_container.length > 0) {
                                $issue_option_container.replaceWith(json.component);
                            } else {
                                const $issue_options_container = jQuery('#custom-types-list');
                                if ($issue_options_container.length > 0) {
                                    $issue_options_container.append(json.component);
                                }
                            }
                            $form[0].reset();
                            Pachno.Main.Helpers.Backdrop.reset();
                        } else {
                            $form.find('.error-container > .error').html(json.error);
                            $form.find('.error-container').addClass('invalid');
                        }

                        $form.removeClass('submitting');
                        $form.find('.button.primary').attr('disabled', false);
                    })
                        .catch(function (error) {
                            $form.find('.error-container > .error').html(error);
                            $form.find('.error-container').addClass('invalid');

                            $form.removeClass('submitting');
                            $form.find('.button.primary').attr('disabled', false);
                        });
                });
        }

        Pachno.Config.Issuefields.Custom.update = function (url, type) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'edit_custom_type_' + type + '_form',
                loading: {indicator: 'edit_custom_type_' + type + '_indicator'},
                success: {
                    hide: 'edit_custom_type_' + type + '_form',
                    callback: function (json) {
                        $('custom_type_' + type + '_description_span').update(json.description);
                        $('custom_type_' + type + '_instructions_span').update(json.instructions);
                        if (json.instructions != '') {
                            $('custom_type_' + type + '_instructions_div').show();
                            $('custom_type_' + type + '_no_instructions_div').hide();
                        } else {
                            $('custom_type_' + type + '_instructions_div').hide();
                            $('custom_type_' + type + '_no_instructions_div').show();
                        }
                        $('custom_type_' + type + '_name').update(json.name);
                    },
                    show: 'custom_type_' + type + '_info'
                }
            });
        }

        Pachno.Config.Issuefields.Custom.remove = function (url, id) {
            fetch(url, { method: 'POST' })
                .then(function (response) {
                    response.json().then(function (json) {
                        Pachno.Main.Helpers.Dialog.dismiss();
                        if (response.ok) {
                            jQuery('[data-issue-field][data-id=' + id + ']').remove();
                            const $container = jQuery('#issue-fields-configuration-container'),
                                $options = jQuery('#selected-issue-field-options');

                            $container.removeClass('active');
                            $container.find('.issue-type-scheme-issue-type').removeClass('active');
                            $options.html('');
                        } else {
                            Pachno.Main.Helpers.Message.error(json.error);
                        }
                    })
                        .catch(function (error) {
                            Pachno.Main.Helpers.Dialog.dismiss();
                            Pachno.Main.Helpers.Message.error(error);
                        });
                });
        };

        Pachno.Config.Permissions.set = function (url, field) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: field + '_indicator',
                    callback: function (json) {
                        $$('#' + field + ' .image img').each(function (element) {
                            $(element).hide();
                        });
                    }
                },
                success: {update: field + '_wrapper'}
            });
        };

        Pachno.Config.Permissions.getOptions = function (url, field) {
            $(field).toggle();
            if ($(field).childElements().size() == 0) {
                Pachno.Main.Helpers.ajax(url, {
                    loading: {indicator: field + '_indicator'},
                    success: {update: field}
                });
            }
        }

        Pachno.Config.Roles.update = function (url, role_id) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'role_' + role_id + '_form',
                loading: {indicator: 'role_' + role_id + '_form_indicator'},
                success: {
                    hide: 'role_' + role_id + '_permissions_edit',
                    callback: function (json) {
                        $('role_' + role_id + '_permissions_count').update(json.permissions_count);
                        $('role_' + role_id + '_permissions_list').update('');
                        $('role_' + role_id + '_permissions_list').hide();
                        $('role_' + role_id + '_name').update(json.role_name);
                    }
                }
            });
        }

        Pachno.Config.Roles.remove = function (url, role_id) {
            Pachno.Main.Helpers.ajax(url, {
                url_method: 'post',
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop']
                },
                success: {
                    callback: function () {
                        var rc = $('role_' + role_id + '_container');
                        if (rc.up('ul').childElements().size() == 2) {
                            rc.up('ul').down('li.no_roles').show();
                        }
                        rc.remove();
                    }
                }
            });
        }

        Pachno.Config.Roles.add = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'new_role_form',
                loading: {indicator: 'new_role_form_indicator'},
                success: {
                    update: {element: 'global_roles_list', insertion: true},
                    hide: ['global_roles_no_roles'],
                    callback: function  () {
                        $('add_new_role_input').setValue('');
                    }
                }
            });
        };

        Pachno.Project.Roles.add = function (url, pid) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'new_project' + pid + '_role_form',
                loading: {indicator: 'new_project' + pid + '_role_form_indicator'},
                success: {
                    update: {element: 'project' + pid + '_roles_list', insertion: true},
                    hide: ['project' + pid + '_roles_no_roles', 'new_project' + pid + '_role']
                }
            });
        };

        Pachno.Config.User.show = function (url, findstring) {
            Pachno.Main.Helpers.ajax(url, {
                params: '&findstring=' + findstring,
                loading: {indicator: 'find_users_indicator'},
                success: {update: 'users_results'}
            });
        };

        Pachno.Config.User.add = function (url, callback_function_for_import, form) {
            f = (form !== undefined) ? form : 'createuser_form';
            Pachno.Main.Helpers.ajax(url, {
                form: f,
                loading: {
                    indicator: 'createuser_form_indicator'
                },
                success: {
                    hide: ['createuser_form_indicator', 'createuser_form_quick_indicator'],
                    update: 'users_results',
                    callback: function (json) {
                        $('adduser_div').hide();
                        Pachno.Config.User._updateLinks(json);
                        $(f).reset();
                    }
                },
                failure: {
                    hide: ['createuser_form_indicator', 'createuser_form_quick_indicator'],
                    callback: function (json) {
                        if (json.allow_import || false) {
                            callback_function_for_import();
                        }
                    }
                }
            });
        };

        Pachno.Config.User.addToScope = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'createuser_form',
                loading: {indicator: 'dialog_indicator'},
                success: {
                    update: 'users_results',
                    callback: function (json) {
                        Pachno.Main.Helpers.Dialog.dismiss();
                        Pachno.Config.User._updateLinks(json);
                    }
                }
            });
        };

        Pachno.Config.User.getEditForm = function (url, uid) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'user_' + uid + '_edit_spinning',
                    hide: 'users_results_user_' + uid
                },
                success: {
                    // update: 'user_' + uid + '_edit_td',
                    update: 'user_' + uid + '_edit_td',
                    show: ['user_' + uid + '_edit_tr', 'users_results_user_' + uid]
                },
                failure: {
                    show: 'users_results_user_' + uid
                }
            });
        };

        Pachno.Config.User.remove = function (url, user_id) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop']
                },
                success: {
                    remove: ['users_results_user_' + user_id, 'user_' + user_id + '_edit_spinning', 'user_' + user_id + '_edit_tr', 'users_results_user_' + user_id + '_permissions_row'],
                    callback: Pachno.Config.User._updateLinks
                }
            });
        };

        Pachno.Config.User._updateLinks = function (json) {
            if (json == null) return;
            if ($('current_user_num_count'))
                $('current_user_num_count').update(json.total_count);
            (json.more_available) ? $('adduser_form_container').show() : $('adduser_form_container').hide();
            Pachno.Config.Collection.updateDetailsFromJSON(json);
        };

        Pachno.Config.User.update = function (url, user_id) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'edit_user_' + user_id + '_form',
                loading: {indicator: 'edit_user_' + user_id + '_indicator'},
                success: {
                    update: 'users_results_user_' + user_id,
                    show: 'users_results_user_' + user_id,
                    hide: 'user_' + user_id + '_edit_tr',
                    callback: function (json) {
                        $('password_' + user_id + '_leave').checked = true;
                        $('new_password_' + user_id + '_1').value = '';
                        $('new_password_' + user_id + '_2').value = '';
                        Pachno.Config.Collection.updateDetailsFromJSON(json);
                    }
                }
            });
        };

        Pachno.Config.User.updateScopes = function (url, user_id) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'edit_user_' + user_id + '_scopes_form',
                loading: {indicator: 'edit_user_' + user_id + '_scopes_form_indicator'},
                success: {
                    callback: Pachno.Main.Helpers.Backdrop.reset
                }
            });
        };

        Pachno.Config.User.getPermissionsBlock = function (url, user_id) {
            $('users_results_user_' + user_id + '_permissions_row').toggle();
            if ($('users_results_user_' + user_id + '_permissions').innerHTML == '') {
                Pachno.Main.Helpers.ajax(url, {
                    loading: {
                        indicator: 'permissions_' + user_id + '_indicator'
                    },
                    success: {
                        update: 'users_results_user_' + user_id + '_permissions',
                        show: 'users_results_user_' + user_id + '_permissions'
                    }
                });
            }
        };

        Pachno.Config.Collection.add = function (url, type, callback_function) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'create_' + type + '_form',
                loading: {indicator: 'create_' + type + '_indicator'},
                success: {
                    update: {element: type + 'config_list', insertion: true},
                    callback: callback_function
                }
            });
        };

        Pachno.Config.Collection.remove = function (url, type, cid, callback_function) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop']
                },
                success: {
                    remove: type + 'box_' + cid,
                    callback: function (json) {
                        if (callback_function)
                            callback_function(json);
                    }
                }
            });
        };

        Pachno.Config.Collection.clone = function (url, type, cid, callback_function) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'clone_' + type + '_' + cid + '_form',
                loading: {indicator: 'clone_' + type + '_' + cid + '_indicator'},
                success: {
                    update: {element: type + 'config_list', insertion: true},
                    hide: 'clone_' + type + '_' + cid,
                    callback: callback_function
                }
            });
        };

        Pachno.Config.Collection.showMembers = function (url, type, cid) {
            $(type + '_members_' + cid + '_container').toggle();
            if ($(type + '_members_' + cid + '_list').innerHTML == '') {
                Pachno.Main.Helpers.ajax(url, {
                    loading: {indicator: type + '_members_' + cid + '_indicator'},
                    success: {update: type + '_members_' + cid + '_list'},
                    failure: {hide: type + '_members_' + cid + '_container'}
                });
            }
        };

        Pachno.Config.Collection.removeMember = function (url, type, cid, user_id) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: type + '_members_' + cid + '_indicator',
                    hide: 'dialog_backdrop'
                },
                success: {
                    callback: function (json) {
                        $(type + '_' + cid + '_' + user_id + '_item').remove();
                        Pachno.Config.Collection.updateDetailsFromJSON(json, false);
                        var ul = $(type + '_members_' + cid + '_list').down('ul');
                        if (ul != undefined && ul.childElements().size() == 0)
                            $(type + '_members_' + cid + '_no_users').show();
                    }
                }
            });
        };

        Pachno.Config.Collection.addMember = function (url, type, cid, user_id) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {indicator: type + '_members_' + cid + '_indicator'},
                success: {
                    callback: function (json) {
                        Pachno.Config.Collection.updateDetailsFromJSON(json, false);
                        var ul = $(type + '_members_' + cid + '_list').down('ul');
                        if (ul != undefined && ul.childElements().size() == 0) {
                            $(type + '_members_' + cid + '_no_users').hide();
                        }
                        $(type + '_members_' + cid + '_list').down('ul').insert({bottom: json[type + 'listitem']});
                    }
                }
            });
        };

        Pachno.Config.Collection.updateDetailsFromJSON = function (json, clear) {
            if (json.update_groups) {
                json.update_groups.ids.each(function (group_id) {
                    if ($('group_' + group_id + '_membercount'))
                        $('group_' + group_id + '_membercount').update(json.update_groups.membercounts[group_id]);
                    if (clear == undefined || clear == true) {
                        $('group_members_' + group_id + '_container').hide();
                        $('group_members_' + group_id + '_list').update('');
                    }
                });
            }
            if (json.update_teams) {
                json.update_teams.ids.each(function (team_id) {
                    if ($('team_' + team_id + '_membercount'))
                        $('team_' + team_id + '_membercount').update(json.update_teams.membercounts[team_id]);
                    if (clear == undefined || clear == true) {
                        $('team_members_' + team_id + '_container').hide();
                        $('team_members_' + team_id + '_list').update('');
                    }
                });
            }
            if (json.update_clients) {
                json.update_clients.ids.each(function (client_id) {
                    if ($('client_' + client_id + '_membercount'))
                        $('client_' + client_id + '_membercount').update(json.update_clients.membercounts[client_id]);
                    if (clear == undefined || clear == true) {
                        $('client_members_' + client_id + '_container').hide();
                        $('client_members_' + client_id + '_list').update('');
                    }
                });
            }
        };

        Pachno.Config.Group.add = function (url) {
            Pachno.Config.Collection.add(url, 'group');
        };

        Pachno.Config.Group.remove = function (url, group_id) {
            Pachno.Config.Collection.remove(url, 'group', group_id);
        };

        Pachno.Config.Group.clone = function (url, group_id) {
            Pachno.Config.Collection.clone(url, 'group', group_id);
        };

        Pachno.Config.Group.showMembers = function (url, group_id) {
            Pachno.Config.Collection.showMembers(url, 'group', group_id);
        }

        Pachno.Config.Team.updateLinks = function (json) {
            if ($('current_team_num_count'))
                $('current_team_num_count').update(json.total_count);
            $$('.copy_team_link').each(function (element) {
                (json.more_available) ? $(element).show() : $(element).hide();
            });
            (json.more_available) ? $('add_team_div').show() : $('add_team_div').hide();
        }

        Pachno.Config.Team.getPermissionsBlock = function (url, team_id) {
            if ($('team_' + team_id + '_permissions').innerHTML == '') {
                Pachno.Main.Helpers.ajax(url, {
                    loading: {
                        show: 'team_' + team_id + '_permissions_container',
                        indicator: 'team_' + team_id + '_permissions_indicator'
                    },
                    success: {
                        update: 'team_' + team_id + '_permissions',
                    }
                });
            }
            else {
                $('team_' + team_id + '_permissions_container').show();
            }
        };

        Pachno.Config.Team.add = function (url) {
            Pachno.Config.Collection.add(url, 'team', Pachno.Config.Team.updateLinks);
        }

        Pachno.Config.Team.remove = function (url, team_id) {
            Pachno.Config.Collection.remove(url, 'team', team_id, Pachno.Config.Team.updateLinks);
        };

        Pachno.Config.Team.clone = function (url, team_id) {
            Pachno.Config.Collection.clone(url, 'team', team_id, Pachno.Config.Team.updateLinks);
        }

        Pachno.Config.Team.showMembers = function (url, team_id) {
            Pachno.Config.Collection.showMembers(url, 'team', team_id);
        }

        Pachno.Config.Team.removeMember = function (url, team_id, member_id) {
            Pachno.Config.Collection.removeMember(url, 'team', team_id, member_id);
        }

        Pachno.Config.Team.addMember = function (url, team_id, member_id) {
            Pachno.Config.Collection.addMember(url, 'team', team_id, member_id);
        }

        Pachno.Config.Client.add = function (url) {
            Pachno.Config.Collection.add(url, 'client');
        }

        Pachno.Config.Client.remove = function (url, client_id) {
            Pachno.Config.Collection.remove(url, 'client', client_id);
        }

        Pachno.Config.Client.showMembers = function (url, client_id) {
            Pachno.Config.Collection.showMembers(url, 'client', client_id);
        }

        Pachno.Config.Client.removeMember = function (url, client_id, member_id) {
            Pachno.Config.Collection.removeMember(url, 'client', client_id, member_id);
        }

        Pachno.Config.Client.addMember = function (url, client_id, member_id) {
            Pachno.Config.Collection.addMember(url, 'client', client_id, member_id);
        }

        Pachno.Config.Client.update = function (url, client_id) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'edit_client_' + client_id + '_form',
                loading: {indicator: 'edit_client_' + client_id + '_indicator'},
                success: {
                    hide: 'edit_client_' + client_id,
                    update: 'client_' + client_id + '_item'
                }
            });
        };

        Pachno.Config.fetchComponentUpdateHandler = function (type) {
            return function ([$form, response]) {
                response.json().then(function (json) {
                    if (response.ok) {
                        const $scheme_container = jQuery('[data-' + type + '][data-id='+json.item.id+']');
                        if ($scheme_container.length > 0) {
                            $scheme_container.replaceWith(json.component);
                        } else {
                            const $schemes_container = jQuery('#workflow-schemes-list');
                            if ($schemes_container.length > 0) {
                                $schemes_container.append(json.component);
                            }
                        }
                        $form[0].reset();
                        Pachno.Main.Helpers.Backdrop.reset();
                    } else {
                        $form.find('.error-container > .error').html(json.error);
                        $form.find('.error-container').addClass('invalid');
                    }

                    $form.removeClass('submitting');
                    $form.find('.button.primary').attr('disabled', false);
                })
                    .catch(function (error) {
                        $form.find('.error-container > .error').html(error);
                        $form.find('.error-container').addClass('invalid');

                        $form.removeClass('submitting');
                        $form.find('.button.primary').attr('disabled', false);
                    });
            };
        };

        Pachno.Config.loadComponentOptions = function (options, $item) {
            return new Promise(function (resolve, reject) {
                const $container = jQuery(options.container),
                    $options = jQuery(options.options),
                    url = $item.data('options-url');

                $options.html('<div><i class="fas fa-spin fa-spinner"></i></div>');
                $container.addClass('active');
                $container.find(options.component).removeClass('active');
                $item.addClass('active');

                fetch(url, {
                    method: 'GET'
                })
                    .then(function (response) {
                        response.json().then(function (json) {
                            if (response.ok) {
                                $options.html(json.content);
                                Pachno.Main.updateWidgets()
                                    .then(resolve);
                            }
                        });
                    });
            });
        };

        Pachno.Config.Workflows.Scheme.save = function (form) {
            Pachno.Core.fetchPostHelper(form)
                .then(Pachno.Config.fetchComponentUpdateHandler('workflow-scheme'));
        };

        Pachno.Config.Workflows.Scheme.remove = function (url, id) {
            fetch(url, { method: 'POST' })
                .then(function (response) {
                    response.json().then(function (json) {
                        Pachno.Main.Helpers.Dialog.dismiss();
                        if (response.ok) {
                            jQuery('[data-workflow-scheme][data-id=' + id + ']').remove();
                        } else {
                            Pachno.Main.Helpers.Message.error(json.error);
                        }
                    })
                        .catch(function (error) {
                            Pachno.Main.Helpers.Dialog.dismiss();
                            Pachno.Main.Helpers.Message.error(error);
                        });
                });
        };

        Pachno.Config.Workflows.Workflow.save = function (form) {
            Pachno.Core.fetchPostHelper(form)
                .then(Pachno.Core.fetchPostDefaultFormHandler);
        };

        Pachno.Config.Workflows.Workflow.copy = function (url, workflow_id) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'copy_workflow_' + workflow_id + '_form',
                loading: {indicator: 'copy_workflow_' + workflow_id + '_indicator'},
                success: {
                    hide: 'copy_workflow_' + workflow_id + '_popup',
                    update: {element: 'workflows_list', insertion: true},
                    callback: Pachno.Config.Workflows._updateLinks
                }
            });
        };

        Pachno.Config.Workflows.Workflow.remove = function (url, workflow_id) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'delete_workflow_' + workflow_id + '_form',
                loading: {indicator: 'delete_workflow_' + workflow_id + '_indicator'},
                success: {
                    remove: ['delete_workflow_' + workflow_id + '_popup', 'copy_workflow_' + workflow_id + '_popup', 'workflow_' + workflow_id],
                    update: {element: 'workflows_list', insertion: true},
                    callback: Pachno.Config.Workflows._updateLinks
                }
            });
        };

        Pachno.Config.Workflows.Workflow.Step.show = function ($item) {
            Pachno.Config.loadComponentOptions(
                {
                    container: '#workflow-steps-container',
                    options: '#selected-workflow-step-options',
                    component: '.workflow-step'
                },
                $item
            );
        };

        Pachno.Config.Workflows.Workflow.Step.save = function (form) {
            Pachno.Core.fetchPostHelper(form)
                .then(Pachno.Core.fetchPostDefaultFormHandler);
        };

        Pachno.Config.Workflows.Transition.save = function (form) {
            Pachno.Core.fetchPostHelper(form)
                .then(Pachno.Core.fetchPostDefaultFormHandler);
        };

        Pachno.Config.Workflows.Transition.remove = function (url, transition_id, direction) {
            $('transition_' + transition_id + '_delete_form').submit();
        };

        Pachno.Config.Workflows.Transition.Validations.add = function (url, mode, key) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {indicator: 'workflowtransition' + mode + 'validationrule_add_indicator'},
                success: {
                    hide: ['no_workflowtransition' + mode + 'validationrules', 'add_workflowtransition' + mode + 'validationrule_' + key],
                    update: {element: 'workflowtransition' + mode + 'validationrules_list', insertion: true}
                }
            });
        }

        Pachno.Config.Workflows.Transition.Validations.update = function (url, rule_id) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'workflowtransitionvalidationrule_' + rule_id + '_form',
                loading: {indicator: 'workflowtransitionvalidationrule_' + rule_id + '_indicator'},
                success: {
                    hide: ['workflowtransitionvalidationrule_' + rule_id + '_cancel_button', 'workflowtransitionvalidationrule_' + rule_id + '_edit'],
                    update: 'workflowtransitionvalidationrule_' + rule_id + '_value',
                    show: ['workflowtransitionvalidationrule_' + rule_id + '_edit_button', 'workflowtransitionvalidationrule_' + rule_id + '_delete_button', 'workflowtransitionvalidationrule_' + rule_id + '_description']
                }
            });
        }

        Pachno.Config.Workflows.Transition.Validations.remove = function (url, rule_id, type, mode) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop']
                },
                success: {
                    remove: ['workflowtransitionvalidationrule_' + rule_id],
                    show: ['add_workflowtransition' + type + 'validationrule_' + mode],
                    callback: function () {
                        Pachno.Main.Helpers.Dialog.dismiss();
                    }
                }
            });
        }

        Pachno.Config.Workflows.Transition.Actions.add = function (url, key) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {indicator: 'workflowtransitionaction_add_indicator'},
                success: {
                    hide: ['no_workflowtransitionactions', 'add_workflowtransitionaction_' + key],
                    update: {element: 'workflowtransitionactions_list', insertion: true}
                }
            });
        }

        Pachno.Config.Workflows.Transition.Actions.update = function (url, action_id) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'workflowtransitionaction_' + action_id + '_form',
                loading: {indicator: 'workflowtransitionaction_' + action_id + '_indicator'},
                success: {
                    hide: ['workflowtransitionaction_' + action_id + '_cancel_button', 'workflowtransitionaction_' + action_id + '_edit'],
                    update: 'workflowtransitionaction_' + action_id + '_value',
                    show: ['workflowtransitionaction_' + action_id + '_edit_button', 'workflowtransitionaction_' + action_id + '_delete_button', 'workflowtransitionaction_' + action_id + '_description']
                }
            });
        }

        Pachno.Config.Workflows.Transition.Actions.remove = function (url, action_id, type) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {indicator: 'workflowtransitionaction_' + action_id + '_delete_indicator'},
                success: {
                    hide: ['workflowtransitionaction_' + action_id + '_delete', 'workflowtransitionaction_' + action_id],
                    show: ['add_workflowtransitionaction_' + type],
                    callback: function () {
                        Pachno.Main.Helpers.Dialog.dismiss();
                    }
                }
            });
        }

        /**
         * This function updates available issue reporting fields on page to match
         * those returned by pachno
         */
        Pachno.Issues.updateFields = function (url)
        {
            if ($('issuetype_id').getValue() != 0) {
                $('issuetype_list').hide();
            }
            if ($('project_id').getValue() != 0 && $('issuetype_id').getValue() != 0) {
                $('report_more_here').hide();
                $('report_form').show('block');

                Pachno.Main.Helpers.ajax(url, {
                    loading: {indicator: 'report_issue_more_options_indicator'},
                    params: 'issuetype_id=' + $('issuetype_id').getValue(),
                    success: {
                        callback: function (json) {
                            Pachno.Main.Helpers.MarkitUp($$('textarea.markuppable'));
                            json.available_fields.each(function (fieldname, key)
                            {
                                if ($(fieldname + '_div')) {
                                    if (json.fields[fieldname]) {
                                        var prev_val = '';
                                        if (json.fields[fieldname].values) {
                                            if ($(fieldname + '_additional') && $(fieldname + '_additional').visible()) {
                                                prev_val = $(fieldname + '_id_additional').getValue();
                                            } else if ($(fieldname + '_div') && $(fieldname + '_div').visible()) {
                                                prev_val = $(fieldname + '_id').getValue();
                                            }
                                        }
                                        if (json.fields[fieldname].additional && $(fieldname + '_additional')) {
                                            $(fieldname + '_additional').show('block');
                                            $(fieldname + '_div').hide();
                                            if ($(fieldname + '_id_additional')) {
                                                $(fieldname + '_id_additional').enable();
                                            }
                                            if ($(fieldname + '_value_additional')) {
                                                $(fieldname + '_value_additional').enable();
                                            }
                                            if ($(fieldname + '_id')) {
                                                $(fieldname + '_id').disable();
                                            }
                                            if ($(fieldname + '_value')) {
                                                $(fieldname + '_value').disable();
                                            }
                                            if (json.fields[fieldname].values) {
                                                $(fieldname + '_id_additional').update('');
                                                for (var opt in json.fields[fieldname].values) {
                                                    $(fieldname + '_id_additional').insert('<option value="' + opt.substr(1) + '">' + json.fields[fieldname].values[opt] + '</option>');
                                                }
                                                $(fieldname + '_id_additional').setValue(prev_val);
                                            }
                                        } else {
                                            if ($(fieldname + '_div')) {
                                                $(fieldname + '_div').show('block');
                                            }
                                            if ($(fieldname + '_id')) {
                                                $(fieldname + '_id').enable();
                                            }
                                            if ($(fieldname + '_value')) {
                                                $(fieldname + '_value').enable();
                                            }
                                            if ($(fieldname + '_id_additional')) {
                                                $(fieldname + '_id_additional').disable();
                                            }
                                            if ($(fieldname + '_value_additional')) {
                                                $(fieldname + '_value_additional').disable();
                                            }
                                            if ($(fieldname + '_additional')) {
                                                $(fieldname + '_additional').hide();
                                            }
                                            if (json.fields[fieldname].values) {
                                                if ($(fieldname + '_id')) {
                                                    $(fieldname + '_id').update('');
                                                    for (var opt in json.fields[fieldname].values) {
                                                        $(fieldname + '_id').insert('<option value="' + opt.substr(1) + '">' + json.fields[fieldname].values[opt] + '</option>');
                                                    }
                                                    $(fieldname + '_id').setValue(prev_val);
                                                }
                                            }
                                        }
                                        (json.fields[fieldname].required) ? $(fieldname + '_label').addClassName('required') : $(fieldname + '_label').removeClassName('required');
                                    } else {
                                        if ($(fieldname + '_div')) {
                                            $(fieldname + '_div').hide();
                                        }
                                        if ($(fieldname + '_id')) {
                                            $(fieldname + '_id').disable();
                                        }
                                        if ($(fieldname + '_value')) {
                                            $(fieldname + '_value').disable();
                                        }
                                        if ($(fieldname + '_additional')) {
                                            $(fieldname + '_additional').hide();
                                        }
                                        if ($(fieldname + '_id_additional')) {
                                            $(fieldname + '_id_additional').disable();
                                        }
                                        if ($(fieldname + '_value_additional')) {
                                            $(fieldname + '_value_additional').disable();
                                        }
                                    }
                                }
                            });
                            var visible_fields = false;
                            $$('.additional_information').each(function (elm) {
                                if (elm.visible()) {
                                    visible_fields = true;
                                    return;
                                }
                            })
                            if (visible_fields) {
                                $$('.additional_information')[0].up('.reportissue_additional_information_container').show('block');
                            } else {
                                $$('.additional_information')[0].up('.reportissue_additional_information_container').hide();
                            }
                            var visible_extrafields = false;
                            $('reportissue_extrafields').childElements().each(function (elm) {
                                if (elm.visible()) {
                                    visible_extrafields = true;
                                    return;
                                }
                            })
                            if (visible_extrafields) {
                                $('reportissue_extrafields_none').hide();
                            } else {
                                $('reportissue_extrafields_none').show('block');
                            }
                            $('title').focus();
                            $('report_issue_more_options_indicator').hide();
                        }
                    }
                });
            } else {
                $('report_form').hide();
                $('report_more_here').show('block');
                $('issuetype_list').show('block');
                $('reportissue_container').addClassName('large');
                $('reportissue_container').removeClassName('huge');
            }

        }

        /**
         * Displays the workflow transition popup dialog
         */
        Pachno.Issues.showWorkflowTransition = function (transition_id) {
            var existing_container = $('workflow_transition_fullpage').down('.workflow_transition');
            if (existing_container) {
                existing_container.hide();
                $('workflow_transition_container').insert(existing_container);
            }
            var workflow_div = $('issue_transition_container_' + transition_id);
            $('workflow_transition_fullpage').insert(workflow_div);
            $('workflow_transition_fullpage').appear({duration: 0.2});
            workflow_div.appear({duration: 0.2, afterFinish: function () {
                if ($('duplicate_finder_transition_' + transition_id)) {
                    $('viewissue_find_issue_' + transition_id + '_input').observe('keypress', function (event) {
                        if (event.keyCode == Event.KEY_RETURN) {
                            Pachno.Issues.findDuplicate($('duplicate_finder_transition_' + transition_id).getValue(), transition_id);
                            event.stop();
                        }
                    });
                }

            }});
        };

        Pachno.Issues.submitWorkflowTransition = function (form, callback) {
            Pachno.Main.Helpers.ajax(form.action, {
                form: form,
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop', 'workflow_transition_fullpage']
                },
                success: {
                    hide: 'workflow_transition_fullpage',
                    callback: callback
                },
                failure: {
                    show: 'workflow_transition_fullpage'
                }
            });
        };

        Pachno.Issues.showLog = function (url) {
            if ($('viewissue_log_items').childElements().size() == 0) {
                Pachno.Main.Helpers.ajax(url, {
                    url_method: 'get',
                    loading: {indicator: 'viewissue_log_loading_indicator'},
                    success: {
                        update: {element: 'viewissue_log_items'}
                    }
                });
            }
        }

        Pachno.Issues.refreshRelatedIssues = function (url) {
            if ($('related_child_issues_inline')) {
                Pachno.Main.Helpers.ajax(url, {
                    loading: {indicator: 'related_issues_indicator'},
                    success: {
                        hide: 'no_child_issues',
                        update: {element: 'related_child_issues_inline'},
                        callback: function () {
                            $('viewissue_related_issues_count').update($('related_child_issues_inline').childElements().size());
                        }
                    }
                });
            }
        };

        Pachno.Issues.findRelated = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'viewissue_find_issue_form',
                loading: {indicator: 'viewissue_find_issue_indicator'},
                success: {update: 'viewissue_relation_results'}
            });
            return false;
        };

        Pachno.Issues.findDuplicate = function (url, transition_id) {
            Pachno.Main.Helpers.ajax(url, {
                additional_params: 'searchfor=' + $('viewissue_find_issue_' + transition_id + '_input').getValue(),
                loading: {indicator: 'viewissue_find_issue_' + transition_id + '_indicator'},
                success: {update: 'viewissue_' + transition_id + '_duplicate_results'}
            });
        };

        Pachno.Issues.editTimeEntry = function (form) {
            var url = form.action;
            Pachno.Main.Helpers.ajax(url, {
                form: form,
                loading: {
                    indicator: 'fullpage_backdrop_indicator',
                    hide: 'fullpage_backdrop_content'
                },
                success: {
                    callback: function (json) {
                        $('fullpage_backdrop_content').update(json.timeentries);
                        $('fullpage_backdrop_content').show();
                        if (json.timesum == 0) {
                            $('no_spent_time_' + json.issue_id).show();
                            $('spent_time_' + json.issue_id + '_name').hide();
                        } else {
                            $('no_spent_time_' + json.issue_id).hide();
                            $('spent_time_' + json.issue_id + '_name').show();
                            $('spent_time_' + json.issue_id + '_value').update(json.spenttime);
                        }
                        Pachno.Issues.Field.updateEstimatedPercentbar(json);
                    }
                }
            });
        };

        Pachno.Issues.deleteTimeEntry = function (url, entry_id) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {indicator: 'dialog_indicator'},
                success: {
                    callback: function (json) {
                        Pachno.Main.Helpers.Dialog.dismiss();
                        $('issue_spenttime_' + entry_id).remove();
                        if ($('issue_spenttime_' + entry_id + '_comment'))
                            $('issue_spenttime_' + entry_id + '_comment').remove();
                        if (json.timesum == 0) {
                            $('no_spent_time_' + json.issue_id).show();
                            $('spent_time_' + json.issue_id + '_name').hide();
                        } else {
                            $('no_spent_time_' + json.issue_id).hide();
                            $('spent_time_' + json.issue_id + '_name').show();
                            $('spent_time_' + json.issue_id + '_value').update(json.spenttime);
                        }
                        Pachno.Issues.Field.updateEstimatedPercentbar(json);
                    }
                }
            });
        };

        Pachno.Issues.Field.updateEstimatedPercentbar = function (data) {
            $('estimated_percentbar').update(data.percentbar);
            if ($('no_estimated_time_' + data.issue_id).visible()) {
                $('estimated_percentbar').hide();
            }
            else {
                $('estimated_percentbar').show();
            }
        };

        Pachno.Issues.Add = function (url, btn) {
            var btn = btn != undefined ? $(btn) : $('reportissue_button');
            var additional_params_query = '';

            if (btn.dataset != undefined && btn.dataset.milestoneId != undefined && parseInt(btn.dataset.milestoneId) > 0) {
                additional_params_query += '/milestone_id/' + btn.dataset.milestoneId;
            }

            if (url.indexOf('issuetype') !== -1) {
                Pachno.Main.Helpers.Backdrop.show(url +  additional_params_query, function () {
                    jQuery('#reportissue_container').addClass('huge');
                    jQuery('#reportissue_container').removeClass('large');
                });
            }
            else {
                Pachno.Main.Helpers.Backdrop.show(url +  additional_params_query);
            }
        };

        Pachno.Issues.relate = function (url) {

            Pachno.Main.Helpers.ajax(url, {
                form: 'viewissue_relate_issues_form',
                loading: {indicator: 'relate_issues_indicator'},
                success: {
                    update: {element: 'related_child_issues_inline', insertion: true},
                    hide: 'no_child_issues',
                    callback: function (json) {
                        if (jQuery('.milestone_details_link.selected').eq(0).find('> a:first-child').length) {
                            jQuery('.milestone_details_link.selected').eq(0).find('> a:first-child').trigger('click');
                        }
                        else {
                            Pachno.Main.Helpers.Backdrop.reset();
                        }
                        if ($('viewissue_related_issues_count')) $('viewissue_related_issues_count').update(json.count);
                        if (json.count > 0 && $('no_related_issues').visible()) $('no_related_issues').hide();
                    }
                }
            });
            return false;
        };

        Pachno.Issues.removeRelated = function (url, issue_id) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {indicator: 'related_issues_indicator'},
                success: {
                    remove: 'related_issue_' + issue_id,
                    callback: function () {
                        var childcount = $('related_child_issues_inline').childElements().size();
                        $('viewissue_related_issues_count').update(childcount);
                        if (childcount == 0) {
                            $('no_related_issues').show();
                        }
                    }
                }
            });
        };

        Pachno.Issues.removeDuplicated = function (url, issue_id) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {indicator: 'duplicate_issues_indicator'},
                success: {
                    remove: 'duplicated_issue_' + issue_id,
                    callback: function () {
                        var childcount = $('related_duplicate_issues_inline').childElements().size();
                        $('viewissue_duplicate_issues_count').update(childcount);
                        if (childcount == 0) {
                            $('no_duplicated_issues').show();
                        }
                    }
                }
            });
        };

        Pachno.Issues.move = function (form, issue_id) {
            Pachno.Main.Helpers.ajax(form.action, {
                form: form,
                loading: {
                    indicator: 'move_issue_indicator'
                },
                success: {
                    remove: 'issue_' + issue_id,
                    update: 'viewissue_move_issue_div'
                }
            });
        };

        Pachno.Issues._addVote = function (url, direction) {
            var opp_direction = (direction == 'up') ? 'down' : 'up';

            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'vote_' + direction + '_indicator',
                    hide: 'vote_' + direction + '_link'},
                success: {
                    update: 'issue_votes',
                    hide: ['vote_' + direction + '_link', 'vote_' + opp_direction + '_faded'],
                    show: ['vote_' + direction + '_faded', 'vote_' + opp_direction + '_link']
                }
            });
        };

        Pachno.Issues.voteUp = function (url) {
            Pachno.Issues._addVote(url, 'up');
        };

        Pachno.Issues.voteDown = function (url) {
            Pachno.Issues._addVote(url, 'down');
        };

        Pachno.Issues.toggleFavourite = function (url, issue_id_user_id)
        {
            var issue_id = new String(issue_id_user_id).indexOf('_') !== -1
                ? issue_id_user_id.substr(0, issue_id_user_id.indexOf('_'))
                : issue_id_user_id;
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    callback: function () {
                        if ($('popup_find_subscriber_' + issue_id) != null && $('popup_find_subscriber_' + issue_id).visible() && $('popup_find_subscriber_' + issue_id + '_spinning')) {
                            $('popup_find_subscriber_' + issue_id + '_spinning').show();
                        }
                        else {
                            Pachno.Core._processCommonAjaxPostEvents({
                                show: 'issue_favourite_indicator_' + issue_id_user_id,
                                hide: ['issue_favourite_normal_' + issue_id_user_id, 'issue_favourite_faded_' + issue_id_user_id]
                            });
                        }
                    }
                },
                success: {
                    hide: 'popup_find_subscriber_' + issue_id,
                    callback: function (json) {
                        if ($('popup_find_subscriber_' + issue_id + '_spinning')) {
                            $('popup_find_subscriber_' + issue_id + '_spinning').hide();
                        }
                        else {
                            Pachno.Core._processCommonAjaxPostEvents({
                                hide: 'issue_favourite_indicator_' + issue_id_user_id,
                            });
                        }
                        if ($('issue_favourite_faded_' + issue_id_user_id)) {
                            if (json.starred) {
                                $('issue_favourite_faded_' + issue_id_user_id).hide();
                                $('issue_favourite_indicator_' + issue_id_user_id).hide();
                                $('issue_favourite_normal_' + issue_id_user_id).show();
                            } else {
                                $('issue_favourite_normal_' + issue_id_user_id).hide();
                                $('issue_favourite_indicator_' + issue_id_user_id).hide();
                                $('issue_favourite_faded_' + issue_id_user_id).show();
                            }
                        } else if (json.subscriber != '') {
                            $('subscribers_list').insert(json.subscriber);
                        }
                        if (json.count != undefined && $('subscribers_field_count')) {
                            $('subscribers_field_count').update(json.count);
                        }
                    }
                }
            });
        }

        Pachno.Issues.toggleBlocking = function (url, issue_id)
        {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: 'fullpage_backdrop_content'
                },
                success: {
                    callback: function (json) {
                        $('more_actions_mark_notblocking_link_' + issue_id).toggle();
                        $('more_actions_mark_blocking_link_' + issue_id).toggle();

                        if ($('blocking_div')) {
                            $('blocking_div').toggle();
                        }
                        if ($('issue_' + issue_id)) {
                            $('issue_' + issue_id).toggleClassName('blocking');
                        }
                    }
                }
            });
        }

        Pachno.Issues.Link.add = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'attach_link_form',
                loading: {
                    indicator: 'attach_link_indicator',
                    callback: function () {
                        $('attach_link_submit').disable();
                    }
                },
                success: {
                    reset: 'attach_link_form',
                    hide: ['attach_link', 'viewissue_no_uploaded_files'],
                    update: {element: 'viewissue_uploaded_links', insertion: true},
                    callback: function (json) {
                        if ($('viewissue_uploaded_attachments_count'))
                            $('viewissue_uploaded_attachments_count').update(json.attachmentcount);
                        Pachno.Main.Helpers.Backdrop.reset();
                    }
                },
                complete: {
                    callback: function () {
                        $('attach_link_submit').enable();
                    }
                }
            });
        }

        Pachno.Issues.Link.remove = function (url, link_id) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'viewissue_links_' + link_id + '_remove_indicator',
                    hide: link_id + '_remove_link',
                    callback: Pachno.Main.Helpers.Dialog.dismiss
                },
                success: {
                    remove: ['viewissue_links_' + link_id, 'viewissue_links_' + link_id + '_remove_confirm'],
                    callback: function (json) {
                        if (json.attachmentcount == 0 && $('viewissue_no_uploaded_files')) $('viewissue_no_uploaded_files').show();
                        if ($('viewissue_uploaded_attachments_count')) $('viewissue_uploaded_attachments_count').update(json.attachmentcount);
                    }
                },
                failure: {
                    show: link_id + '_remove_link'
                }
            });
        }

        Pachno.Issues.File.remove = function (url, file_id) {
            Pachno.Core._detachFile(url, file_id, 'viewissue_files_', 'dialog_indicator');
        }

        Pachno.Issues.Field.setPercent = function (url, mode) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {indicator: 'percent_complete_spinning'},
                success: {
                    callback: function (json) {
                        Pachno.Main.updatePercentageLayout(json.percent);
                        (mode == 'set') ? Pachno.Issues.markAsChanged('percent_complete') : Pachno.Issues.markAsUnchanged('percent_complete');
                    },
                    hide: 'percent_complete_change'
                }
            });
        }

        Pachno.Issues.Field.Updaters.dualFromJSON = function (issue_id, dualfield, field) {
            if (dualfield.id == 0) {
                $(field + '_table').hide();
                $('no_' + field).show();
            } else {
                $(field + '_content').update(dualfield.name);
                if (field == 'status')
                    $('status_' + issue_id + '_color').setStyle({backgroundColor: dualfield.color});
                else if (field == 'issuetype')
                    $('issuetype_image').src = dualfield.src;
                if ($('no_' + field))
                    $('no_' + field).hide();
                if ($(field + '_table'))
                    $(field + '_table').show();
            }
        }

        Pachno.Issues.Field.Updaters.fromObject = function (issue_id, object, field) {
            var fn = field + '_' + issue_id + '_name';
            var nf = 'no_' + field + '_' + issue_id;
            if (!$(fn)) {
                fn = field + '_name';
                nf = 'no_' + field;
            }
            if ((Object.isUndefined(object.id) == false && object.id == 0) || (object.value && object.value == '')) {
                $(fn).hide();
                $(nf).show();
            } else {
                $(fn).update(object.name);
                if (object.url)
                    $(fn).href = object.url;
                $(nf).hide();
                $(fn).show();
            }
        }

        Pachno.Issues.Field.Updaters.timeFromObject = function (issue_id, object, values, field) {
            var fn = field + '_' + issue_id + '_name';
            var nf = 'no_' + field + '_' + issue_id;
            if ($(fn) && $(nf)) {
                if (object.id == 0) {
                    $(fn).hide();
                    $(nf).show();
                } else {
                    $(fn).update(object.name);
                    $(nf).hide();
                    $(fn).show();
                }
            }
            ['points', 'minutes', 'hours', 'days', 'weeks', 'months'].each(function (unit) {
                if (field != 'spent_time' && $(field + '_' + issue_id + '_' + unit + '_input'))
                    $(field + '_' + issue_id + '_' + unit + '_input').setValue(values[unit]);

                if ($(field + '_' + issue_id + '_' + unit)) {
                    $(field + '_' + issue_id + '_' + unit).update(values[unit]);
                    if (values[unit] == 0) {
                        $(field + '_' + issue_id + '_' + unit).addClassName('faded_out');
                    } else {
                        $(field + '_' + issue_id + '_' + unit).removeClassName('faded_out');
                    }
                }
            });
        }

        Pachno.Issues.Field.Updaters.allVisible = function (visible_fields) {
            Pachno.available_fields.each(function (field)
            {
                if ($(field + '_field')) {
                    if (visible_fields[field] != undefined) {
                        $(field + '_field').show();
                        if ($(field + '_additional'))
                            $(field + '_additional').show();
                    } else {
                        $(field + '_field').hide();
                        if ($(field + '_additional'))
                            $(field + '_additional').hide();
                    }
                }
            });
        }

        /**
         * This function is triggered every time an issue is updated via the web interface
         * It sends a request that performs the update, and gets JSON back
         *
         * Depending on the JSON return value, it updates fields, shows/hides boxes on the
         * page, and sets some class values
         *
         * @param url The URL to request
         * @param field The field that is being changed
         * @param serialize_form Whether a form is being serialized
         */
        Pachno.Issues.Field.set = function (url, field, serialize_form) {
            var post_form = undefined;
            if (['description', 'reproduction_steps', 'title', 'shortname'].indexOf(field) != -1) {
                post_form = field + '_form';
            } else if (serialize_form != undefined) {
                post_form = serialize_form + '_form';
            }

            var loading_show = (field == 'issuetype') ? 'issuetype_indicator_fullpage' : undefined;

            Pachno.Main.Helpers.ajax(url, {
                form: post_form,
                loading: {
                    indicator: loading_show != undefined ? loading_show : field + '_spinning',
                    clear: field + '_change_error',
                    hide: field + '_change_error'
                },
                success: {
                    callback: function (json) {
                        if (json.field != undefined)
                        {
                            if (field == 'status' || field == 'issuetype')
                                Pachno.Issues.Field.Updaters.dualFromJSON(json.issue_id, json.field, field);
                            else if (field == 'percent_complete')
                                Pachno.Main.updatePercentageLayout(json.percent);
                            else if (field == 'estimated_time') {
                                Pachno.Issues.Field.Updaters.timeFromObject(json.issue_id, json.field, json.values, field);
                                $(field + '_' + json.issue_id + '_change').hide();
                                Pachno.Issues.Field.updateEstimatedPercentbar(json);
                            }
                            else if (field == 'spent_time') {
                                Pachno.Issues.Field.Updaters.timeFromObject(json.issue_id, json.field, json.values, field);
                                $(field + '_' + json.issue_id + '_change').hide();
                            }
                            else
                                Pachno.Issues.Field.Updaters.fromObject(json.issue_id, json.field, field);

                            if (field == 'issuetype')
                                Pachno.Issues.Field.Updaters.allVisible(json.visible_fields);
                            else if (field == 'pain_bug_type' || field == 'pain_likelihood' || field == 'pain_effect')
                            {
                                $('issue_user_pain').update(json.user_pain);
                                if (json.user_pain_diff_text != '') {
                                    $('issue_user_pain_calculated').update(json.user_pain_diff_text);
                                    $('issue_user_pain_calculated').show();
                                } else {
                                    $('issue_user_pain_calculated').hide();
                                }
                            }
                        }
                        (json.changed == true) ? Pachno.Issues.markAsChanged(field) : Pachno.Issues.markAsUnchanged(field);
                        if (field == 'description' && $('description_edit')) {
                            $('description_edit').style.display = '';
                        }
                        else if (field == 'title') {
                            $('title-field').toggleClassName('editing');
                        }
                    },
                    hide: field + '_change'
                },
                failure: {
                    update: field + '_change_error',
                    show: field + '_change_error',
                    callback: function (json) {
                        new Effect.Pulsate($(field + '_change_error'));
                    }
                }
            });
        }

        Pachno.Issues.Field.setTime = function (url, field, issue_id) {
            Pachno.Main.Helpers.ajax(url, {
                form: field + '_' + issue_id + '_form',
                loading: {
                    indicator: field + '_' + issue_id + '_spinning',
                    clear: field + '_' + issue_id + '_change_error',
                    hide: field + '_' + issue_id + '_change_error'
                },
                success: {
                    callback: function (json) {
                        Pachno.Issues.Field.Updaters.timeFromObject(json.issue_id, json.field, json.values, field);
                        (json.changed == true) ? Pachno.Issues.markAsChanged(field) : Pachno.Issues.markAsUnchanged(field);
                        if ($('issue_' + issue_id)) {
                            ['points', 'hours', 'minutes'].each(function (unit) {
                                if (field == 'estimated_time') {
                                    Pachno.Issues.Field.updateEstimatedPercentbar(json);
                                    $('issue_' + issue_id).setAttribute('data-estimated-' + unit, json.values[unit]);
                                    $('issue_' + issue_id).down('.issue_estimate.' + unit).update(json.values[unit]);
                                    (parseInt(json.values[unit]) > 0) ? $('issue_' + issue_id).down('.issue_estimate.' + unit).show() : $('issue_' + issue_id).down('.issue_estimate.' + unit).hide();
                                } else {
                                    $('issue_' + issue_id).setAttribute('data-spent-' + unit, json.values[unit]);
                                    $('issue_' + issue_id).down('.issue_spent.' + unit).update(json.values[unit]);
                                    (parseInt(json.values[unit]) > 0) ? $('issue_' + issue_id).down('.issue_spent.' + unit).show() : $('issue_' + issue_id).down('.issue_spent.' + unit).hide();
                                }
                                $('issue_' + issue_id).dataset.lastUpdated = get_current_timestamp();
                            });
                            var fields = $('issue_' + issue_id).select('.sc_' + field);
                            if (fields.size() > 0) {
                                fields.each(function (sc_element) {
                                    if (json.field.name) {
                                        $(sc_element).update(json.field.name);
                                        $(sc_element).removeClassName('faded_out');
                                    } else {
                                        $(sc_element).update('-');
                                        $(sc_element).addClassName('faded_out');
                                    }
                                });
                            }
                        }
                        if ($('milestones-list')) {
                            Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails($('issue_' + issue_id).up('.milestone-issues'));
                            Pachno.Project.Planning.calculateNewBacklogMilestoneDetails();
                        }
                    },
                    hide: field + '_' + issue_id + '_change'
                },
                failure: {
                    update: field + '_' + issue_id + '_change_error',
                    show: field + '_' + issue_id + '_change_error',
                    callback: function (json) {
                        new Effect.Pulsate($(field + '_' + issue_id + '_change_error'));
                    }
                }
            });
        }

        Pachno.Issues.Field.revert = function (url, field)
        {
            var loading_show = (field == 'issuetype') ? 'issuetype_indicator_fullpage' : undefined;

            Pachno.Issues.markAsUnchanged(field);

            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: loading_show != undefined ? loading_show : field + '_undo_spinning'
                },
                success: {
                    callback: function (json) {
                        if (json.field != undefined) {
                            if (field == 'status' || field == 'issuetype')
                                Pachno.Issues.Field.Updaters.dualFromJSON(json.issue_id, json.field, field);
                            else if (field == 'estimated_time') {
                                Pachno.Issues.Field.Updaters.timeFromObject(json.issue_id, json.field, json.values, field);
                                Pachno.Issues.Field.updateEstimatedPercentbar(json);
                            }
                            else if (field == 'spent_time')
                                Pachno.Issues.Field.Updaters.timeFromObject(json.issue_id, json.field, json.values, field);
                            else if (field == 'percent_complete')
                                Pachno.Main.updatePercentageLayout(json.field);
                            else
                                Pachno.Issues.Field.Updaters.fromObject(json.issue_id, json.field, field);

                            if (field == 'issuetype')
                                Pachno.Issues.Field.Updaters.allVisible(json.visible_fields);
                            else if (field == 'description' || field == 'reproduction_steps')
                                $(field + '_form_value').update(json.field.form_value);
                            else if (field == 'pain_bug_type' || field == 'pain_likelihood' || field == 'pain_effect')
                                $('issue_user_pain').update(json.field.user_pain);

                            if (field == 'description') {
                                $('description_edit').style.display = '';
                                $('description_change').hide();
                            }
                        }

                    }
                },
                failure: {
                    callback: function () {
                        Pachno.Issues.markAsChanged(field);
                    }
                }
            });
        }

        Pachno.Issues.Field.incrementTimeMinutes = function (minutes, input)
        {
            if (minutes > 60 || minutes < 0) return;

            var hour_input = input.replace('minutes', 'hours');

            // Increment hour by one for 60 minutes
            if (minutes == 60 && $(hour_input)) {
              $(hour_input).setValue((parseInt($(hour_input).getValue()) || 0) + 1);
              return;
            }

            if (! $(input)) return;

            var new_minutes = (parseInt($(input).getValue()) || 0) + minutes;

            if (new_minutes >= 60 && $(hour_input)) {
                $(hour_input).setValue((parseInt($(hour_input).getValue()) || 0) + 1);
                new_minutes = new_minutes - 60;
            }

            $(input).setValue(new_minutes);
        }

        Pachno.Issues.markAsChanged = function (field)
        {
            if ($('viewissue_changed') != undefined) {
                if (!$('viewissue_changed').visible()) {
                    $('viewissue_changed').show();
                    Effect.Pulsate($('issue-messages-container'), {pulses: 3, duration: 2});
                }

                $(field + '_field').addClassName('issue_detail_changed');
                if (field == 'issuetype') {
                    jQuery("#workflow-actions input[type='submit'], #workflow-actions input[type='button']").prop("disabled", true);
                    jQuery("#workflow-actions a").off('click');
                }
            }

            if ($('comment_save_changes'))
                $('comment_save_changes').checked = true;
        }

        Pachno.Issues.markAsUnchanged = function (field)
        {
            if ($(field + '_field') && $('issue_view')) {
                $(field + '_field').removeClassName('issue_detail_changed');
                $(field + '_field').removeClassName('issue_detail_unmerged');
                if ($('issue_view').select('.issue_detail_changed').size() == 0) {
                    $('viewissue_changed').hide();
                    $('viewissue_merge_errors').hide();
                    $('viewissue_unsaved').hide();
                    if ($('comment_save_changes'))
                        $('comment_save_changes').checked = false;
                }
                if (field == 'issuetype') {
                    jQuery("#workflow-actions input[type='submit'], #workflow-actions input[type='button']").prop("disabled", false);
                    jQuery("#workflow-actions a").on('click');
                }
            }
        }

        Pachno.Issues.ACL.toggle_checkboxes = function (element, issue_id, val) {
            if (! jQuery(element).is(':checked')) return;

            switch (val) {
                case 'public':
                    $('acl_' + issue_id + '_public').show();
                    $('acl_' + issue_id + '_restricted').hide();
                    $('issue_' + issue_id + '_public_category_access_list').hide();
                    $('issue_access_public_category_input').disable();
                    $('acl-users-teams-selector').hide();
                    break;
                case 'public_category':
                    $('acl_' + issue_id + '_public').show();
                    $('acl_' + issue_id + '_restricted').hide();
                    $('issue_' + issue_id + '_public_category_access_list').show();
                    $('issue_access_public_category_input').enable();
                    $('acl-users-teams-selector').show();
                    break;
                case 'restricted':
                    $('acl_' + issue_id + '_public').hide();
                    $('acl_' + issue_id + '_restricted').show();
                    $('acl-users-teams-selector').show();
                    break;
            }
        };

        Pachno.Issues.ACL.toggle_custom_access = function (element) {
            if (jQuery(element).is(':checked')) {
                jQuery('.report-issue-custom-access-container').show();
                jQuery('.report-issue-custom-access-container input[name=issue_access]').trigger('change');
            }
            else {
                jQuery('.report-issue-custom-access-container').hide();
            }
        };

        Pachno.Issues.ACL.addTarget = function (url, issue_id) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'popup_find_acl_' + issue_id + '_spinning'
                },
                success: {
                    update: {},
                    callback: function(json) {
                        $('issue_' + issue_id + '_restricted_access_list').insert({bottom: json.content});
                        $('issue_' + issue_id + '_public_category_access_list').insert({bottom: json.content});
                        $('issue_' + issue_id + '_restricted_access_list_none').hide();
                        $('issue_' + issue_id + '_public_category_access_list_none').hide();
                    },
                    hide: 'popup_find_acl_' + issue_id
                }
            });
        };

        Pachno.Issues.ACL.set = function (url, issue_id, mode) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'acl_' + issue_id + '_' + mode + 'form',
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop']
                },
                success: {
                    callback: Pachno.Main.Helpers.Backdrop.reset
                }
            });
        };

        Pachno.Issues.Affected.toggleConfirmed = function (url, affected)
        {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    callback: function () {
                        $('affected_' + affected + '_state').up('.affected-state').addClassName('loading');
                    }
                },
                success: {
                    callback: function (json) {
                        $('affected_' + affected + '_state').update(json.text);
                        $('affected_' + affected + '_state').up('.affected-state').toggleClassName('unconfirmed');
                        $('affected_' + affected + '_state').up('.affected-state').toggleClassName('confirmed');
                        $('affected_' + affected + '_state').up('.affected-state').removeClassName('loading');
                    }
                },
                complete: {
                    callback: function () {
                        $('affected_' + affected + '_state').up('.affected-state').removeClassName('loading');
                    }
                }
            });
        }

        Pachno.Issues.Affected.remove = function (url, affected)
        {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'fullpage_backdrop',
                    show: 'fullpage_backdrop_indicator',
                    hide: ['fullpage_backdrop_content', 'dialog_backdrop']
                },
                success: {
                    update: {element: 'viewissue_affects_count', from: 'itemcount'},
                    remove: ['affected_' + affected + '_delete', 'affected_' + affected],
                    callback: function (json) {
                        if (json.itemcount == 0)
                            $('no_affected').show();
                    }
                }
            });
        }

        Pachno.Issues.Affected.setStatus = function (url, affected)
        {
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'affected_' + affected + '_status_spinning'
                },
                success: {
                    callback: function (json) {
                        $('affected_' + affected + '_status').setStyle({
                            backgroundColor: json.colour,
                        });
                    },
                    update: {element: 'affected_' + affected + '_status', from: 'name'},
                    hide: 'affected_' + affected + '_status_change'
                },
                failure: {
                    update: {element: 'affected_' + affected + '_status_error', from: 'error'},
                    show: 'affected_' + affected + '_status_error',
                    callback: function (json) {
                        new Effect.Pulsate($('affected_' + affected + '_status_error'));
                    }
                }
            });
        }

        Pachno.Issues.Affected.add = function (url)
        {
            Pachno.Main.Helpers.ajax(url, {
                form: 'viewissue_add_item_form',
                loading: {
                    indicator: 'add_affected_spinning'
                },
                success: {
                    callback: function (json) {
                        if ($('viewissue_affects_count'))
                            $('viewissue_affects_count').update(json.itemcount);
                        if (json.itemcount != 0 && $('no_affected'))
                            $('no_affected').hide();
                        Pachno.Main.Helpers.Backdrop.reset();
                    },
                    update: {element: 'affected_list', insertion: true},
                }
            });
        }

        Pachno.Issues.updateWorkflowAssignee = function (url, assignee_id, assignee_type, transition_id, teamup)
        {
            teamup = (teamup == undefined) ? 0 : 1;
            Pachno.Main.Helpers.ajax(url, {
                loading: {
                    indicator: 'popup_assigned_to_name_indicator_' + transition_id,
                    hide: 'popup_no_assigned_to_' + transition_id,
                    show: 'popup_assigned_to_name_' + transition_id
                },
                success: {
                    update: 'popup_assigned_to_name_' + transition_id
                },
                complete: {
                    callback: function () {
                        $('popup_assigned_to_id_' + transition_id).setValue(assignee_id);
                        $('popup_assigned_to_type_' + transition_id).setValue(assignee_type);
                        $('popup_assigned_to_teamup_' + transition_id).setValue(teamup);
                        if (teamup) {
                            $('popup_assigned_to_teamup_info_' + transition_id).show();
                        } else {
                            $('popup_assigned_to_teamup_info_' + transition_id).hide();
                        }
                    },
                    hide: ['popup_assigned_to_teamup_info_' + transition_id, 'popup_assigned_to_change_' + transition_id]
                }
            });
        }

        Pachno.Issues.updateWorkflowAssigneeTeamup = function (url, assignee_id, assignee_type, transition_id)
        {
            Pachno.Issues.updateWorkflowAssignee(url, assignee_id, assignee_type, transition_id, true);
        }

        Pachno.Issues.removeTodo = function (url, todo) {
            Pachno.Main.Helpers.ajax(url, {
                params: {
                    todo: todo
                },
                loading: {
                    indicator: 'dialog_indicator'
                },
                success: {
                    update: 'viewissue_todos',
                    callback: Pachno.Main.Helpers.Dialog.dismiss
                }
            });
        };

        Pachno.Issues.markTodo = function (url, todo, todo_key) {
            Pachno.Main.Helpers.ajax(url, {
                params: {
                    todo: todo
                },
                loading: {
                    indicator: 'todo_' + todo_key + '_mark_indicator',
                    callback: function () {
                        $$('#todo_' + todo_key + '_mark_wrapper .image i').each(function (element) {
                            $(element).hide();
                        });
                    }
                },
                success: {update: 'viewissue_todos'}
            });
        };

        Pachno.Issues.showTodo = function () {
            $$('.todo_editor').each(Element.hide);
            $('todo_add_button').hide();
            $('todo_add').show();
            $('todo_bodybox').focus();
        };

        Pachno.Issues.addTodo = function (url) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'todo_form',
                loading: {
                    indicator: 'todo_add_indicator',
                    disable: 'todo_add_button'
                },
                success: {
                    hide: ['todo_add_indicator', 'todo_add'],
                    clear: 'todo_bodybox',
                    update: 'viewissue_todos'
                }
            });
        };

        Pachno.Search.deleteSavedSearch = function (url, id) {
            Pachno.Main.Helpers.ajax(url, {
                loading: {indicator: 'delete_search_' + id + '_indicator'},
                success: {hide: 'saved_search_' + id + '_container'}
            });
        };

        Pachno.Search.toPage = function (url, parameters, offset, button) {
            parameters += '&offset=' + offset;
            Pachno.Main.Helpers.ajax(url, {
                params: parameters,
                loading: {
                    callback: function() {
                        jQuery(this).addClass('submitting');
                    }
                },
                success: {
                    update: 'search-results',
                    callback: function() {
                        jQuery(this).removeClass('submitting');
                    }
                }
            });
        };

        Pachno.Search.toggleColumn = function (column) {
            $$('.sc_' + column).each(function (element) {
                element.toggle();
            });
        };

        Pachno.Search.resetColumns = function () {
            Pachno.Search.ResultViews[Pachno.Search.current_result_view].visible.each(function (column) {
                if (Pachno.Search.ResultViews[Pachno.Search.current_result_view].default_visible.indexOf(column) != -1) {
                    Pachno.Search.setFilterValue($('search_column_' + column + '_toggler'), true);
                    $$('.sc_' + column).each(Element.show);
                } else {
                    Pachno.Search.setFilterValue($('search_column_' + column + '_toggler'), false);
                    $$('.sc_' + column).each(Element.hide);
                }
            });
            Pachno.Search.saveColumnVisibility();
        };

        Pachno.Search.setColumns = function (resultview, available_columns, visible_columns, default_columns) {
            Pachno.Search.current_result_view = resultview;
            Pachno.Search.ResultViews[resultview] = {
                available: available_columns,
                visible: visible_columns,
                default_visible: default_columns
            };
            Pachno.Search.ResultViews[resultview].available.each(function (column) {
                if (Pachno.Search.ResultViews[resultview].visible.indexOf(column) != -1) {
                    Pachno.Search.setFilterValue($('search_column_' + column + '_toggler'), true);
                } else {
                    Pachno.Search.setFilterValue($('search_column_' + column + '_toggler'), false);
                }
            });
            $('scs_current_template').setValue(resultview);
        };

        Pachno.Search.checkToggledCheckboxes = function () {
            var num_checked = 0,
                sr = $('search-results');

            if (sr) {
                sr.select('input[type=checkbox]').each(function (elm) {
                    if (elm.checked)
                        num_checked++;
                });
            }

            if (num_checked == 0) {
                $('search-bulk-actions').addClassName('unavailable');
                $('bulk_action_submit').addClassName('disabled');
            } else {
                $('search-bulk-actions').removeClassName('unavailable');
                var selected_radio_value = jQuery('input[name=search_bulk_action]:checked', '#search-bulk-action-form').val();
                if (selected_radio_value) {
                    $('bulk_action_submit').removeClassName('disabled');
                }
            }
        };

        Pachno.Search.toggleCheckboxes = function () {
            var do_check = true;

            if ($(this).hasClassName('semi-checked')) {
                $(this).removeClassName('semi-checked');
                $(this).checked = true;
                do_check = true;
            } else {
                do_check = $(this).checked;
            }

            $(this).up('.results_container').down('.results_body').select('input[type=checkbox]').each(function (element) {
                element.checked = do_check;
            });

            Pachno.Search.checkToggledCheckboxes();
        };

        Pachno.Search.toggleCheckbox = function () {
            var num_unchecked = 0;
            var num_checked = 0;
            this.up('.results_container').select('input[type=checkbox]').each(function (elm) {
                if (!elm.checked)
                    num_unchecked++;
                if (elm.checked)
                    num_checked++;
            });

            var chk_box = this.up('.results_body').down('.row.header').down('input[type=checkbox]');
            if (num_unchecked == 0) {
                chk_box.checked = true;
                chk_box.removeClassName('semi-checked');
            } else if (num_checked > 0) {
                chk_box.checked = true;
                chk_box.addClassName('semi-checked');
            } else {
                chk_box.checked = false;
                chk_box.removeClassName('semi-checked');
            }

            Pachno.Search.checkToggledCheckboxes();
        };

        Pachno.Search.bulkContainerChanger = function () {
            var selected_radio_value = jQuery('input[name=search_bulk_action]:checked', '#search-bulk-action-form').val(),
                sub_container_id = 'bulk_action_subcontainer_' + selected_radio_value;

            $$('.bulk_action_subcontainer').each(function (element) {
                element.hide();
            });
            if ($(sub_container_id)) {
                $(sub_container_id).show();
                $('bulk_action_submit').removeClassName('disabled');
                var dropdown_element = $(sub_container_id + '').down('.focusable');
                if (dropdown_element != undefined)
                    dropdown_element.focus();
            } else {
                $('bulk_action_submit').addClassName('disabled');
            }
        };

        Pachno.Search.bulkChanger = function (mode) {
            var sub_container_id = 'bulk_action_' + $('bulk_action_selector_' + mode).getValue();
            var opp_mode = (mode == 'top') ? 'bottom' : 'top';

            if ($('bulk_action_selector_' + mode).getValue() == '') {
                $('bulk_action_submit_' + mode).addClassName('disabled');
                $('bulk_action_submit_' + opp_mode).addClassName('disabled');
            } else if (!$('search-bulk-actions_' + mode).hasClassName('unavailable')) {
                $('bulk_action_submit_' + mode).removeClassName('disabled');
                $('bulk_action_submit_' + opp_mode).removeClassName('disabled');
            }
            $(sub_container_id + '_' + opp_mode).value = $(sub_container_id + '_' + mode).getValue();
        }

        Pachno.Search.bulkPostProcess = function (json) {
            if (json.last_updated) {
                if (json.milestone_name != undefined && json.milestone_id) {
                    if ($('milestones-list') != undefined) {
                        if ($('milestone_' + json.milestone_id) == undefined) {
                            Pachno.Project.Milestone.retrieve(json.milestone_url, json.milestone_id, json.issue_ids);
                        }
                    }
                    if ($('bulk_action_assign_milestone_top') != undefined && $('bulk_action_assign_milestone_top_' + json.milestone_id) == undefined) {
                        $('bulk_action_assign_milestone_top').insert('<option value="' + json.milestone_id + '" id="bulk_action_assign_milestone_top_' + json.milestone_id + '">' + json.milestone_name + '</option>');
                        $('bulk_action_assign_milestone_top').setValue(json.milestone_id);
                        $('bulk_action_assign_milestone_top_name').hide();
                    }
                    if ($('bulk_action_assign_milestone_bottom') != undefined && $('bulk_action_assign_milestone_bottom_' + json.milestone_id) == undefined) {
                        $('bulk_action_assign_milestone_bottom').insert('<option value="' + json.milestone_id + '" id="bulk_action_assign_milestone_bottom_' + json.milestone_id + '">' + json.milestone_name + '</option>');
                        $('bulk_action_assign_milestone_bottom').setValue(json.milestone_id);
                        $('bulk_action_assign_milestone_bottom_name').hide();
                    }
                }
                json.issue_ids.each(function (issue_id) {
                    var issue_elm = $('issue_' + issue_id);
                    if (issue_elm != undefined) {
                        if (json.milestone_name != undefined) {
                            var milestone_container = issue_elm.down('.sc_milestone');
                            if (milestone_container != undefined) {
                                milestone_container.update(json.milestone_name);
                                if (json.milestone_name != '-') {
                                    milestone_container.removeClassName('faded_out');
                                } else {
                                    milestone_container.addClassName('faded_out');
                                }
                            }
                        }
                        if (json.status != undefined) {
                            var status_container = issue_elm.down('.sc_status');
                            if (status_container != undefined) {
                                status_container.down('.sc_status_name').update(json.status['name']);
                                var status_color_item = status_container.down('.sc_status_color');
                                if (status_color_item)
                                    status_color_item.setStyle({backgroundColor: json.status['color']});
                            }
                        }
                        ['resolution', 'priority', 'category', 'severity'].each(function (action) {
                            if (json[action] != undefined) {
                                var data_container = issue_elm.down('.sc_' + action);
                                if (data_container != undefined) {
                                    data_container.update(json[action]['name']);
                                    if (json[action]['name'] != '-') {
                                        data_container.removeClassName('faded_out');
                                    } else {
                                        data_container.addClassName('faded_out');
                                    }
                                }
                                if ($(action + '_selector_' + issue_id) != undefined) {
                                    $(action + '_selector_' + issue_id).setValue(json[action]['id']);
                                }
                            }
                        });
                        var last_updated_container = issue_elm.down('.sc_last_updated');
                        if (last_updated_container != undefined) {
                            last_updated_container.update(json.last_updated);
                        }
                        if (json.closed != undefined) {
                            if (json.closed) {
                                issue_elm.addClassName('closed');
                            } else {
                                issue_elm.removeClassName('closed');
                            }
                        }
                    }
                });
                Pachno.Search.liveUpdate(true);
            }
        }

        Pachno.Search.interactiveWorkflowTransition = function (url, transition_id, form) {
            Pachno.Main.Helpers.ajax(url, {
                form: form,
                loading: {
                    indicator: 'transition_working_' + transition_id + '_indicator',
                    callback: function () {
                        $$('.workflow_transition_submit_button').each(function (element) {
                            $(element).addClassName('disabled');
                            $(element).writeAttribute('disabled');
                        });
                    }
                },
                success: {
                    callback: function (json) {
                        Pachno.Core.Pollers.Callbacks.planningPoller();
                        Pachno.Main.Helpers.Backdrop.reset();
                        Pachno.Search.liveUpdate(true);
                    }
                },
                complete: {
                    callback: function () {
                        $$('.workflow_transition_submit_button').each(function (element) {
                            $(element).removeClassName('disabled');
                            $(element).writeAttribute('disabled', false);
                        });
                    }
                }
            });
        }

        Pachno.Search.nonInteractiveWorkflowTransition = function () {
            // No need to remove 'disabled' class and attribute since form that is submitted
            // will refresh page.
            $$('.workflow_transition_submit_button').each(function (element) {
                $(element).addClassName('disabled');
                $(element).writeAttribute('disabled');
            });
        }

        Pachno.Search.bulkWorkflowTransition = function (url, transition_id) {
            Pachno.Main.Helpers.ajax(url, {
                form: 'bulk_workflow_transition_form',
                loading: {
                    indicator: 'transition_working_' + transition_id + '_indicator',
                    callback: function () {
                        $$('.workflow_transition_submit_button').each(function (element) {
                            $(element).addClassName('disabled');
                            $(element).writeAttribute('disabled');
                        });
                    }
                },
                success: {
                    callback: function (json) {
                        Pachno.Search.bulkPostProcess(json)
                        Pachno.Main.Helpers.Backdrop.reset();
                    }
                },
                complete: {
                    callback: function () {
                        $$('.workflow_transition_submit_button').each(function (element) {
                            $(element).removeClassName('disabled');
                            $(element).writeAttribute('disabled', false);
                        });
                    }
                }
            });
        };

        Pachno.Search.bulkUpdate = function (url) {
            if ($('bulk_action_selector').getValue() == '')
                return;
            var issues = '';
            $('search-results').select('tbody input[type=checkbox]').each(function (element) {
                if (element.checked)
                    issues += '&issue_ids[' + element.getValue() + ']=' + element.getValue();
            });

            if ($('bulk_action_selector').getValue() == 'perform_workflow_step') {
                Pachno.Main.Helpers.Backdrop.show($('bulk_action_subcontainer_perform_workflow_step_url').getValue() + issues);
            } else {
                Pachno.Main.Helpers.ajax(url, {
                    form: 'search-bulk-action-form',
                    additional_params: issues,
                    loading: {
                        indicator: 'fullpage_backdrop',
                        show: 'fullpage_backdrop_indicator',
                        hide: 'fullpage_backdrop_content'
                    },
                    success: {
                        callback: Pachno.Search.bulkPostProcess
                    }
                });
            }
        };

        Pachno.Search.moveDown = function (event) {
            var selected_elements = $('search-results').select('tr.selected');
            var old_selected_element = (selected_elements.size() == 0) ? undefined : selected_elements[0];
            var new_selected_element = (old_selected_element == undefined) ? $('search-results').select('table tbody tr')[0] : old_selected_element.next();

            Pachno.Search.move(old_selected_element, new_selected_element, event, true);
        };

        Pachno.Search.moveUp = function (event) {
            var selected_elements = $('search-results').select('tr.selected');
            var old_selected_element = (selected_elements.size() == 0) ? undefined : selected_elements[selected_elements.size() - 1];
            var new_selected_element = (old_selected_element == undefined) ? $('search-results').select('table tbody tr')[0] : old_selected_element.previous();

            Pachno.Search.move(old_selected_element, new_selected_element, event, true);
        };

        Pachno.Search.move = function (old_selected_element, new_selected_element, event, move) {
            if (old_selected_element && new_selected_element) {
                $(old_selected_element).removeClassName('selected');
            }
            if (new_selected_element) {
                var ns = $(new_selected_element);
                ns.addClassName('selected');
                var offsets = ns.cumulativeOffset();
                var dimensions = ($('search-bulk-action-form')) ? $('search-bulk-action-form').getDimensions() : ns.getDimensions();
                if (event)
                    event.preventDefault();
                if (move) {
                    var top = document.viewport.getScrollOffsets().top;
                    var v_height = document.viewport.getDimensions().height;
                    var bottom = top + v_height;
                    var is_above = top > offsets.top - dimensions.height;
                    var is_below = bottom < offsets.top + dimensions.height;
                    if (is_above || is_below) {
                        if (is_above)
                            window.scrollTo(0, offsets.top - dimensions.height);
                        if (is_below)
                            window.scrollTo(0, offsets.top + dimensions.height - v_height);
                    }
                }
            }
        }

        Pachno.Search.moveTo = function (event) {
            var selected_elements = $('search-results').select('tr.selected');
            if (selected_elements.size() > 0) {
                var selected_issue = selected_elements[0];
                var link = selected_issue.select('a.issue_link')[0];
                if (link) {
                    window.location = link.href;
                    event.preventDefault();
                }
            }
        };

        Pachno.Search.getFilterValues = function (element) {
            var filter = element.up('.filter');
            var results_container = filter.down('.filter_callback_results');
            var existing_container = filter.down('.filter_existing_values');
            var url = element.dataset.callbackUrl;
            var value = element.getValue();
            results_container.childElements().each(function (existing_element) {
                if (existing_element.hasClassName('selected')) {
                    existing_container.insert(existing_element.remove());
                }
            });
            if (value == '') {
                results_container.update('');
                Pachno.Search.filterFilterOptionsElement(element);
            } else {
                var parameters = '&filter=' + value;
                filter.down('.filter_existing_values').select('input[type=checkbox]').each(function (checkbox) {
                    parameters += '&existing_id[' + checkbox.value + ']=1';
                });
                Pachno.Main.Helpers.ajax(url, {
                    params: parameters,
                    loading: {
                        callback: function () {
                            Pachno.Search.filterFilterOptionsElement(element);
                            element.addClassName('filtering');
                        }
                    },
                    success: {
                        callback: function (json) {
                            results_container.update(json.results);
                            element.removeClassName('filtering');
                        }
                    }
                });
            }
        };

        Pachno.Search.initializeFilterField = function (filter, hidden) {
            // filter.on('click', Pachno.Search.toggleInteractiveFilter);
            // filter.select('li.filtervalue').each(function (filtervalue) {
            //     filtervalue.on('click', Pachno.Search.toggleFilterValue);
            // });
            // Pachno.Search.initializeFilterSearchValues(filter);
            // Pachno.Search.initializeFilterNavigation(filter);
            // Pachno.Search.calculateFilterDetails(filter);
            if (!hidden && filter.dataset.isdate == '') {
                var filter_key = filter.dataset.filterkey;
                Calendar.setup({
                    dateField: jQuery('.filter_' + filter_key + '_value_input', filter)[0],
                    parentElement: jQuery('.filter_' + filter_key + '_calendar_container', filter)[0],
                    valueCallback: Pachno.Search.setInteractiveDate
                });
            }
        };

        Pachno.Search.filterFilterOptionsElement = function (element) {
            var filtervalue = element.val().toLowerCase(),
                $filterContainer = jQuery(element.closest('.filter-values-container'));

            if (filtervalue !== element.data('previousValue')) {
                if (filtervalue !== '') {
                    $filterContainer.addClass('filtered');
                } else {
                    $filterContainer.removeClass('filtered');
                }

                $filterContainer.find('.filtervalue').each(function () {
                    var $filterElement = jQuery(this);
                    if ($filterElement.hasClass('sticky'))
                        return;

                    if (filtervalue !== '') {
                        if ($filterElement.text().toLowerCase().indexOf(filtervalue) !== -1 || $filterElement.hasClass('selected')) {
                            $filterElement.addClass('visible');
                        } else {
                            $filterElement.removeClass('visible');
                        }
                    } else {
                        $filterElement.addClass('visible');
                    }
                    $filterElement.removeClass('highlighted');
                });
                element.data('previousValue', filtervalue);
            }
        };

        Pachno.Search.moveFilterDown = function (event, filter) {
            var available_elements = filter.select('.filtervalue.unfiltered');
            var selected_elements = filter.select('li.highlighted');
            var old_selected_element = (selected_elements.size() == 0) ? undefined : selected_elements[0];
            var new_selected_element = (old_selected_element == undefined) ? available_elements[0] : old_selected_element.next('.filtervalue');
            if (new_selected_element === undefined && available_elements.size() > 1)
                new_selected_element = available_elements[0];

            Pachno.Search.moveFilter(old_selected_element, new_selected_element, event);
        };

        Pachno.Search.moveFilterUp = function (event, filter) {
            var available_elements = filter.select('.filtervalue.unfiltered');
            var selected_elements = filter.select('li.highlighted');
            var old_selected_element = (selected_elements.size() == 0) ? undefined : selected_elements[0];
            var new_selected_element = (old_selected_element == undefined) ? available_elements[0] : old_selected_element.previous('.filtervalue');
            if (new_selected_element === undefined && available_elements.size() > 1)
                new_selected_element = available_elements.last();

            Pachno.Search.moveFilter(old_selected_element, new_selected_element, event);
        };

        Pachno.Search.moveFilter = function (old_selected_element, new_selected_element, event) {
            if (old_selected_element && new_selected_element) {
                $(old_selected_element).removeClassName('highlighted');
            }
            if (new_selected_element) {
                var ns = $(new_selected_element);
                ns.addClassName('highlighted');
                if (event)
                    event.preventDefault();
            }
        };

        Pachno.Search.addFilter = function () {
            if (this.hasClassName('disabled')) return;

            var filter_key = this.dataset.filter;
            var filter_element = jQuery('#search-filters-hidden-container .interactive_filter_' + filter_key);

            if (filter_element.data('isdate') == '') {
                var filter_element_clone = filter_element.clone().appendTo('#search-filters')[0];
            }
            else {
                $('search-filters').insert($('interactive_filter_' + filter_key).remove());
            }
            this.addClassName('disabled');
        };

        Pachno.Search.removeFilter = function (event) {
            var element = jQuery(this).closest('.filter');

            if (jQuery(element).data('isdate') == '') {
                var do_update = (jQuery('filter_' + element.dataset.filterkey + '_value_input', element).val() != '');
                element.remove();
            }
            else {
                var do_update = ($('filter_' + element.dataset.filterkey + '_value_input').getValue() != '');
                $('additional_filter_' + element.dataset.filterkey + '_link').removeClassName('disabled');
                $('search-filters-hidden-container').insert(element.remove());
            }

            if (do_update)
                Pachno.Search.liveUpdate();
        };

        Pachno.Search.saveColumnVisibility = function (force) {
            var fif = $('find_issues_form');
            if (fif.dataset.isSaved === undefined || force === true) {
                var scc = $('search_columns_container');
                var parameters = fif.serialize();
                Pachno.Main.Helpers.ajax(scc.dataset.url, {
                    params: parameters,
                    loading: {indicator: 'search_column_settings_indicator'},
                    success: {hide: 'search_column_settings_indicator'}
                });
            }
        };

        Pachno.Search.updateColumnVisibility = function (event, element) {
            event.preventDefault();
            event.stopPropagation();
            if (element.down('input').checked) {
                Pachno.Search.setFilterValue(element, false);
            } else {
                Pachno.Search.setFilterValue(element, true);
            }
            Pachno.Search.toggleColumn(element.dataset.value);
            Pachno.Search.saveColumnVisibility(true);
        };

        Pachno.Search.initializeFilters = function () {
            var fif = $('find_issues_form');
            fif.reset();
            $('search_columns_container').select('li').each(function (element) {
                element.on('click', Pachno.Search.updateColumnVisibility);
            });
            $('search_grouping_container').select('li').each(function (element) {
                element.on('click', Pachno.Search.setGrouping);
            });
            $$('.template-picker').each(function (element) {
                element.on('click', Pachno.Search.pickTemplate);
            });

            let $body = jQuery('body');
            $body.on('change', 'input[type=radio].bulk-action-checkbox', Pachno.Search.bulkContainerChanger);

            $body.on('change', '.filter .fancy-dropdown input[type=checkbox],.filter .fancy-dropdown input[type=radio]', function () {
                var filter = jQuery(this);
                // if (jQuery('.filter_' + filter.data('filterkey'), filter).length) {
                //     jQuery('.filter_' + filter.data('filterkey'), filter).data('dirty', 'dirty');
                // }
                // else {
                //     $('filter_' + filter.data('filterkey')).data('dirty', 'dirty');
                // }
                Pachno.Search.liveUpdate(true);
            });

            Pachno.Search.initializeIssuesPerPageSlider();

            var sff = $('search-filters');
            $('add-search-filter-button').select('.list-item').each(function (element) {
                element.on('click', Pachno.Search.addFilter);
                if (sff.down('#interactive_filter_' + element.dataset.filter)) {
                    element.addClassName('disabled');
                }
            });
            var ifts = $$('.filter_searchfield');
            Pachno.ift_observers = {};
            ifts.each(function (ift) {
                ift.dataset.lastValue = '';
                ift.on('keyup', function (event, element) {
                    if (Pachno.ift_observers[ift.id])
                        clearTimeout(Pachno.ift_observers[ift.id]);
                    if ((ift.getValue().length >= 3 || ift.getValue().length == 0 || (ift.dataset.maxlength && ift.getValue().length > parseInt(ift.dataset.maxlength))) && ift.getValue() != ift.dataset.lastValue) {
                        Pachno.ift_observers[ift.id] = setTimeout(function () {
                            Pachno.Search.liveUpdate(true);
                            ift.dataset.lastValue = ift.getValue();
                            var flt = ift.up('.filter');
                            if (flt !== undefined) {
                                Pachno.Search.updateFilterVisibleValue(flt, ift.getValue());
                            }
                        }, 1000);
                    }
                });

            });
        };

        Pachno.Search.pickTemplate = function (event, element) {
            event.stopPropagation();
            var is_selected = this.hasClassName('selected');
            var current_elm = this;
            if (!is_selected) {
                $$('.template-picker').each(function (element) {
                    if (element == current_elm) {
                        current_elm.addClassName('selected');
                        $('filter_selected_template').setValue(current_elm.dataset.templateName);
                        if (current_elm.dataset.grouping == '1') {
                            $('search_grouping_container').removeClassName('nogrouping');
                            $('search_grouping_container').removeClassName('parameter');
                            $('search_filter_parameter_input').disable();
                        } else {
                            $('search_grouping_container').addClassName('nogrouping');
                            if (current_elm.dataset.parameter == '1') {
                                $('search_grouping_container').addClassName('parameter');
                                $('search_filter_parameter_description').update(current_elm.dataset.parameterText)
                                $('search_filter_parameter_input').enable();
                            } else {
                                $('search_grouping_container').removeClassName('parameter');
                            }
                        }
                    } else {
                        element.removeClassName('selected');
                    }
                });
            }
            $$('.filter,.interactive_plus_button').each(function (element) {
                if (element != this)
                    element.removeClassName('selected');
            });
            if (is_selected)
                this.removeClassName('selected');
            else
                this.addClassName('selected');

            Pachno.Search.liveUpdate();
        };

        Pachno.Search.setGrouping = function (event, element) {
            event.stopPropagation();
            Pachno.Search.setFilterSelectionGroupSelections(this);
            Pachno.Search.setFilterValue(element, true);

            if (element.hasClassName('groupby')) {
                if (element.dataset.groupby == '') {
                    $('filter_grouping_options').select('.grouporder').each(Element.hide);
                } else {
                    $('filter_grouping_options').select('.grouporder').each(Element.show);
                }
            }

            Pachno.Search.liveUpdate();
        };

        Pachno.Search.toggleInteractiveFilter = function (event, element) {
            event.stopPropagation();
            if (['INPUT'].indexOf(event.target.nodeName) != -1)
                return;
            Pachno.Search.toggleInteractiveFilterElement(this);
        };

        Pachno.Search.moveIssuesPerPageSlider = function (step) {
            var steps = [25, 50, 100, 250, 500];
            var value = steps[step - 1];
            $('issues_per_page_slider_value').update(value);
            return value;
        };

        Pachno.Search.isDirty = function () {
            if ($('filter_project_id_value_input').dataset.dirty == 'dirty')
                return true;
            if ($('filter_subprojects_value_input') && $('filter_subprojects_value_input').dataset.dirty == 'dirty')
                return true;

            return false;
        };

        Pachno.Search.clearDirty = function () {
            $('filter_project_id_value_input').dataset.dirty = undefined;
            $('filter_subprojects_value_input').dataset.dirty = undefined;
        };

        Pachno.Search.loadDynamicChoices = function () {
            var fif = $('find_issues_form');
            if (!fif) {
                return;
            }
            var url = fif.dataset.dynamicCallbackUrl;
            var parameters = '&project_id=' + $('filter_project_id_value_input').getValue();
            var filters_containers = [];
            var fsvi = $('filter_subprojects_value_input');
            if (fsvi)
                parameters += '&subprojects=' + fsvi.getValue();
            ['build', 'component', 'edition', 'milestone'].each(function (elm) {
                var filter = $('interactive_filter_' + elm);
                var results_container = filter.down('.interactive_menu_values');
                results_container.select('input[type=checkbox]').each(function (checkbox) {
                    if (checkbox.checked)
                        parameters += '&existing_ids[' + filter.dataset.filterkey + '][' + checkbox.value + ']=' + checkbox.value;
                });
                filters_containers.push({filter: filter, container: results_container});
            });
            Pachno.Main.Helpers.ajax(url, {
                params: parameters,
                loading: {
                    callback: function () {
                        filters_containers.each(function (details) {
                            details['container'].addClassName('updating');
                        });
                    }
                },
                success: {
                    callback: function (json) {
                        filters_containers.each(function (details) {
                            details['container'].update(json.results[details['filter'].dataset.filterkey]);
                            // window.setTimeout(function () {
                            //     var si = details['filter'].down('input[type=search]');
                            //     if (si != undefined) {
                            //         si.dataset.previousValue = '';
                            //         Pachno.Search.filterFilterOptionsElement(si);
                            //     }
                            // }, 250);
                            details['container'].removeClassName('updating');
                        });
                    }
                }
            });
        };

        Pachno.Search.sortResults = function (event) {
            if (this.dataset.sortField !== undefined) {
                var direction = (this.dataset.sortDirection == 'asc') ? 'desc' : 'asc';
                $('search_sortfields_input').setValue(this.dataset.sortField + '=' + direction);
                Pachno.Search.liveUpdate(true);
            }
        };

        Pachno.Search.download = function (format) {
            var fif = $('find_issues_form');
            var parameters = fif.serialize();
            window.location = fif.dataset.historyUrl + '?' + parameters + '&format=' + format;
        };

        Pachno.Search.updateSavedSearchCounts = function () {
            var search_ids = '',
                searchitems = $$('.savedsearch-item'),
                project_id = ($('project-menu')) ? $('project-menu').dataset.projectId : 0;

            searchitems.each(function (searchitem) {
                search_ids += '&search_ids[]='+$(searchitem).dataset.searchId;
            });
            Pachno.Main.Helpers.ajax($('main_container').dataset.url, {
                url_method: 'get',
                params: '&say=getsearchcounts&project_id='+project_id+search_ids,
                success: {
                    callback: function (json) {
                        searchitems.each(function (searchitem) {
                            var badge = $(searchitem).down('.count-badge');
                            if (badge !== undefined) {
                                badge.update(json[$(searchitem).dataset.searchId]);
                            }
                        });
                    }
                }
            });
        };

        Pachno.Search.liveUpdate = function (force) {
            var fif = $('find_issues_form');
            if (!fif) {
                return;
            }
            var url = fif.action;
            var parameters = fif.serialize();

            var results_loaded = (fif.dataset.resultsLoaded != undefined && fif.dataset.resultsLoaded != '');

            if (force == true || results_loaded) {
                jQuery('nav.sidebar').addClass('collapsed');
                Pachno.Main.Helpers.ajax(url, {
                    params: parameters,
                    loading: {
                        indicator: 'search_results_loading_indicator',
                        callback: function () {
                            if (history.pushState) {
                                history.pushState({caller: 'liveUpdate'}, '', fif.dataset.historyUrl + '?' + parameters);
                            }
                        }
                    },
                    success: {update: 'search-results'},
                    complete: {
                        callback: function (json) {
                            if (!results_loaded) {
                                Pachno.Search.updateSavedSearchCounts();
                            }
                            $('findissues_num_results_span').update(json.num_issues);
                            if (! $('findissues_search_title').visible() && ! $('findissues_search_generictitle').visible()) {
                                $('findissues_search_generictitle').show();
                            }
                            $('findissues_num_results').show();
                            $('interactive_save_button').show();
                            fif.dataset.resultsLoaded = true;
                            fif.dataset.isSaved = undefined;
                            $('search-results').select('th').each(function (header_elm) {
                                if (!header_elm.hasClassName('nosort')) {
                                    header_elm.on('click', Pachno.Search.sortResults);
                                }
                            });
                            if (Pachno.Search.isDirty()) {
                                Pachno.Search.loadDynamicChoices();
                                Pachno.Search.clearDirty();
                            }
                        }
                    }
                });
            }
        };

        Pachno.Search.setIssuesPerPage = function (value) {
            var fip_value = $('filter_issues_per_page');
            fip_value.setValue(parseInt(value));
            Pachno.Search.liveUpdate();
        };

        Pachno.Search.initializeIssuesPerPageSlider = function () {
            var $ipp_slider = jQuery('#issues-per-page-slider');
            if (!$ipp_slider.data('initialized')) {
                var filter_ipp_value = jQuery('filter_issues_per_page');
                var step_start = 1;
                switch (parseInt(filter_ipp_value.val())) {
                    case 25:
                        step_start = 1;
                        break;
                    case 50:
                        step_start = 2;
                        break;
                    case 100:
                        step_start = 3;
                        break;
                    case 250:
                        step_start = 4;
                        break;
                    case 500:
                        step_start = 5;
                        break;
                }

                jQuery('#issues-per-page-slider').slider();
                // new Control.Slider('issues_per_page_handle', ipp_slider, {
                //     range: $R(1, 5),
                //     values: [1, 2, 3, 4, 5],
                //     sliderValue: step_start,
                //     onSlide: function (step) {
                //         Pachno.Search.moveIssuesPerPageSlider(step);
                //     },
                //     onChange: function (step) {
                //         var value = Pachno.Search.moveIssuesPerPageSlider(step);
                //         Pachno.Search.setIssuesPerPage(value);
                //     }
                // });
                $ipp_slider.data('initialized', true);
            }
        };

        Pachno.Search.setFilterValue = function (element, checked) {
            if (element) {
                if (element.hasClassName('separator'))
                    return;
                if (checked) {
                    element.addClassName('selected');
                    element.down('input').checked = true;
                } else {
                    element.removeClassName('selected');
                    element.down('input').checked = false;
                }
            } else {
                console.error(element, 'not an element');
            }
        };

        Pachno.Search.setFilterSelectionGroupSelections = function (element) {
            var current_element = element;
            if (element.dataset.exclusive !== undefined) {
                element.up('.interactive_menu_values').childElements().each(function (filter_element) {
                    if (filter_element.hasClassName('filtervalue')) {
                        if ((element.dataset.excludeGroup !== undefined && filter_element.dataset.selectionGroup == element.dataset.excludeGroup) ||
                            element.dataset.selectionGroup == filter_element.dataset.selectionGroup) {
                            if (filter_element.dataset.value != current_element.dataset.value)
                                Pachno.Search.setFilterValue(filter_element, false);
                        }
                    }
                });
            }
            else if (element.dataset.excludeGroup !== undefined) {
                element.up('.interactive_menu_values').childElements().each(function (filter_element) {
                    if (filter_element.hasClassName('filtervalue')) {
                        if (filter_element.dataset.selectionGroup != current_element.dataset.selectionGroup)
                            Pachno.Search.setFilterValue(filter_element, false);
                    }
                });
            }
        };

        Pachno.Search.setInteractiveDate = function (element) {
            var f_element = element.up('.filter');
            Pachno.Search.calculateFilterDetails(f_element);
            element.dataset.dirty = 'dirty';
            Pachno.Search.liveUpdate(true);
        };

        Pachno.Search.saveSearch = function () {
            var fif = $('find_issues_form');
            var find_parameters = fif.serialize();
            var ssf = $('save_search_form');
            var p = find_parameters + '&' + ssf.serialize();

            var button = ssf.down('input[type=submit]');
            Pachno.Main.Helpers.ajax(ssf.action, {
                params: p,
                loading: {
                    indicator: 'save_search_indicator',
                    callback: function () {
                        button.disable();
                    }
                },
                complete: {
                    callback: function () {
                        button.enable();
                    }
                }
            });
        };

        Pachno.Search.calculateFilterDetails = function (filter) {
            var string = '';
            var value_string = '';
            var selected_elements = [];
            var selected_values = [];
            filter.select('input[type=checkbox]').each(function (element) {
                if (element.checked) {
                    selected_elements.push(element.dataset.text);
                    if (element.up('.filtervalue').dataset.operator == undefined) {
                        selected_values.push(element.getValue());
                    } else {
                        if (jQuery('.filter_' + filter.dataset.filterkey + '_operator_input', filter).length) {
                            jQuery('.filter_' + filter.dataset.filterkey + '_operator_input', filter).val(element.getValue());
                        }
                        else {
                            $('filter_' + filter.dataset.filterkey + '_operator_input').setValue(element.getValue());
                        }
                    }
                }
            });
            if (selected_elements.size() > 0) {
                string = selected_elements.join(', ');
                value_string = selected_values.join(',');
            } else {
                string = filter.dataset.allValue;
            }
            if (filter.dataset.isdate !== undefined) {
                if (jQuery('.filter_' + filter.dataset.filterkey + '_value_input', filter).length) {
                    selected_elements.push(jQuery('.filter_' + filter.dataset.filterkey + '_value_input', filter).attr('data-display-value'));
                }
                else {
                    selected_elements.push($('filter_' + filter.dataset.filterkey + '_value_input').dataset.displayValue);
                }
                string = selected_elements.join(' ');
            }
            if (filter.dataset.istext !== undefined) {
                if (jQuery('.filter_' + filter.dataset.filterkey + '_value_input', filter).length) {
                    string = jQuery('.filter_' + filter.dataset.filterkey + '_value_input', filter).val();
                }
                else {
                    string = $('filter_' + filter.dataset.filterkey + '_value_input').getValue();
                }
            }
            Pachno.Search.updateFilterVisibleValue(filter, string);
            if (filter.dataset.isdate === undefined && filter.dataset.istext === undefined) {
                if (jQuery('.filter_' + filter.dataset.filterkey + '_value_input', filter).length) {
                    jQuery('.filter_' + filter.dataset.filterkey + '_value_input', filter).val(value_string);
                }
                else {
                    $('filter_' + filter.dataset.filterkey + '_value_input').setValue(value_string);
                }
            }
        };

        Pachno.Search.updateFilterVisibleValue = function (filter, value) {
            if (value.length > 23) {
                value = value.substr(0, 20) + '...';
            }
            filter.down('.value').update(value);
        };

        Pachno.Search.initializeKeyboardNavigation = function () {
            Event.observe(document, 'keydown', function (event) {
                if (['INPUT', 'TEXTAREA'].indexOf(event.target.nodeName) != -1)
                    return;
                if (Event.KEY_DOWN == event.keyCode) {
                    Pachno.Search.moveDown(event);
                }
                else if (Event.KEY_PAGEDOWN == event.keyCode) {
                    for (var cc = 1; cc <= 5; cc++) {
                        Pachno.Search.moveDown(event);
                    }
                }
                else if (Event.KEY_UP == event.keyCode) {
                    Pachno.Search.moveUp(event);
                }
                else if (Event.KEY_PAGEUP == event.keyCode) {
                    for (var cc = 1; cc <= 5; cc++) {
                        Pachno.Search.moveUp(event);
                    }
                }
                else if (Event.KEY_RETURN == event.keyCode) {
                    Pachno.Search.moveTo(event);
                }
            });
            $('search-results').select('tr').each(function (element) {
                element.observe('click', function (event) {
                    var selected_elements = $('search-results').select('tr.selected');
                    var old_selected_element = (selected_elements.size() == 0) ? undefined : selected_elements[selected_elements.size() - 1];
                    Pachno.Search.move(old_selected_element, this, null, false);
                });
            });
        };

        /*
         Simple OpenID Plugin
         http://code.google.com/p/openid-selector/

         This code is licensed under the New BSD License.
         */

        Pachno.Chart.config = {
            y_config: {color: '#AAA', min: 0, tickDecimals: 0},
            x_config: {color: '#AAA', tickDecimals: 0},
            grid_config: {
                color: '#CCC',
                borderWidth: 1,
                backgroundColor: {colors: ["#FFF", "#EEE"]},
                hoverable: true,
                autoHighlight: true
            }
        };

        Pachno.OpenID = {
            version: '1.3', // version constant
            demo: false,
            demo_text: null,
            cookie_expires: 6 * 30, // 6 months.
            cookie_name: 'openid_provider',
            cookie_path: '/',
            img_path: 'images/',
            locale: 'en', // is set in openid-<locale>.js
            sprite: 'en', // usually equals to locale, is set in
            // openid-<locale>.js
            signin_text: null, // text on submit button on the form
            all_small: false, // output large providers w/ small icons
            image_title: '%openid_provider_name', // for image title

            input_id: 'openid_identifier',
            provider_url: null,
            provider_id: null,
            providers_small: null,
            providers_large: null,
            /**
             * Class constructor
             *
             * @return {Void}
             */
            init: function () {
                var openid_btns = $('openid_btns');
                if ($('openid_choice')) {
                    $('openid_choice').setStyle({
                        display: 'block'
                    });
                }
                if ($('openid_input_area')) {
                    $('openid_input_area').innerHTML = "";
                }
                var i = 0;
                // add box for each provider
                for (id in this.providers_large) {
                    box = this.getBoxHTML(id, this.providers_large[id], (this.all_small ? 'small' : 'large'), i++);
                    openid_btns.insert(box);
                }
                if (this.providers_small) {
                    openid_btns.insert('<br/>');
                    for (id in this.providers_small) {
                        box = this.getBoxHTML(id, this.providers_small[id], 'small', i++);
                        openid_btns.insert(box);
                    }
                }
    //		$('openid_form').submit = this.submit;
    //		var box_id = this.readCookie();
    //		if (box_id) {
    //			this.signin(box_id, true);
    //		}
            },
            /**
             * @return {String}
             */
            getBoxHTML: function (box_id, provider, box_size, index) {
                var image_ext = box_size == 'small' ? '.ico.png' : '.png';
                return '<a title="' + this.image_title.replace('%openid_provider_name', provider["name"]) + '" href="javascript:Pachno.OpenID.signin(\'' + box_id + '\');"'
                    + 'class="' + box_id + ' openid_' + box_size + '_btn button"><img src="' + Pachno.basepath + 'images/openid_providers.' + box_size + '/' + box_id + image_ext + '"></a>';
            },
            /**
             * Provider image click
             *
             * @return {Void}
             */
            signin: function (box_id) {
                var provider = (this.providers_large[box_id]) ? this.providers_large[box_id] : this.providers_small[box_id];
                if (!provider) {
                    return;
                }
                this.highlight(box_id);
                this.provider_id = box_id;
                this.provider_url = provider['url'];
                // prompt user for input?
                if (provider['label']) {
                    this.useInputBox(provider);
                } else {
                    $('openid_input_area').innerHTML = '';
                    this.submit();
                    $('openid_form').submit();
                }
            },
            /**
             * Sign-in button click
             *
             * @return {Boolean}
             */
            submit: function () {
                var url = this.provider_url;
                var username_field = $('openid_username');
                var username = username_field ? $('openid_username').getValue() : '';
                if (url) {
                    url = url.replace('{username}', username);
                    this.setOpenIdUrl(url);
                }
                return true;
            },
            /**
             * @return {Void}
             */
            setOpenIdUrl: function (url) {
                var hidden = document.getElementById(this.input_id);
                if (hidden != null) {
                    hidden.value = url;
                } else {
                    $('openid_form').insert('<input type="hidden" id="' + this.input_id + '" name="' + this.input_id + '" value="' + url + '"/>');
                }
            },
            /**
             * @return {Void}
             */
            highlight: function (box_id) {
                // remove previous highlight.
                var highlight = $$('.openid_highlight');
                if (highlight[0]) {
                    highlight[0].removeClassName('button-pressed');
                    highlight[0].removeClassName('openid_highlight');
                }
                // add new highlight.
                var box = $$('.' + box_id)[0];
                box.addClassName('openid_highlight');
                box.addClassName('button-pressed');
            },
            setCookie: function (value) {
                var date = new Date();
                date.setTime(date.getTime() + (this.cookie_expires * 24 * 60 * 60 * 1000));
                var expires = "; expires=" + date.toGMTString();
                document.cookie = this.cookie_name + "=" + value + expires + "; path=" + this.cookie_path;
            },
            readCookie: function () {
                var nameEQ = this.cookie_name + "=";
                var ca = document.cookie.split(';');
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ')
                        c = c.substring(1, c.length);
                    if (c.indexOf(nameEQ) == 0)
                        return c.substring(nameEQ.length, c.length);
                }
                return null;
            },
            /**
             * @return {Void}
             */
            useInputBox: function (provider) {
                var input_area = $('openid_input_area');
                var html = '';
                var id = 'openid_username';
                var value = '';
                var label = provider['label'];
                var style = '';
                if (provider['name'] == 'OpenID') {
                    id = this.input_id;
                    value = 'http://';
                    style = 'background: #FFF url(' + Pachno.basepath + 'images/openid-inputicon.gif) no-repeat scroll 0 50%; padding-left:18px;';
                }
                html = '<input id="' + id + '" type="text" style="' + style + '" name="' + id + '" value="' + value + '" />';
                if (label) {
                    html += '<label for="' + id + '">' + label + '</label>';
                }
                input_area.innerHTML = html;
                $('openid_submit_button').show();

    //		$('openid_submit').onclick = this.submit;
                $(id).focus();
            },
            setDemoMode: function (demoMode) {
                this.demo = demoMode;
            }
        };

        Pachno.Tutorial.highlightArea = function (top, left, width, height, blocked, seethrough) {
            var backdrop_class = (seethrough != undefined && seethrough == true) ? 'seethrough' : '';
            var d1 = '<div class="fullpage_backdrop ' + backdrop_class + ' tutorial" style="top: 0; left: 0; width: ' + left + 'px;"></div>';
            var d2_width = Pachno.Core._vp_width - left - width;
            var d2 = '<div class="fullpage_backdrop ' + backdrop_class + ' tutorial" style="top: 0; left: ' + (left + width) + 'px; width: ' + d2_width + 'px;"></div>';
            var d3 = '<div class="fullpage_backdrop ' + backdrop_class + ' tutorial" style="top: 0; left: ' + left + 'px; width: ' + width + 'px; height: ' + top + 'px"></div>';
            var vp_height = document.viewport.getHeight();
            var d4_height = vp_height - top - height;
            var d4 = '<div class="fullpage_backdrop ' + backdrop_class + ' tutorial" style="top: ' + (top + height) + 'px; left: ' + left + 'px; width: ' + width + 'px; height: ' + d4_height + 'px"></div>';
            var mc = $('main_container');
            if (blocked == true) {
                var d_overlay = '<div class="tutorial block_overlay" style="top: ' + top + 'px; left: ' + left + 'px; width: ' + width + 'px; height: ' + height + 'px;"></div>';
                mc.insert(d_overlay);
            }
            mc.insert(d1);
            mc.insert(d2);
            mc.insert(d3);
            mc.insert(d4);
            Pachno.Tutorial.positionMessage(top, left, width, height);
        };
        Pachno.Tutorial.highlightElement = function (element, blocked, seethrough) {
            element = $(element);
            var el = element.getLayout();
            var os = element.cumulativeOffset();
            var width = el.get('width') + el.get('padding-left') + el.get('padding-right');
            var height = el.get('height') + el.get('padding-top') + el.get('padding-bottom');
            Pachno.Tutorial.highlightArea(os.top, os.left, width, height, blocked, seethrough);
        };
        Pachno.Tutorial.positionMessage = function (top, left, width, height) {
            var tm = $('tutorial-message');
            ['above', 'below', 'left', 'right'].each(function (pos) {
                tm.removeClassName(pos);
            });
            if (top + left + width + height == 0) {
                tm.addClassName('full');
                tm.setStyle({top: '', left: ''});
            } else {
                tm.removeClassName('full');
                var step = parseInt(tm.dataset.tutorialStep);
                var key = tm.dataset.tutorialKey;
                var td = Pachno.Tutorial.Stories[key][step];
                tm.addClassName(td.messagePosition);
                var tl = tm.getLayout();
                var twidth = tl.get('width') + tl.get('padding-left') + tl.get('padding-right');
                switch (td.messagePosition) {
                    case 'right':
                        var tl = tm.getLayout();
                        var th = tl.get('height') + tl.get('padding-top') + tl.get('padding-bottom');
                        tm.setStyle({top: (top - parseInt(th / 2)) + 'px', left: (left + width + 15) + 'px'});
                        break;
                    case 'left':
                        var tl = tm.getLayout();
                        var width = tl.get('width') + tl.get('padding-left') + tl.get('padding-right');
                        var th = tl.get('height') + tl.get('padding-top') + tl.get('padding-bottom');
                        tm.setStyle({top: (top - parseInt(th / 2)) + 'px', left: (left - width - 15) + 'px'});
                        break;
                    case 'below':
                        tm.setStyle({top: (top + height + 15) + 'px', left: ((left - parseInt(twidth / 2)) + width / 2) + 'px'});
                        break;
                    case 'above':
                        var tl = tm.getLayout();
                        var th = tl.get('height') + tl.get('padding-top') + tl.get('padding-bottom');
                        tm.setStyle({top: (top - th - 15) + 'px', left: ((left - parseInt(twidth / 2)) + width / 2) + 'px'});
                        break;
                    case 'center':
                        var tl = tm.getLayout();
                        var th = tl.get('height') + tl.get('padding-top') + tl.get('padding-bottom');
                        tm.setStyle({top: (top + (height / 2) - (th / 2)) + 'px', left: ((left - parseInt(twidth / 2)) + width / 2) + 'px'});
                        break;
                }
            }
            tm.show();
        };
        Pachno.Tutorial.resetHighlight = function () {
            $$('.tutorial').each(Element.remove);
        };
        Pachno.Tutorial.disable = function () {
            var tm = $('tutorial-message');
            var key = tm.dataset.tutorialKey;
            var url = tm.dataset.disableUrl;
            Pachno.Main.Helpers.ajax(url, {
                params: '&key=' + key
            });
            $('tutorial-next-button').stopObserving('click');
            Pachno.Tutorial.resetHighlight();
            $('tutorial-message').hide();
        };
        Pachno.Tutorial.playNextStep = function () {
            Pachno.Tutorial.resetHighlight();
            var tm = $('tutorial-message');
            tm.hide();
            var step = parseInt(tm.dataset.tutorialStep);
            var key = tm.dataset.tutorialKey;
            step++;
            $('tutorial-current-step').update(step);
            tm.dataset.tutorialStep = step;
            var tutorialData = Pachno.Tutorial.Stories[key][step];
            if (tutorialData != undefined) {
                if (tutorialData.cb) {
                    tutorialData.cb(tutorialData);
                }
                $('tutorial-message-container').update(tutorialData.message);
                var tbn = tm.down('.tutorial-buttons').down('.button-next');
                var tb = tm.down('.tutorial-buttons').down('.button-disable');
                if (tutorialData.button != undefined) {
                    tbn.update(tutorialData.button);
                    tbn.show();
                    if (step > 1) {
                        tb.hide();
                    } else {
                        tb.show();
                    }
                } else {
                    tbn.hide();
                    tb.hide();
                }
                ['small', 'medium', 'large'].each(function (cn) {
                    tm.removeClassName(cn);
                });
                tm.addClassName(tutorialData.messageSize);
                if (tutorialData.highlight != undefined) {
                    var tdh = tutorialData.highlight;
                    var timeout = (tdh.delay) ? tdh.delay : 50;
                    window.setTimeout(function () {
                        tm.show();
                        if (tdh.element != undefined) {
                            var seethrough = (tdh.seethrough != undefined) ? tdh.seethrough : false;
                            Pachno.Tutorial.highlightElement(tdh.element, tdh.blocked, seethrough);
                        } else {
                            Pachno.Tutorial.highlightArea(tdh.top, tdh.left, tdh.width, tdh.height, tdh.blocked);
                        }
                    }, timeout);
                } else {
                    Pachno.Tutorial.highlightArea(0, 0, 0, 0, true);
                }
            } else {
                Pachno.Tutorial.disable();
            }
        };
        Pachno.Tutorial.start = function (key, initial_step) {
            var tutorial = Pachno.Tutorial.Stories[key];
            var ts = 0;
            var is = (initial_step != undefined) ? (initial_step - 1) : 0;
            for (var d in tutorial) {
                ts++;
            }
            var tm = $('tutorial-message');
            tm.dataset.tutorialKey = key;
            tm.dataset.tutorialStep = is;
            tm.dataset.tutorialSteps = ts;
            $('tutorial-total-steps').update(ts);
            $('tutorial-next-button').stopObserving('click');
            $('tutorial-next-button').observe('click', Pachno.Tutorial.playNextStep);
            Pachno.Tutorial.playNextStep();
        };

        Pachno.Main.Helpers.toggler = function (elm) {
            if (elm.data('target')) {
                $(elm.data('target')).toggleClassName('force-active');
            } else {
                elm.toggleClass("active");
            }
        };

        Pachno.Main.loadParentArticles = function (form) {
            Pachno.Main.Helpers.ajax(form.action, {
                params: $(form).serialize(),
                loading: {
                    indicator: 'parent_selector_container_indicator',
                },
                complete: {
                    callback: function (json) {
                        $('parent_articles_list').update(json.list);
                    }
                }
            });
        };

        Pachno.Main.Notifications.markAllRead = function () {
            Pachno.Main.Helpers.ajax(Pachno.data_url, {
                url_method: 'post',
                params: '&say=notificationsread',
                loading: {
                    callback: function () {
                        $('user_notifications').addClassName('toggling');
                    }
                },
                success: {
                    callback: function (json) {
                        var un = $('user_notifications');
                        un.select('li').each(function (li) {
                            li.removeClassName('unread');
                            li.addClassName('read');
                        });
                        Pachno.Core.Pollers.Callbacks.dataPoller();
                    }
                }
            });
        };

        Pachno.Main.Notifications.toggleRead = function (notification_id) {
            Pachno.Main.Helpers.ajax(Pachno.data_url, {
                url_method: 'post',
                params: '&say=notificationstatus&notification_id=' + notification_id,
                loading: {
                    callback: function () {
                        $('notification_' + notification_id + '_container').addClassName('toggling');
                    }
                },
                success: {
                    callback: function (json) {
                        var nc = $('notification_' + notification_id + '_container');
                        ['toggling', 'read', 'unread'].each(function (cn) {
                            nc.toggleClassName(cn);
                        });
                        Pachno.Core.Pollers.Callbacks.dataPoller(notification_id);
                    }
                }
            });
        };

        Pachno.Main.Notifications.loadMore = function (event, loadToTop) {
            var loadToTop = loadToTop || false;
            if (Pachno.Main.Notifications.loadingLocked !== true || loadToTop) {
                if (! loadToTop) Pachno.Main.Notifications.loadingLocked = true;
                var unl = $('user_notifications_list'),
                    unl_data = unl.dataset;
                if (unl) {
                    if (loadToTop && unl.down('li') != undefined) {
                        var url = unl_data.notificationsUrl+'&first_notification_id='+unl.down('li:not(.disabled)').dataset.notificationId;
                    }
                    else if (! loadToTop && unl.select("li:not(.disabled):last-child") != undefined && unl.select("li:not(.disabled):last-child")[0] != undefined) {
                        var url = unl_data.notificationsUrl+'&last_notification_id='+unl.select("li:not(.disabled):last-child")[0].dataset.notificationId;
                    }
                    if (url != undefined) {
                        Pachno.Main.Helpers.ajax(url, {
                            url_method: 'get',
                            loading: {
                                indicator: 'user_notifications_loading_indicator'
                            },
                            success: {
                                update: { element: '', insertion: true },
                                callback: function (json) {
                                    if (loadToTop) {
                                        if (jQuery('.faded_out', unl).length) {
                                            unl.update(json.content);
                                        }
                                        else {
                                            unl.insert({top: json.content});
                                        }
                                    }
                                    else {
                                        if (jQuery('.faded_out', unl).length) {
                                            unl.update(json.content);
                                        }
                                        else {
                                            unl.insert({bottom: json.content});
                                        }
                                    }
                                    if ($('user_notifications_list_wrapper_nano')) jQuery("#user_notifications_list_wrapper_nano").nanoScroller();
                                    if (! loadToTop) Pachno.Main.Notifications.loadingLocked = false;
                                }
                            },
                            exception: {
                                callback: function () {
                                    if (! loadToTop) Pachno.Main.Notifications.loadingLocked = false;
                                }
                            }
                        });
                    }
                }
            }
        }

        Pachno.Main.Notifications.Web.GrantPermissionOrSendTest = function (title, body, icon) {
            if (!Notify.needsPermission) {
                Pachno.Main.Notifications.Web.Send(title, body, 'test', icon);
            } else if (Notify.isSupported()) {
                Notify.requestPermission();
            }
        }

        Pachno.Main.Notifications.Web.Send = function (title, body, tag, icon, click_callback) {
            if (Notify.needsPermission) return;

            new Notify(title, {
                body: body,
                tag: tag,
                icon: icon,
                timeout: 8,
                closeOnClick: true,
                notifyClick: click_callback
            }).show();
        }

        Pachno.Main.initializeMentionable = function (textarea) {
            if ($(textarea).hasClassName('mentionable') && !$(textarea).hasClassName('mentionable-initialized')) {
                Pachno.Main.Helpers.ajax(Pachno.data_url, {
                    url_method: 'get',
                    params: 'say=get_mentionables&target_type=' + $(textarea).dataset.targetType + '&target_id=' + $(textarea).dataset.targetId,
                    success: {
                        callback: function (json) {
                            jQuery('#' + textarea.id).mention({
                                delimiter: '@',
                                sensitive: true,
                                emptyQuery: true,
                                queryBy: ['name', 'username'],
                                typeaheadOpts: {
                                    items: 10 // Max number of items you want to show
                                },
                                users: json.mentionables
                            });
                            $(textarea).addClassName('mentionable-initialized');
                        }
                    }
                });
            }
            ;
        };

        Pachno.Main.Helpers.loadDynamicMenu = function (menu) {
            var url = $(menu).dataset.menuUrl;
            Pachno.Main.Helpers.ajax(url, {
                url_method: 'get',
                success: {
                    callback: function (json) {
                        $(menu).replace(json.menu);
                    }
                }
            });
        };

        Pachno.Main.Helpers.setFancyFilterSelectionGroupSelections = function (element) {
            var current_element = element;
            if (element.dataset.exclusive !== undefined) {
                element.up('.interactive_menu_values').childElements().each(function (filter_element) {
                    if (filter_element.hasClassName('filtervalue')) {
                        if ((element.dataset.excludeGroup !== undefined && filter_element.dataset.selectionGroup == element.dataset.excludeGroup) ||
                            element.dataset.selectionGroup == filter_element.dataset.selectionGroup) {
                            if (filter_element.dataset.value != current_element.dataset.value)
                                Pachno.Main.Helpers.setFancyFilterValue(filter_element, false);
                        }
                    }
                });
            }
            else if (element.dataset.excludeGroup !== undefined) {
                element.up('.interactive_menu_values').childElements().each(function (filter_element) {
                    if (filter_element.hasClassName('filtervalue')) {
                        if (filter_element.dataset.selectionGroup != current_element.dataset.selectionGroup)
                            Pachno.Main.Helpers.setFancyFilterValue(filter_element, false);
                    }
                });
            }
            if (element.up('.fancyfilter').dataset.exclusivityGroup !== undefined) {
                var egroup = element.up('.fancyfilter').dataset.exclusivityGroup;
                $$('.interactive_menu_values').each(function (value_list) {
                    if (value_list.up('.fancyfilter').dataset.exclusivityGroup !== undefined && value_list.up('.fancyfilter').dataset.exclusivityGroup === egroup) {
                        value_list.childElements('.filtervalue').each(function (filtervalue) {
                            if ($(filtervalue).dataset.value === element.dataset.value) {
                                if ($(filtervalue) !== element) {
                                    if (element.hasClassName('selected')) {
                                        $(filtervalue).addClassName('disabled');
                                    } else {
                                        $(filtervalue).removeClassName('disabled');
                                    }
                                }
                            }
                        })
                    }
                });
            }
        };

        Pachno.Main.Helpers.recalculateFancyFilters = function(filter) {
            if (filter != undefined) {
                $$('.filter').each(Pachno.Main.Helpers.calculateFancyFilterDetails);
            }
            else {
                Pachno.Main.Helpers.calculateFancyFilterDetails(filter);
            }
        };

        Pachno.Main.Helpers.toggleFancyFilterValueElement = function (element, checked) {
            if (checked == undefined) {
                if (element.down('input').checked) {
                    Pachno.Main.Helpers.setFancyFilterValue(element, false);
                } else {
                    Pachno.Main.Helpers.setFancyFilterValue(element, true);
                }
            } else {
                Pachno.Main.Helpers.setFancyFilterValue(element, checked);
            }
            Pachno.Main.Helpers.setFancyFilterSelectionGroupSelections(element);
            var f_element = element.up('.filter');
            Pachno.Main.Helpers.calculateFancyFilterDetails(f_element);
            if (element.dataset.exclusive !== undefined) Pachno.Main.Helpers.toggleFancyFilterElement(f_element);
        };

        Pachno.Main.Helpers.updateFancyFilterVisibleValue = function (filter, value) {
            filter.down('.value').update(value);
        };

        Pachno.Main.Helpers.initializeColorPicker = function () {
            jQuery('input.color').each(function (index, element) {
                var input = jQuery(element);
                input.spectrum({
                    cancelText: input.data('cancel-text'),
                    chooseText: input.data('choose-text'),
                    showInput: true,
                    preferredFormat: 'hex'
                });
            });
        };

        Pachno.Core.getPluginUpdates = function (type) {
            var params = '',
                plugins = $('installed-'+type+'s-list').childElements();
            plugins.each(function (plugin) {
                if (type == 'theme' || !plugin.hasClassName('disabled')) {
                    params += '&addons[]=' + plugin.dataset[type+'Key'];
                }
            });
            Pachno.Main.Helpers.ajax($('main_container').dataset.url, {
                url_method: 'get',
                params: 'say=get_'+type+'_updates' + params,
                loading: {
                    indicator: 'installed_'+type+'s_indicator'
                },
                success: {
                    update: 'installed_'+type+'s_indicator',
                    callback: function (json) {
                        plugins.each(function (plugin) {
                            if (json[plugin.dataset[type+'Key']] !== undefined) {
                                if (plugin.dataset.version != json[plugin.dataset[type+'Key']].version) {
                                    plugin.addClassName('can-update');
                                    var link = $(type + '_'+plugin.dataset[type+'Key']+'_download_location');
                                    link.setAttribute('href', json[plugin.dataset[type+'Key']].download);
                                    jQuery('body').on('click', '.update-'+type+'-menu-item', function (e) {
                                        var pluginbox = jQuery(this).parents('li.'+type);
                                        $('update_'+type+'_help_' + pluginbox.data('id')).show();
                                        if (!Pachno.Core.Pollers.pluginupdatepoller)
                                            Pachno.Core.Pollers.pluginupdatepoller = new PeriodicalExecuter(Pachno.Core.validatePluginUpdateUploadedPoller(type, pluginbox.data('module-key')), 5);
                                    });
                                }
                            }
                        })
                    }
                },
                failure: {
                    callback: function (response) {
                    }
                }
            });
        };

        Pachno.Core.cancelManualUpdatePoller = function () {
            Pachno.Core.Pollers.Locks.pluginupdatepoller = false;
            if (Pachno.Core.Pollers.pluginupdatepoller) {
                Pachno.Core.Pollers.pluginupdatepoller.stop();
                Pachno.Core.Pollers.pluginupdatepoller = undefined;
            }
        };

        Pachno.Core.validatePluginUpdateUploadedPoller = function (type, pluginkey) {
            return function () {
                if (!Pachno.Core.Pollers.Locks.pluginupdatepoller) {
                    Pachno.Core.Pollers.Locks.pluginupdatepoller = true;
                    Pachno.Main.Helpers.ajax($('main_container').dataset.url, {
                        url_method: 'get',
                        params: '&say=verify_'+type+'_update_file&'+type+'_key='+pluginkey,
                        success: {
                            callback: function (json) {
                                if (json.verified == '1') {
                                    jQuery('#'+type+'_'+pluginkey+'_perform_update').children('input[type=submit]').prop('disabled', false);
                                    Pachno.Core.cancelManualUpdatePoller();
                                }
                                Pachno.Core.Pollers.Locks.pluginupdatepoller = false;
                            }
                        },
                        exception: {
                            callback: function () {
                                Pachno.Core.Pollers.Locks.pluginupdatepoller = false;
                            }
                        }
                    });
                }
            }
        };

        Pachno.Core.getAvailablePlugins = function (type, callback) {
            Pachno.Main.Helpers.ajax($('main_container').dataset.url, {
                url_method: 'get',
                params: '&say=get_'+type,
                loading: {
                    indicator: 'available_'+type+'_loading_indicator'
                },
                success: {
                    update: 'available_'+type+'_container',
                    callback: function () {
                        jQuery('body').on('click', '.install-button', callback);
                    }
                }
            });
        };

        Pachno.Core.installPlugin = function (button, type) {
            button = jQuery(button);
            button.addClass('installing');
            button.prop('disabled', true);
            Pachno.Main.Helpers.ajax($('main_container').dataset.url, {
                url_method: 'post',
                params: '&say=install-'+type+'&'+type+'_key='+button.data('key'),
                success: {
                    callback: function (json) {
                        if (json.installed) {
                            $('online-'+type+'-' + json[type+'_key']).addClassName('installed');
                            $('installed-'+type+'s-list').insert(json[type], 'after');
                        }
                    }
                },
                failure: {
                    callback: function () {
                        button.removeClass('installing');
                        button.prop('disabled', false);
                    }
                }
            });
        };

        Pachno.Modules.getModuleUpdates = function () {
            Pachno.Core.getPluginUpdates('module');
        };

        Pachno.Modules.getAvailableOnline = function () {
            Pachno.Core.getAvailablePlugins('modules', Pachno.Modules.install);
        };

        Pachno.Modules.install = function (event) {
            Pachno.Core.installPlugin(this, 'module');
        };

        Pachno.Themes.getThemeUpdates = function () {
            Pachno.Core.getPluginUpdates('theme');
        };

        Pachno.Themes.getAvailableOnline = function () {
            Pachno.Core.getAvailablePlugins('themes', Pachno.Themes.install);
        };

        Pachno.Themes.install = function (event) {
            Pachno.Core.installPlugin(this, 'theme');
        };

        return Pachno;
});
