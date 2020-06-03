define(['pachno/tools', 'pachno/index', 'domReady', 'jquery', 'mention'],
    function (tools, Pachno, domReady, jQuery, mention) {

        domReady(function () {
            Pachno.Helpers.MarkitUp(jQuery('textarea.markuppable'));
            (function ($) {
                // jQuery('body').on('click', '.expandable .expander', function (event) {
                //     event.preventDefault();
                //
                //     jQuery(this).closest('.expandable').toggleClass('expanded');
                // });
                //
                // jQuery('body').on('click', '.sidebar .collapser a', function (event) {
                //     event.stopPropagation();
                //     event.preventDefault();
                //
                //     jQuery(this).closest('.sidebar').toggleClass('collapsed');
                // });

                jQuery('body').on('blur', 'form[data-interactive-form] input, form[data-interactive-form] textarea', function () {
                    console.log(this);
                    const $form = jQuery(this).parents('form');

                    $form.submit();
                });

                jQuery('body').on('change', 'input[data-interactive-toggle]', function () {
                    const $input = jQuery(this),
                        value = $input.is(':checked') ? '1' : '0';

                    if ($input.hasClass('submitting')) return;

                    $input.addClass('submitting');
                    $input.attr('disabled', true);

                    let data = new FormData();
                    data.append('value', value);

                    fetch($input.data('url'), {
                        method: 'POST',
                        body: data
                    })
                        .then(function(response) {
                            $input.removeClass('submitting');
                            $input.attr('disabled', false);
                            // response.json().then(resolve);
                            // res = response;
                            // console.log(response);
                            // resolve($form, res);
                            // response.json()
                            //     .then(function (json) {
                            //     });
                        })
                });

                jQuery('body').on('change', 'form[data-interactive-form] input[type=checkbox],form[data-interactive-form] input[type=radio]', function () {
                    console.log('CHAINGING');
                    const $form = jQuery(this).parents('form');

                    $form.submit();
                });

                jQuery("body").on("click", ".collapser", function (e) {
                    let collapser_item = jQuery(this),

                        is_visible = collapser_item.hasClass('active'),
                        collapseItem = function (item) {
                            let target = item.data('target');
                            if (target) {
                                jQuery(target).removeClass('active');
                            }
                            item.removeClass('active');
                        },

                        expandItem = function (item) {
                            let target = item.data('target');
                            if (target) {
                                jQuery(target).addClass('active');
                            }
                            item.addClass('active');
                        };

                    if (collapser_item.data('exclusive')) {
                        jQuery('.collapser.active').each(function () {
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
                jQuery("body").on("click", ".filter-container input[type=search]", function (event) {
                    event.stopPropagation();
                    event.stopImmediatePropagation();
                    event.preventDefault();
                });
                jQuery("body").on("keyup", ".filter-container input[type=search]", function (e) {
                    var $filterInput = jQuery(this);
                    var filter_key = $filterInput.data('filterKey');

                    $filterInput.data('previousValue', '');
                    if ($filterInput.data('callbackUrl') !== undefined) {
                        if (Pachno.ift_observers[filter_key])
                            clearTimeout(Pachno.ift_observers[filter_key]);
                        if (($filterInput.getValue().length >= 3 || $filterInput.getValue().length == 0) && $filterInput.getValue() != $filterInput.data('lastValue')) {
                            Pachno.ift_observers[filter_key] = setTimeout(function () {
                                Pachno.Search.getFilterValues($filterInput);
                                $filterInput.data('lastValue', $filterInput.getValue());
                            }, 1000);
                        }
                    } else {
                        Pachno.Search.filterFilterOptionsElement($filterInput);
                    }
                });
                // jQuery('body').on('keydown', '.filter', function (event) {
                //     if (Event.KEY_DOWN == event.keyCode) {
                //         Pachno.Search.moveFilterDown(event, filter);
                //         event.stopPropagation();
                //         event.preventDefault();
                //     }
                //     else if (Event.KEY_UP == event.keyCode) {
                //         Pachno.Search.moveFilterUp(event, filter);
                //         event.stopPropagation();
                //         event.preventDefault();
                //     }
                //     else if (Event.KEY_RETURN == event.keyCode) {
                //         var selected_elements = filter.select('li.highlighted');
                //         var current_selected_element = (selected_elements.size() == 0) ? undefined : selected_elements[0];
                //         if (current_selected_element != undefined) {
                //             Pachno.Search.toggleFilterValueElement(current_selected_element);
                //         }
                //     }
                //     else if (Event.KEY_ESC == event.keyCode) {
                //         Pachno.Search.toggleInteractiveFilterElement(filter);
                //     }
                // });
                // filter.select('.filtervalue').each(function (elm) {
                //     if (!elm.hasClass('separator'))
                //         elm.addClass('unfiltered');
                // });

                jQuery("body").on("click", ".fancy-dropdown", function (e) {
                    var is_visible = jQuery(this).hasClass('active');
                    Pachno.Main.Profile.clearPopupsAndButtons();
                    if (!is_visible) {
                        jQuery(this).toggleClass('active');
                    }
                    e.stopPropagation();
                });
                // jQuery("body").on("click", ".dynamic_menu_link", function (e) {
                //     var menu = jQuery(this).next()[0];
                //     if (menu === undefined) {
                //         var menu = jQuery(this).parent().next()[0];
                //     }
                //     if (menu !== undefined && menu.hasClass('dynamic_menu')) {
                //         Pachno.Helpers.loadDynamicMenu(menu);
                //     }
                // });
                // jQuery("#user_notifications_container").on("click", Pachno.Main.Profile.toggleNotifications);
                jQuery("#disable-tutorial-button").on("click", Pachno.Tutorial.disable);

                // jQuery("body").on("click", function (e) {
                //     if (e.target.up('#topmenu-container') == undefined && jQuery('#topmenu-container').hasClass('active')) {
                //         jQuery('#topmenu-container').removeClass('active');
                //     }
                //     if (e.target.up('#user_notifications') == undefined && e.target.up('#user_notifications_container') == undefined && jQuery('#user_notifications').hasClass('active')) {
                //         jQuery('#user_notifications').removeClass('active');
                //         jQuery('#user_notifications_container').removeClass('active');
                //     }
                //     if (['INPUT'].indexOf(e.target.nodeName) != -1)
                //         return;
                //     else if (e.target.up('.popup_box') != undefined)
                //         return;
                //     else if (e.target && typeof(e.target.hasAttribute) == 'function' && e.target.hasAttribute('onclick'))
                //         return;
                //     else if (e.target && typeof(e.target.hasAttribute) == 'function' && e.target.hasAttribute('onclick'))
                //         return;
                //     Pachno.Main.Profile.clearPopupsAndButtons();
                //     if (e.target && jQuery(e.target).parents('#searchfor_autocomplete_choices').length > 0)
                //         return;
                //     if (Pachno.autocompleter !== undefined) {
                //         Pachno.autocompleter.options.forceHide();
                //     }
                //
                //     e.stopPropagation();
                // });
                jQuery("textarea").each(function (ta) {
                    ta.on('focus', function (e) {
                        Pachno.Main.initializeMentionable(e.target);
                        var ec = this.up('.editor_container');
                        if (ec != undefined)
                            ec.addClass('focussed');
                    });
                });
                jQuery("textarea").each(function (ta) {
                    ta.on('blur', function (e) {
                        var ec = this.up('.editor_container');
                        if (ec != undefined)
                            ec.removeClass('focussed');
                    });
                });
                Pachno.Main.Dashboard.initializeSorting($);
            })(jQuery);
        });

});
