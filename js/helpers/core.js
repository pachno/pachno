import $ from "jquery";

const Core = {
    /**
     * Initializes the autocompleter
     */
    _initializeAutocompleter: function () {
        if ($('#searchfor') == null)
            return;
        // Pachno.autocompleter = new Ajax.Autocompleter(
        //     "searchfor",
        //     "searchfor_autocomplete_choices",
        //     Pachno.autocompleter_url,
        //     {
        //         paramName: "fs[text][v]",
        //         parameters: "fs[text][o]==",
        //         minChars: 2,
        //         indicator: 'quicksearch_indicator',
        //         callback: function (element, entry) {
        //             $('#quicksearch_submit').prop('disabled', true);
        //             $('#quicksearch_submit').removeClass('button-blue');
        //             $('#quicksearch_submit').addClass('button-silver');
        //             return entry;
        //         },
        //         afterUpdateChoices: function () {
        //             $('#quicksearch_submit').prop('disabled', false);
        //             $('#quicksearch_submit').removeClass('button-silver');
        //             $('#quicksearch_submit').addClass('button-blue');
        //         },
        //         afterUpdateElement: Pachno.Core._extractAutocompleteValue,
        //         onHide: function () {},
        //         forceHide: function () {
        //             new Effect.Fade($('#searchfor_autocomplete_choices'),{duration:0.15});
        //         }
        //     }
        // );
    },

    /**
     * Helper function to extract url from autocomplete response container
     */
    _extractAutocompleteValue: function (elem, value, event) {
        var elements = value.find('.url');
        if (elements.length == 1 && value.find('.link').length == 0) {
            window.location = elements[0].innerHTML.unescapeHTML();
            $('#quicksearch_indicator').show();
            $('#quicksearch_submit').prop('disabled', true);
            $('#quicksearch_submit').removeClass('button-blue');
            $('#quicksearch_submit').addClass('button-silver');
            $('#searchfor').blur();
            $('#searchfor').value('');
        } else {
            var cb_elements = value.find('.backdrop');
            if (cb_elements.length == 1) {
                var elm = cb_elements[0];
                var backdrop_url = elm.down('.backdrop_url').innerHTML;
                Pachno.Helpers.Backdrop.show(backdrop_url);
                $('#searchfor').blur();
                $('#searchfor').value('');
                event.stopPropagation();
                event.preventDefault();
            }
        }
    },

    /**
     * Monitors viewport resize to adapt backdrops
     */
    _resizeWatcher: function () {
        return;
        // Pachno.Core._vp_width = document.viewport.getWidth();
        // Pachno.Core._vp_height = document.viewport.getHeight();
        // if (($('#attach_file') && $('#attach_file').visible())) {
        //     var backdropheight = $('#backdrop_detail_content').getHeight();
        //     if (backdropheight > (Pachno.Core._vp_height - 100)) {
        //         $('#backdrop_detail_content').css({height: Pachno.Core._vp_height - 100 + 'px', overflow: 'scroll'});
        //     } else {
        //         $('#backdrop_detail_content').css({height: 'auto', overflow: ''});
        //     }
        // }
        // Pachno.Core.popupVisiblizer();
    },

    popupVisiblizer: function () {
        return;
        // var visible_popups = $('.dropdown_box').findAll(function (el) {
        //     return el.visible();
        // });
        // if (visible_popups.length) {
        //     visible_popups.each(function (element) {
        //         if ($(element).hasClass("user_dropdown"))
        //             return;
        //         var max_bottom = document.viewport.getHeight();
        //         var element_height = $(element).getHeight();
        //         var parent_offset = $(element).parents().cumulativeOffset().top;
        //         var element_min_bottom = parent_offset + element_height + 35;
        //         if (max_bottom < element_min_bottom) {
        //             if ($(element).getStyle('position') != 'fixed') {
        //                 $(element).data({'top': $(element).getStyle('top')});
        //             }
        //             $(element).css({'position': 'fixed', 'bottom': '5px', 'top': 'auto'});
        //         } else {
        //             $(element).css({'position': 'absolute', 'bottom': 'auto', 'top': $(element).data('top')});
        //         }
        //     });
        // }
    },

    /**
     * Monitors viewport scrolling to adapt fixed positioners
     */
    _scrollWatcher: function () {
        return;
        var vihc = $('#viewissue_header_container');
        if (vihc) {
            var iv = $('#issue_view');
            var y = $(document).scrollTop();
            var compare_coord = (vihc.hasClass('fixed')) ? iv.offsetTop - 15 : vihc.offsetTop;
            if (y >= compare_coord) {
                $('#issue-main-container').addClass('scroll-top');
                $('#issue_details_container').addClass('scroll-top');
                vihc.addClass('fixed');
                $('#workflow_actions').addClass('fixed');
                if ($('#votes_additional').visible() && $('#votes_additional').hasClass('visible')) $('#votes_additional').hide();
                if ($('#user_pain_additional').visible() && $('#user_pain_additional').hasClass('visible')) $('#user_pain_additional').hide();
                var vhc_layout = vihc.getLayout();
                var vhc_height = vhc_layout.get('height') + vhc_layout.get('padding-top') + vhc_layout.get('padding-bottom');
                if (y >= $('#viewissue_comment_count').offsetTop) {
                    if ($('#comment_add_button') != undefined && !$('#comment_add_button').hasClass('immobile')) {
                        var button = $('#comment_add_button').remove();
                        $('#workflow_actions').down('ul').append(button);
                    }
                } else if ($('#comment_add_button') != undefined) {
                    var button = $('#comment_add_button').remove();
                    $('#add_comment_button_container').html(button);
                }
            } else {
                $('#issue-main-container').removeClass('scroll-top');
                $('#issue_details_container').removeClass('scroll-top');
                vihc.removeClass('fixed');
                $('#workflow_actions').removeClass('fixed');
                if (! $('#votes_additional').visible() && $('#votes_additional').hasClass('visible')) $('#votes_additional').show();
                if (! $('#user_pain_additional').visible() && $('#user_pain_additional').hasClass('visible')) $('#user_pain_additional').show();
                if ($('#comment_add_button') != undefined && !$('#comment_add_button').hasClass('immobile')) {
                    var button = $('#comment_add_button').remove();
                    $('#add_comment_button_container').html(button);
                }
            }
        }
        if ($('#search-bulk-action-form')) {
            var y = document.viewport.getScrollOffsets().top;
            var co = $('#search-bulk-action-form').parents('.bulk_action_container').cumulativeOffset();
            if (y >= co.top) {
                $('#search-bulk-action-form').addClass('fixed');
            } else {
                $('#search-bulk-action-form').removeClass('fixed');
            }
        }
        if ($('#whiteboard')) {
            var y = document.viewport.getScrollOffsets().top;
            var co = $('#whiteboard').cumulativeOffset();
            if (y >= co.top) {
                $('#whiteboard').addClass('fixedheader');
            } else {
                $('#whiteboard').removeClass('fixedheader');
            }
        }
        if ($('#issues_paginator')) {
            var ip = $('#issues_paginator');
            var ipl = ip.getLayout();
            var ip_height = ipl.get('height') + ipl.get('padding-top') + ipl.get('padding-bottom');

            var y = document.viewport.getScrollOffsets().top + document.viewport.getHeight();
            var y2 = $('body')[0].scrollHeight;
            if (y >= y2 - ip_height) {
                ip.removeClass('visible');
            } else {
                ip.addClass('visible');
            }
        }
    },

    _detachFile: function (url, file_id, base_id, loading_indicator) {
        Pachno.Helpers.fetch(url, {
            loading: {
                indicator: typeof(loading_indicator) != 'undefined' ? loading_indicator : base_id + file_id + '_remove_indicator',
                hide: [base_id + file_id + '_remove_link', 'uploaded_files_' + file_id + '_remove_link'],
                show: 'uploaded_files_' + file_id + '_remove_indicator'
            },
            success: {
                remove: [base_id + file_id, 'uploaded_files_' + file_id, base_id + file_id + '_remove_confirm', 'uploaded_files_' + file_id + '_remove_confirm'],
                callback: function (json) {
                    if (json.attachmentcount == 0 && $('#viewissue_no_uploaded_files'))
                        $('#viewissue_no_uploaded_files').show();
                    if ($('#viewissue_uploaded_attachments_count'))
                        $('#viewissue_uploaded_attachments_count').html(json.attachmentcount);
                    Pachno.Helpers.Dialog.dismiss();
                }
            },
            failure: {
                show: [base_id + file_id + '_remove_link', 'uploaded_files_' + file_id + '_remove_link'],
                hide: 'uploaded_files_' + file_id + '_remove_indicator'
            }
        });
    },

    _escapeWatcher: (event) => {
        if (Event.KEY_ESC != event.keyCode)
            return;
        Pachno.Helpers.Backdrop.reset();
    },

    fetchPostHelper: (form) => {
        return new Promise(function (resolve, reject) {
            const $form = $(form),
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
    },

    fetchPostDefaultFormHandler: ([$form, response]) => {
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
    }
};

export default Core;
