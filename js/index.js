import $ from "jquery";
import Pachno from "./classes/pachno";

// Pachno.Main.updatePercentageLayout = function (arg1, arg2) {
//     if (isNaN(arg1))
//     {
//         $(arg1).style.width = arg2 + "%";
//     } else {
//         $('#percent_complete_content').find('.percent_filled').first().style.width = arg1 + '%';
//     }
// };
//
// Pachno.Main.updateAttachments = function (form) {
//     var url = form.action;
//     Pachno.Helpers.fetch(url, {
//         form: form,
//         method: 'POST',
//         loading: {
//             indicator: '#attachments_indicator',
//             callback: function () {
//                 $('#dynamic_uploader_submit').addClass('disabled');
//                 $('#dynamic_uploader_submit').prop('disabled', true);
//                 $('#report_issue_submit_button').addClass('disabled');
//                 $('#report_issue_submit_button').prop('disabled', true);
//             }
//         },
//         success: {
//             callback: function (json) {
//                 Pachno.Helpers.Backdrop.reset();
//                 var base = $(json.container_id);
//                 if (base !== undefined) {
//                     base.html('');
//                     json.files.each(function (file_elm) {
//                         base.append(file_elm);
//                     });
//                     if (json.files.length) {
//                         if ($('#viewissue_uploaded_attachments_count')) $('#viewissue_uploaded_attachments_count').html(json.files.length);
//                         $('#viewissue_no_uploaded_files').hide();
//                     }
//                 }
//                 $('#comments_box').prepend(json.comments);
//             }
//         },
//         complete: {
//             callback: function () {
//                 $('#dynamic_uploader_submit').addClass('disabled');
//                 $('#dynamic_uploader_submit').prop('disabled', false);
//                 $('#report_issue_submit_button').addClass('disabled');
//                 $('#report_issue_submit_button').prop('disabled', false);
//             }
//         }
//     });
//
// };
//
// Pachno.Main.Link.add = function (url, target_type, target_id) {
//     Pachno.Helpers.fetch(url, {
//         form: 'attach_link_' + target_type + '_' + target_id + '_form',
//         loading: {
//             indicator: '#attach_link_' + target_type + '_' + target_id + '_indicator',
//             callback: function () {
//                 $('#attach_link_' + target_type + '_' + target_id + '_submit').prop('disabled', true);
//             }
//         },
//         success: {
//             reset: 'attach_link_' + target_type + '_' + target_id + '_form',
//             hide: ['attach_link_' + target_type + '_' + target_id, target_type + '_' + target_id + '_no_links'],
//             update: {element: target_type + '_' + target_id + '_links', insertion: true},
//             callback: function () {
//                 if ($(target_type + '_' + target_id + '_container').hasClass('menu_editing')) {
//                     $('#toggle_' + target_type + '_' + target_id +'_edit_mode').trigger('click');
//                     $('#toggle_' + target_type + '_' + target_id +'_edit_mode').trigger('click');
//                 }
//             }
//         },
//         complete: {
//             callback: function () {
//                 $('#attach_link_' + target_type + '_' + target_id + '_submit').prop('disabled', false);
//             }
//         }
//     });
// };
//
// Pachno.Main.Link.remove = function (url, target_type, target_id, link_id) {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             hide: target_type + '_' + target_id + '_links_' + link_id + '_remove_link',
//             indicator: '#dialog_indicator'
//         },
//         success: {
//             remove: [target_type + '_' + target_id + '_links_' + link_id, target_type + '_' + target_id + '_links_' + link_id + '_remove_confirm'],
//             callback: function (json) {
//                 Pachno.Helpers.Dialog.dismiss();
//                 if ($(json.target_type + '_' + json.target_id + '_links').children().length == 0) {
//                     $(json.target_type + '_' + json.target_id + '_no_links').show();
//                 }
//             }
//         },
//         failure: {
//             show: target_type + '_' + target_id + '_links_' + link_id + '_remove_link'
//         }
//     });
// };
//
// Pachno.Main.Menu.toggleEditMode = function (target_type, target_id, url) {
//     if ($(target_type + '_' + target_id + '_container').hasClass('menu_editing')) {
//         Sortable.destroy(target_type + '_' + target_id + '_links');
//     } else {
//         Sortable.create(target_type + '_' + target_id + '_links', {constraint: '', onUpdate: function (container) {
//             Pachno.Main.Menu.saveOrder(container, target_type, target_id, url);
//         }});
//     }
//     $(target_type + '_' + target_id + '_container').toggleClass('menu_editing');
// };
//
// Pachno.Main.Menu.saveOrder = function (container, target_type, target_id, url) {
//     Pachno.Helpers.fetch(url, {
//         data: Sortable.serialize(container),
//         loading: {
//             indicator: target_type + '_' + target_id + '_indicator'
//         }
//     });
// };
//
// Pachno.Main.detachFileFromArticle = function (url, file_id, article_id) {
//     Pachno.Core._detachFile(url, file_id, 'article_' + article_id + '_files_', 'dialog_indicator');
// };
//
// Pachno.Main.toggleFavouriteArticle = function (url, article_id)
// {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: '#article_favourite_indicator_' + article_id,
//             hide: ['article_favourite_normal_' + article_id, 'article_favourite_faded_' + article_id]
//         },
//         success: {
//             callback: function (json) {
//                 if ($('#article_favourite_faded_' + article_id)) {
//                     if (json.starred) {
//                         $('#article_favourite_faded_' + article_id).hide();
//                         $('#article_favourite_indicator_' + article_id).hide();
//                         $('#article_favourite_normal_' + article_id).show();
//                     } else {
//                         $('#article_favourite_normal_' + article_id).hide();
//                         $('#article_favourite_indicator_' + article_id).hide();
//                         $('#article_favourite_faded_' + article_id).show();
//                     }
//                 } else if (json.subscriber != '') {
//                     $('#subscribers_list').append(json.subscriber);
//                 }
//             }
//         }
//     });
// };
//
// Pachno.Main.deleteArticle = function (url) {
//     Pachno.Helpers.fetch(url, {
//         method: 'POST',
//         loading: {
//             indicator: '#fullpage_backdrop',
//             show: 'fullpage_backdrop_indicator',
//             hide: ['fullpage_backdrop_content', 'dialog_backdrop']
//         },
//         success: {
//             callback: function () {
//                 location.reload();
//             }
//         }
//     });
// };
//
// Pachno.Main.Profile.changePassword = function (url) {
//     Pachno.Helpers.fetch(url, {
//         form: 'change_password_form',
//         loading: {indicator: '#change_password_indicator'},
//         success: {reset: 'change_password_form', hide: 'change_password_div'}
//     });
// };
//
// Pachno.Main.Profile.addApplicationPassword = function (url) {
//     Pachno.Helpers.fetch(url, {
//         form: 'add_application_password_form',
//         loading: {indicator: '#add_application_password_indicator'},
//         success: {
//             hide: 'add_application_password_container',
//             update: {element: 'application_password_preview', from: 'password'},
//             show: 'add_application_password_response'
//         }
//     });
// };
//
// Pachno.Main.Profile.removeApplicationPassword = function (url, p_id) {
//     Pachno.Helpers.fetch(url, {
//         method: 'POST',
//         loading: {
//             callback: function () {
//                 $('#application_password_' + p_id).down('button').prop('disabled', true);
//             }
//         },
//         success: {
//             remove: 'application_password_' + p_id,
//             callback: function () {
//                 Pachno.Helpers.Dialog.dismiss();
//             }
//         },
//         failure: {
//             callback: function () {
//                 $('#application_password_' + p_id).down('button').prop('disabled', false);
//             }
//         }
//     });
// };
//
// Pachno.Main.Profile.checkUsernameAvailability = function (url) {
//     Pachno.Helpers.fetch(url, {
//         form: 'check_username_form',
//         loading: {
//             indicator: '#pick_username_indicator',
//             hide: 'username_unavailable'
//         },
//         complete: {
//             callback: function (json) {
//                 if (json.available) {
//                     Pachno.Helpers.Backdrop.show(json.url);
//                 } else {
//                     $('#username_unavailable').show();
//                     $('#username_unavailable').pulsate({pulses: 3, duration: 1});
//                 }
//             }
//         }
//     });
// };
//
// Pachno.Main.Profile.toggleNotificationSettings = function (preset) {
//     if (preset == 'custom') {
//         $('#notification_settings_selectors').show();
//     } else {
//         $('#notification_settings_selectors').hide();
//     }
// };
//
// Pachno.Main.Profile.removeOpenIDIdentity = function (url, oid) {
//     Pachno.Helpers.fetch(url, {
//         loading: {indicator: '#dialog_indicator'},
//         success: {
//             remove: 'openid_account_' + oid,
//             callback: function () {
//                 if ($('#openid_accounts_list').children().length == 0)
//                     $('#no_openid_accounts').show();
//                 if ($('#openid_accounts_list').children().length == 1 && $('#pick_username_button'))
//                     $('#openid_accounts_list').down('.button').remove();
//                 Pachno.Helpers.Dialog.dismiss();
//             }
//         }
//     });
// };
//
// Pachno.Main.Profile.cancelScopeMembership = function (url, sid) {
//     Pachno.Helpers.fetch(url, {
//         loading: {indicator: '#dialog_indicator'},
//         success: {
//             remove: 'account_scope_' + sid,
//             callback: function () {
//                 if ($('#pending_scope_memberships').children().length == 0)
//                     $('#no_pending_scope_memberships').show();
//                 Pachno.Helpers.Dialog.dismiss();
//             }
//         }
//     });
// };
//
// Pachno.Main.Profile.confirmScopeMembership = function (url, sid) {
//     Pachno.Helpers.fetch(url, {
//         loading: {indicator: '#dialog_indicator'},
//         success: {
//             callback: function () {
//                 $('#confirmed_scope_memberships').append($('#account_scope_' + sid).remove());
//                 $('#account_scope_' + sid).down('.button-green').remove();
//                 $('#account_scope_' + sid).down('.button-red').show();
//                 if ($('#pending_scope_memberships').children().length == 0)
//                     $('#no_pending_scope_memberships').show();
//                 Pachno.Helpers.Dialog.dismiss();
//             }
//         }
//     });
// };
//
// Pachno.Main.Dashboard.View.init = function (view_id) {
//     var dashboard_element = $('#dashboard_container_' + view_id),
//         dashboard_container = dashboard_element.parents('.dashboard'),
//         url = dashboard_container.data('url').replace('{view_id}', view_id);
//
//     if (dashboard_element.data('preloaded') == "0") {
//         Pachno.Helpers.fetch(url, {
//             method: 'GET',
//             loading: {indicator: '#dashboard_view_' + view_id + '_indicator'},
//             success: {update: '#dashboard_view_' + view_id},
//             complete: {
//                 callback: function () {
//                     Pachno.Core._resizeWatcher();
//                     Pachno.Main.Dashboard.views.splice(0, 1);
//                     if (Pachno.Main.Dashboard.views.length == 0) {
//                         $('html').css({'cursor': 'default'});
//                     }
//                 }
//             }
//         });
//     }
// };
//
// Pachno.Main.Dashboard.sort = function (event) {
//     var list = $(event.target);
//     var url = list.parents('.dashboard').data('sort-url');
//     var items = '&column=' + list.data('column');
//     list.children().each(function (view) {
//         if (view.data('view-id') !== undefined) {
//             items += '&view_ids[]=' + view.data('view-id');
//         }
//     });
//     Pachno.Helpers.fetch(url, {
//         data: items,
//         loading: {indicator: list.down('.dashboard_indicator')}
//     });
// };
//
// Pachno.Main.Dashboard.initializeSorting = function ($) {
//     $('.dashboard_column.jsortable').sortable({
//         handle: '.dashboardhandle',
//         connectWith: '.dashboard_column',
//         items: '.dashboard_view_container',
//         helper: function(event, ui){
//             var $clone =  $(ui).clone();
//             $clone .css('position','absolute');
//             return $clone.get(0);
//         }
//     }).bind('sortupdate', Pachno.Main.Dashboard.sort);
// };
//
// Pachno.Main.Dashboard.addView = function (element) {
//     var dashboard_element = element.parents('.dashboard_view');
//     element.prop('disabled', true);
//     var dashboard_views_container = dashboard_element.parents('.available_views_container');
//     var dashboard_container = $('#dashboard_' + dashboard_views_container.data('dashboard-id'));
//     var url = dashboard_container.data('post-url');
//     var column = dashboard_views_container.data('column');
//     Pachno.Helpers.fetch(url, {
//         method: 'POST',
//         params: 'mode=add_view&view_type=' + dashboard_element.data('view-type') + '&view_subtype=' + dashboard_element.data('view-subtype') + '&column=' + column,
//         loading: {
//             indicator: dashboard_element.down('.view_indicator'),
//         },
//         success: {
//             callback: function (json) {
//                 var column_container = dashboard_container.down('.dashboard_column.column_' + column);
//                 column_container.append(json.view_content);
//                 Pachno.Main.Dashboard.views.push(json.view_id);
//                 Pachno.Main.Dashboard.View.init(json.view_id);
//                 element.prop('disabled', false);
//                 Pachno.Main.Dashboard.initializeSorting(jQuery);
//             }
//         }
//     });
// };
//
// Pachno.Main.Dashboard.removeView = function (event, element) {
//     var view_id = element.parents('.dashboard_view_container').data('view-id');
//     var column = element.parents('.dashboard_column');
//     var dashboard_container = element.parents('.dashboard');
//     var url = dashboard_container.data('post-url');
//     Pachno.Helpers.fetch(url, {
//         params: '&mode=remove_view&view_id=' + view_id,
//         loading: {indicator: element.parents('.dashboard_view_container').down('.dashboard_indicator')},
//         success: {
//             remove: 'dashboard_container_' + view_id
//         }
//     });
// };
//
// Pachno.Main.Dashboard.toggleMenu = function (link) {
//     var section = $(link).data('section');
//     $(link).parents('ul').children().each(function (menu_elm) {
//         menu_elm.removeClass('selected');
//     })
//     $(link).parents('li').addClass('selected');
//     $(link).parents('.backdrop_detail_content').down('.available_views_container').children().each(function (view_list) {
//         ($(view_list).data('section') == section) ? $(view_list).show() : $(view_list).hide();
//     });
//
// };
//
// Pachno.Main.Dashboard.sidebar = function (url, id)
// {
//     Pachno.Main.setToggleState(url, !$(id).hasClass('collapsed'));
//     $(id).toggleClass('collapsed');
//     Pachno.Core._resizeWatcher();
//     Pachno.Core._scrollWatcher();
// }
//
// Pachno.Main.Profile.setState = function (url, ind) {
//     Pachno.Helpers.fetch(url, {
//         loading: {indicator: ind},
//         success: {
//             callback: function (json) {
//                 $('.current_userstate').each(function (element) {
//                     $(element).html(json.userstate);
//                 });
//             }
//         }
//     });
// }
//
// Pachno.Main.Profile.addFriend = function (url, user_id, rnd_no) {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: '#toggle_friend_' + user_id + '_' + rnd_no + '_indicator',
//             hide: ['add_friend_' + user_id + '_' + rnd_no, 'user_' + user_id + '_more_actions']
//         },
//         success: {
//             show: ['remove_friend_' + user_id + '_' + rnd_no, 'user_' + user_id + '_more_actions']
//         },
//         failure: {
//             show: ['add_friend_' + user_id + '_' + rnd_no, 'user_' + user_id + '_more_actions']
//         }
//     });
// }
//
// Pachno.Main.Profile.removeFriend = function (url, user_id, rnd_no) {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: '#toggle_friend_' + user_id + '_' + rnd_no + '_indicator',
//             hide: ['remove_friend_' + user_id + '_' + rnd_no, 'user_' + user_id + '_more_actions']
//         },
//         success: {
//             show: ['add_friend_' + user_id + '_' + rnd_no, 'user_' + user_id + '_more_actions']
//         },
//         failure: {
//             show: ['remove_friend_' + user_id + '_' + rnd_no, 'user_' + user_id + '_more_actions']
//         }
//     });
// };
//
// Pachno.Main.hideInfobox = function (url, boxkey) {
//     if ($('#close_me_' + boxkey).checked) {
//         var $form = $('#close_me_' + boxkey + '_form');
//         $form.addClass('submitting');
//         $form.find('.button.primary').prop('disabled', true);
//
//         fetch(url)
//             .then(function (response) {
//                 setTimeout(function () {
//                     $form.removeClass('submitting');
//                     $form.find('.button.primary').prop('disabled', false);
//                 }, 300);
//                 $('#infobox_' + boxkey).fade({duration: 0.25});
//             });
//     } else {
//         $('#infobox_' + boxkey).fade({duration: 0.3});
//     }
// };
//
// Pachno.Main.setToggleState = function (url, state) {
//     url += '/' + (state ? '1' : 0);
//     Pachno.Helpers.fetch(url, {});
// };
//
// Pachno.Main.Comment.toggleOrder = function (target_type, target_id) {
//     Pachno.Helpers.fetch($('#main_container').data('url'), {
//         method: 'POST',
//         loading: {
//             indicator: '#comments_loading_indicator'
//         },
//         params: '&say=togglecommentsorder',
//         success: {
//             callback: function () {
//                 Pachno.Main.Comment.reloadAll(target_type, target_id);
//             }
//         }
//     });
// };
//
// Pachno.Main.Comment.reloadAll = function (target_type, target_id) {
//     Pachno.Helpers.fetch($('#main_container').data('url'), {
//         method: 'GET',
//         loading: {
//             indicator: '#comments_loading_indicator'
//         },
//         params: '&say=loadcomments&target_type='+target_type+'&target_id='+target_id,
//         success: {
//             callback: function (json) {
//                 $('#comments_box').html(json.comments);
//             }
//         }
//     });
// };
//
// Pachno.Main.Comment.remove = function (url, comment_id, commentcount_span) {
//     $('#dialog_indicator').show();
//     fetch(url, {
//         method: 'DELETE'
//     })
//         .then(function (response) {
//             response.json()
//                 .then(function () {
//                     if (response.ok) {
//                         $('#comment_' + comment_id).remove();
//                         Pachno.Helpers.Dialog.dismiss();
//                         $('#dialog_indicator').hide();
//                         if ($('#comments_box').children().length == 0) {
//                             $('#comments-list-none').show();
//                         }
//                         $(commentcount_span).html($('#comments_box').children().length);
//                     }
//                 });
//         });
//     // Pachno.Helpers.fetch(url, {
//     //     method: 'DELETE'
//     //     loading: {
//     //         indicator: '#dialog_indicator'
//     //     },
//     //     success: {
//     //         remove: 'comment_' + comment_id,
//     //         callback: function () {
//     //             Pachno.Helpers.Dialog.dismiss();
//     //             if ($('#comments_box').children().length == 0) {
//     //                 $('#comments-list-none').show();
//     //             }
//     //             $(commentcount_span).html($('#comments_box').children().length);
//     //         }
//     //     }
//     // });
// };
//
// Pachno.Main.Comment.update = function (comment_id) {
//     var $form = $('#comment_edit_form_' + comment_id),
//         data = new FormData($form[0]),
//         $comment_container = $('#comment_' + comment_id + '_content');
//
//     $form.find('.error-container').removeClass('invalid');
//     $form.find('.error-container > .error').html('');
//     $form.addClass('submitting');
//     $form.find('.button.primary').prop('disabled', true);
//
//     fetch($form.attr('action'), {
//         method: 'POST',
//         body: data
//     })
//         .then(function (response) {
//             response.json().then(function (json) {
//                 if (response.ok) {
//                     $comment_container.html(json.comment_data);
//                     $('#comment_edit_' + comment_id).removeClass('active');
//                     $('#comment_' + comment_id + '_body').show();
//                     $('#comment_view_' + comment_id).show();
//                 } else {
//                     $form.find('.error-container > .error').html(json.error);
//                     $form.find('.error-container').addClass('invalid');
//                 }
//
//                 $form.removeClass('submitting');
//                 $form.find('.button.primary').prop('disabled', false);
//             });
//         });
//
//     // Pachno.Helpers.fetch(url, {
//     //     form: 'comment_edit_form_' + comment_id,
//     //     loading: {
//     //         indicator: '#comment_edit_indicator_' + comment_id,
//     //         hide: 'comment_edit_controls_' + comment_id
//     //     },
//     //     success: {
//     //         hide: ['comment_edit_indicator_' + comment_id],
//     //         show: ['comment_view_' + comment_id, 'comment_edit_controls_' + comment_id, 'comment_add_button'],
//     //         update: {element: 'comment_' + comment_id + '_content', from: 'comment_body'},
//     //         callback: function () {
//     //             $('#comment_edit_' + comment_id).removeClass('active');
//     //             $('#comment_' + comment_id + '_body').show();
//     //         }
//     //     },
//     //     failure: {
//     //         show: ['comment_edit_controls_' + comment_id]
//     //     }
//     // });
// };
//
// Pachno.Main.Comment.add = function (url, commentcount_span) {
//     var $form = $('#add-comment-form'),
//         data = new FormData($form[0]),
//         $count_span = $('#' + commentcount_span),
//         $comments_container = $('#comments_box');
//
//     $form.find('.error-container').removeClass('invalid');
//     $form.find('.error-container > .error').html('');
//     $form.addClass('submitting');
//     $form.find('.button.primary').prop('disabled', true);
//
//     fetch($form.attr('action'), {
//         method: 'POST',
//         body: data
//     })
//         .then(function (response) {
//             response.json().then(function (json) {
//                 if (response.ok) {
//                     $comments_container.append(json.comment_data);
//                     $('#comments-list-none').remove();
//                     window.location.hash = "#comment_" + json.comment_id;
//                     $count_span.html(json.commentcount);
//                     $form[0].reset();
//
//                     $('#comment_add').hide();
//                     $('#comment_add_button').show();
//                 } else {
//                     $form.find('.error-container > .error').html(json.error);
//                     $form.find('.error-container').addClass('invalid');
//                 }
//
//                 $form.removeClass('submitting');
//                 $form.find('.button.primary').prop('disabled', false);
//             });
//         });
// };
//
// Pachno.Main.Comment.reply = function (reply_comment_id) {
//     var $form = $('#comment_reply_form_' + reply_comment_id),
//         data = new FormData($form[0]),
//         $comments_container = $('#comment_' + reply_comment_id + '_replies');
//
//     $form.find('.error-container').removeClass('invalid');
//     $form.find('.error-container > .error').html('');
//     $form.addClass('submitting');
//     $form.find('.button.primary').prop('disabled', true);
//
//     fetch($form.attr('action'), {
//         method: 'POST',
//         body: data
//     })
//         .then(function (response) {
//             response.json().then(function (json) {
//                 if (response.ok) {
//                     $comments_container.append(json.comment_data);
//                     window.location.hash = "#comment_" + json.comment_id;
//                     $form[0].reset();
//
//                     $('#comment_reply_controls_' + reply_comment_id).show();
//                     $('#comment_reply_' + reply_comment_id).removeClass('active');
//                 } else {
//                     $form.find('.error-container > .error').html(json.error);
//                     $form.find('.error-container').addClass('invalid');
//                 }
//
//                 $form.removeClass('submitting');
//                 $form.find('.button.primary').prop('disabled', false);
//             });
//         });
// };
//
// Pachno.Main.Login.register = function (url)
// {
//     Pachno.Helpers.fetch(url, {
//         form: 'register_form',
//         loading: {
//             indicator: '#register_indicator',
//             hide: 'register_button',
//             callback: function () {
//                 $('#input.required').each(function (field) {
//                     $(field).css({backgroundColor: ''});
//                 });
//             }
//         },
//         success: {
//             hide: 'register_form',
//             update: {element: 'register_message', from: 'loginmessage'},
//             callback: function (json) {
//                 if (json.activated) {
//                     $('#register_username_hidden').value($('#fieldusername').val());
//                     $('#register_password_hidden').value(json.one_time_password);
//                     $('#register_auto_form').show();
//                 } else {
//                     $('#register_confirm_back').show();
//                 }
//                 $('#register_confirmation').show();
//             }
//         },
//         failure: {
//             show: 'register_button',
//             callback: function (json) {
//                 json.fields.each(function (field) {
//                     $(field).css({backgroundColor: '#FBB'});
//                 });
//             }
//         }
//     });
// };
//
// Pachno.Main.Login.checkUsernameAvailability = function (url)
// {
//     var $username_row = $('#row-register-username'),
//         data = new FormData();
//
//     data.append('username', $('#fieldusername').val());
//     $username_row.addClass('submitting');
//
//     fetch(url, {
//         method: 'POST',
//         body: data
//     })
//         .then((_) => _.json())
//         .then(function (json) {
//             $username_row.removeClass('submitting');
//             if (json.available) {
//                 $username_row.removeClass('invalid');
//             } else {
//                 $username_row.addClass('invalid');
//             }
//         });
// };
//
// Pachno.Main.Login.registerAutologin = function (url)
// {
//     Pachno.Helpers.fetch(url, {
//         form: 'register_auto_form',
//         loading: {
//             indicator: '#register_autologin_indicator',
//             callback: function () {
//                 $('#register_autologin_button').prop('disabled', true);
//                 $('#register_autologin_indicator').show();
//             }
//         },
//         complete: {
//             callback: function () {
//                 $('#register_autologin_indicator').hide();
//                 $('#register_autologin_button').prop('disabled', false);
//             }
//         }
//     });
// };
//
// Pachno.Main.Login.login = function ()
// {
//     var $form = $('#login_form'),
//         $login_button = $('#login_button'),
//         url = $form.attr('action');
//
//     $('#login-error-container').removeClass('invalid');
//     $login_button.addClass('submitting');
//     $login_button.prop('disabled', true);
//
//     fetch(url, {
//         method: 'POST',
//         body: new FormData($form[0])
//     })
//         .then(function (response) {
//             response.json().then(function (json) {
//                 $login_button.removeClass('submitting');
//                 $login_button.prop('disabled', false);
//
//                 if (response.ok) {
//                     if (json.forward) {
//                         window.location = json.forward;
//                     } else {
//                         window.location.reload();
//                     }
//                 } else {
//                     console.error(json);
//                     $('#login-error-message').html(json.error);
//                     $('#login-error-container').addClass('invalid');
//                 }
//             });
//         })
//         .catch(function (error) {
//             $('#login-error-message').html(error);
//             $('#login-error-container').addClass('invalid');
//             console.error(error);
//         });
//
//     // Pachno.Helpers.fetch(url, {
//     //     form: 'login_form',
//     //     loading: {
//     //         indicator: '#login_indicator',
//     //         callback: function () {
//     //             $('#login_button').prop('disabled', true);
//     //             $('#login_indicator').show();
//     //         }
//     //     },
//     //     complete: {
//     //         callback: function () {
//     //             $('#login_indicator').hide();
//     //             $('#login_button').prop('disabled', false);
//     //         }
//     //     }
//     // });
// };
//
// Pachno.Main.Login.verify2FaTokenWithLogin = function (form) {
//     Pachno.Core.fetchPostHelper(form)
//         .then(Pachno.Core.fetchPostDefaultFormHandler)
//         .then(([$form, response]) => {
//             if (response.ok) {
//                 response.json().then(function (json) {
//                     window.location = json.forward;
//                 });
//             }
//         })
// };
//
// Pachno.Main.Login.elevatedLogin = function (url)
// {
//     Pachno.Helpers.fetch(url, {
//         form: 'login_form',
//         loading: {
//             indicator: '#elevated_login_indicator',
//             callback: function () {
//                 $('#login_button').prop('disabled', true);
//                 $('#elevated_login_indicator').show();
//             }
//         },
//         complete: {
//             callback: function (json) {
//                 $('#elevated_login_indicator').hide();
//                 if (json.elevated) {
//                     window.location.reload(true);
//                 } else {
//                     Pachno.Helpers.Message.error(json.error);
//                     $('#login_button').prop('disabled', false);
//                 }
//             }
//         }
//     });
// };
//
// Pachno.Main.Login.resetForgotPassword = function (url) {
//     Pachno.Helpers.fetch(url, {
//         form: 'forgot_password_form',
//         loading: {
//             indicator: '#forgot_password_indicator',
//             hide: 'forgot_password_button'
//         },
//         failure: {
//             reset: 'forgot_password_form'
//         },
//         complete: {
//             show: 'forgot_password_button',
//             callback: function () {
//                 $('#regular_login_container').parents().find('.logindiv').each(function (elm) {
//                     elm.removeClass('active');
//                 });
//                 $('#regular_login_container').addClass('active');
//             }
//         }
//     });
// };
//
// Pachno.Main.Login.showLogin = function (section) {
//     $('#login_backdrop').find('.logindiv').removeClass('active');
//     $(section).addClass('active');
//     if (section != 'register' && $('#registration-button-container')) {
//         $('#registration-button-container').addClass('active');
//     }
//     $('#login_backdrop').show();
//     setTimeout(function () {
//         if (section == 'register') {
//             $('#fieldusername').focus();
//         } else if (section == 'regular_login_container') {
//             $('#pachno_username').focus();
//         }
//     }, 250);
// };
//
// Pachno.Main.Login.forgotToggle = function () {
//     $('#regular_login_container').parents().find('.logindiv').each(function () {
//         $(this).removeClass('active');
//     });
//     $('#forgot_password_container').addClass('active');
// };
//
// Pachno.Project.Statistics.get = function (url, section) {
//     $('#statistics_selector').children().each(function () {
//         $(this).removeClass('selected');
//     });
//     $('#statistics_per_' + section + '_selector').addClass('selected');
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             show: 'statistics_main',
//             hide: 'statistics_help',
//             callback: function () {
//                 $('#statistics_main_image').src = '';
//                 for (var cc = 1; cc <= 3; cc++) {
//                     $('#statistics_mini_image_' + cc).src = '';
//                 }
//             }
//         },
//         success: {
//             callback: function (json) {
//                 $('#statistics_main_image').src = json.images.main;
//                 ecc = 1;
//                 for (var cc = 1; cc <= 3; cc++) {
//                     var small_name = 'mini_' + cc + '_small';
//                     var large_name = 'mini_' + cc + '_large';
//                     if (json.images[small_name]) {
//                         $('#statistics_mini_image_' + cc).show();
//                         $('#statistics_mini_image_' + cc).src = json.images[small_name];
//                         $('#statistics_mini_' + cc + '_main').value(json.images[large_name]);
//                     } else {
//                         $('#statistics_mini_image_' + cc).hide();
//                         $('#statistics_mini_' + cc + '_main').value('');
//                         ecc++;
//                     }
//                 }
//                 if (ecc == cc) {
//                     $('#statistics_main_image_div').next().hide();
//                     $('#statistics_main_image_div').next().next().hide();
//                 }
//                 else {
//                     $('#statistics_main_image_div').next().show();
//                     $('#statistics_main_image_div').next().next().show();
//                 }
//             }
//         },
//         failure: {show: 'statistics_help'}
//     });
// };
//
// Pachno.Project.Statistics.toggleImage = function (image) {
//     $('#statistics_main_image').src = '';
//     $('#statistics_main_image').src = $('#statistics_mini_' + image + '_main').val();
// };
//
// Pachno.Project.Milestone.refresh = function (url, milestone_id) {
//     var m_id = milestone_id;
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: '#milestone_' + milestone_id + '_indicator'
//         },
//         success: {
//             callback: function (json) {
//                 var must_reload_issue_list = false;
//                 if (json.percent) {
//                     Pachno.Main.updatePercentageLayout('milestone_' + m_id + '_percent', json.percent);
//                     delete json.percent;
//                 }
//                 for (var item in json)
//                 {
//                     var existing = $('#milestone_' + m_id + '_' + item);
//                     if (existing)
//                     {
//                         if (existing.innerHTML != json[item])
//                         {
//                             existing.html(json[item]);
//                             must_reload_issue_list = true;
//                         }
//                     }
//                 }
//                 if (must_reload_issue_list) {
//                     $('#milestone_' + m_id + '_changed').show();
//                     $('#milestone_' + m_id + '_issues').html('');
//                 }
//
//             }
//         }
//     });
// };
//
// Pachno.Project.Timeline.update = function (url) {
//     Pachno.Helpers.fetch(url, {
//         method: 'GET',
//         data: "offset=" + $('#timeline_offset').val(),
//         loading: {
//             indicator: '#timeline_indicator',
//             hide: 'timeline_more_link'
//         },
//         success: {
//             update: {element: 'timeline', insertion: true},
//             show: 'timeline_more_link',
//             callback: function (json) {
//                 $('#timeline_offset').value(json.offset)
//             }
//         }
//     });
// };
//
// Pachno.Project.showBranchCommits = function (url, branch) {
//     Pachno.Helpers.fetch(url, {
//         method: 'POST',
//         data: "branch=" + branch,
//         loading: {
//             indicator: '#fullpage_backdrop',
//             show: 'fullpage_backdrop_indicator',
//             hide: ['fullpage_backdrop_content', 'project_commits_box']
//         },
//         success: {
//             show: 'project_commits_box',
//             update: '#project_commits'
//         }
//     });
// };
//
// Pachno.Project.Commits.update = function (url, branch) {
//     Pachno.Helpers.fetch(url, {
//         method: 'POST',
//         data: "from_commit=" + $('#from_commit').val() + "&branch=" + branch,
//         loading: {
//             indicator: '#commits_indicator',
//             hide: 'commits_more_link'
//         },
//         success: {
//             update: {element: 'commits', insertion: true},
//             show: 'commits_more_link',
//             callback: function (json) {
//                 $('#from_commit').value(json.last_commit)
//             }
//         }
//     });
// };
//
// Pachno.Project.Commits.viewIssueUpdate = function (url) {
//     Pachno.Helpers.fetch(url, {
//         method: 'POST',
//         data: "offset=" + $('#commits_offset').val() + "&limit=" + $('#commits_limit').val(),
//         loading: {
//             indicator: '#commits_indicator',
//             hide: 'commits_more_link'
//         },
//         success: {
//             update: {element: 'viewissue_vcs_integration_commits', insertion: true}
//         }
//     });
// };
//
// Pachno.Project.Scrum.Sprint.add = function (url, assign_url)
// {
//     Pachno.Helpers.fetch(url, {
//         form: 'add_sprint_form',
//         loading: {indicator: '#sprint_add_indicator'},
//         success: {
//             reset: 'add_sprint_form',
//             hide: 'no_sprints',
//             update: {element: 'scrum_sprints', insertion: true}
//         }
//     });
// }
//
// Pachno.Project.Scrum.Story.setColor = function (url, story_id, color, event)
// {
//     event.stopPropagation();
//     Pachno.Helpers.fetch(url, {
//         params: {color: color},
//         loading: {indicator: '#color_selector_' + story_id + '_indicator'},
//         success: {
//             callback: function (json) {
//                 $('#story_color_' + story_id).style.backgroundColor = color;
//                 $('#story_color_' + story_id).style.color = json.text_color;
//                 $('.epic_badge').each(function (badge) {
//                     if (badge.data('parent-epic-id') == story_id) {
//                         badge.style.backgroundColor = color;
//                         badge.style.color = json.text_color;
//                     }
//                 });
//             }
//         },
//         complete: {
//             callback: function () {
//                 Pachno.Main.Profile.clearPopupsAndButtons();
//             }
//         }
//     });
// }
//
// Pachno.Project.updateLinks = function (json) {
//     if ($('#current_project_num_count'))
//         $('#current_project_num_count').html(json.total_count);
//     (json.more_available) ? $('#add_project_div').show() : $('#add_project_div').hide();
// }
//
// Pachno.Project.resetIcons = function (url) {
//     Pachno.Helpers.fetch(url, {
//         method: 'POST',
//         data: '&clear_icons=1'
//     });
// };
//
// Pachno.Project.initializeFilterSearch = function () {
//     var si = filter.down('input[type=search]');
//     if (si != undefined)
//     {
//         si.data('previous-value', '');
//         if (si.data('callback-url') !== undefined) {
//             var fk = filter.data('filter-key');
//             si.on('keyup', function (event, element) {
//                 if (Pachno.ift_observers[fk])
//                     clearTimeout(Pachno.ift_observers[fk]);
//                 if ((si.val().length >= 3 || si.val().length == 0) && si.val() != si.data('last-value')) {
//                     Pachno.ift_observers[fk] = setTimeout(function () {
//                         Pachno.Search.getFilterValues(si);
//                         si.data('last-value', si.val());
//                     }, 1000);
//                 }
//             });
//         } else {
//             si.on('keyup', Pachno.Search.filterFilterOptions);
//         }
//         si.on('click', function (event, element) {
//             event.stopPropagation();
//             event.preventDefault();
//         });
//         filter.addClass('searchable');
//     }
// };
//
// Pachno.Project.remove = function (url, pid) {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: '#fullpage_backdrop',
//             show: 'fullpage_backdrop_indicator',
//             hide: ['fullpage_backdrop_content', 'dialog_backdrop', 'project_delete_controls_' + pid]
//         },
//         success: {
//             remove: 'project_box_' + pid,
//             callback: function (json) {
//                 if ($('#project_table').children().length == 0)
//                     $('#noprojects_tr').show();
//                 if ($('#project_table_archived').children().length == 0)
//                     $('#noprojects_tr_archived').show();
//                 Pachno.Project.updateLinks(json);
//                 Pachno.Helpers.Dialog.dismiss();
//             }
//         },
//         failure: {
//             show: 'project_delete_error_' + pid
//         },
//         complete: {
//             show: 'project_delete_controls_' + pid
//         }
//     });
// }
//
// Pachno.Project.archive = function (url, pid) {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: '#project_' + pid + '_archive_indicator'
//         },
//         success: {
//             remove: 'project_box_' + pid,
//             hide: 'noprojects_tr_archived',
//             callback: function (json) {
//                 if ($('#project_table').children().length == 0)
//                     $('#noprojects_tr').show();
//                 $('#project_table_archived').prepend(json.box);
//                 Pachno.Helpers.Dialog.dismiss();
//             }
//         }
//     });
// }
//
// Pachno.Project.unarchive = function (url, pid) {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: '#project_' + pid + '_archive_indicator'
//         },
//         success: {
//             remove: 'project_box_' + pid,
//             hide: 'noprojects_tr',
//             callback: function (json) {
//                 if ($('#project_table_archived').children().length == 0)
//                     $('#noprojects_tr_archived').show();
//                 if (json.parent_id != 0) {
//                     $('#project_' + json.parent_id + '_children').append(json.box);
//                 } else {
//                     $('#project_table').append(json.box);
//                 }
//             }
//         },
//         failure: {
//             show: 'project_' + pid + '_unarchive'
//         }
//     });
// };
//
// Pachno.Project.Planning.initializeMilestoneDragDropSorting = function (milestone) {
//     var milestone_issues = $(milestone).find('.milestone-issues.jsortable');
//     if (milestone_issues.hasClass('ui-sortable')) {
//         milestone_issues.sortable('destroy');
//     }
//     milestone_issues.sortable({
//         handle: '.draggable',
//         connectWith: '.jsortable.intersortable',
//         update: Pachno.Project.Planning.sortMilestoneIssues,
//         receive: Pachno.Project.Planning.moveIssue,
//         sort: Pachno.Project.Planning.calculateNewBacklogMilestoneDetails,
//         start: function (event) {
//             $('.milestone-issues-container').each(function (index) {
//                 $(this).addClass('issue-drop-target');
//             })
//         },
//         stop: function (event) {
//             $('.milestone-issues-container').each(function (index) {
//                 $(this).removeClass('issue-drop-target');
//             })
//         },
//         over: function (event) { $(this).addClass('drop-hover'); },
//         out: function (event) { $(this).removeClass('drop-hover'); },
//         tolerance: 'pointer',
//         helper: function(event, ui) {
//             var $clone =  $(ui).clone();
//             $clone .css('position','absolute');
//             return $clone.get(0);
//         }
//     });
// };
//
// Pachno.Project.Planning.initializeReleaseDroptargets = function () {
//     $('#builds-list .release').not('ui-droppable').droppable({
//         drop: Pachno.Project.Planning.assignRelease,
//         accept: '.milestone-issue',
//         tolerance: 'pointer',
//         hoverClass: 'drop-hover'
//     });
// };
//
// Pachno.Project.Planning.initializeEpicDroptargets = function () {
//     $('#epics-list .epic').not('.ui-droppable').droppable({
//         drop: Pachno.Project.Planning.assignEpic,
//         accept: '.milestone-issue',
//         tolerance: 'pointer',
//         hoverClass: 'drop-hover'
//     });
// };
//
// Pachno.Project.Planning.toggleReleaseFilter = function (release) {
//     if (release !== 'auto' && $('#epics-list') && $('#epics-list').hasClass('filtered'))
//         Pachno.Project.Planning.toggleEpicFilter('auto');
//     if ($('#builds-list').hasClass('filtered') && (release == 'auto' || ($(release) && $(release).hasClass('selected')))) {
//         $('#builds-list').removeClass('filtered');
//         $('#builds-list').children().each(function (rel) {
//             rel.removeClass('selected');
//         });
//         $('.milestone-issue').each(function (issue) {
//             issue.removeClass('filtered');
//         });
//     } else if ($(release)) {
//         $('#builds-list').addClass('filtered');
//         $('#builds-list').children().each(function (rel) {
//             rel.removeClass('selected');
//         });
//         $(release).addClass('selected');
//         var release_id = $(release).data('release-id');
//         $('.milestone-issue').each(function (issue) {
//             (issue.data('release-' + release_id) === undefined) ? issue.addClass('filtered') : issue.removeClass('filtered');
//         });
//     }
//
//     Pachno.Project.Planning.calculateAllMilestonesVisibilityDetails();
// };
//
// Pachno.Project.Planning.toggleEpicFilter = function (epic) {
//     if (epic !== 'auto' && $('#builds-list') && $('#builds-list').hasClass('filtered'))
//         Pachno.Project.Planning.toggleReleaseFilter('auto');
//     if ($('#epics-list').hasClass('filtered') && (epic == 'auto' || ($(epic) && $(epic).hasClass('selected')))) {
//         $('#epics-list').removeClass('filtered');
//         $('#epics-list').children().each(function (ep) {
//             ep.removeClass('selected');
//         });
//         $('.milestone-issue').each(function (issue) {
//             issue.removeClass('filtered');
//         });
//     } else if ($(epic)) {
//         $('#epics-list').addClass('filtered');
//         $('#epics-list').children().each(function (ep) {
//             ep.removeClass('selected');
//         });
//         $(epic).addClass('selected');
//         var epic_id = $(epic).data('issue-id');
//         $('.milestone-issue').each(function (issue) {
//             (issue.data('parent-' + epic_id) === undefined) ? issue.addClass('filtered') : issue.removeClass('filtered');
//         });
//     }
//
//     Pachno.Project.Planning.calculateAllMilestonesVisibilityDetails();
// };
//
// Pachno.Project.Planning.toggleClosedIssues = function () {
//     $('#milestones-list').toggleClass('show_closed');
//     Pachno.Project.Planning.calculateAllMilestonesVisibilityDetails();
//     Pachno.Project.Planning.calculateNewBacklogMilestoneDetails();
//     Pachno.Main.Profile.clearPopupsAndButtons();
// };
//
// Pachno.Project.Planning.assignRelease = function (event, ui) {
//     var issue = $(ui.draggable[0]);
//     issue.data('sort-cancel', true);
//     if (issue.hasClass('milestone-issue')) {
//         var release = $(event.target);
//         var release_id = $(event.target).data('release-id');
//         var url = release.data('assign-issue-url');
//         Pachno.Helpers.fetch(url, {
//             data: 'issue_id=' + issue.data('issue-id'),
//             loading: {indicator: release.down('.planning_indicator')},
//             complete: {
//                 callback: function (json) {
//                     $('#release_' + release_id + '_percentage_filler').css({width: json.closed_pct + '%'});
//                     Pachno.Core.Pollers.Callbacks.planningPoller();
//                     issue.data('release-' + release_id, true);
//                 }
//             }
//         });
//     }
// };
//
// Pachno.Project.Planning.updateNewMilestoneIssues = function () {
//     var num_issues = $('.milestone-issue.included').length;
//     $('#milestone_include_num_issues').html(num_issues);
//     $('#milestone_include_issues').show();
//     $('#include_selected_issues').value(1);
// };
//
// Pachno.Project.Planning.addEpic = function (form) {
//     var url = form.action;
//     Pachno.Helpers.fetch(url, {
//         form: form,
//         loading: {indicator: '#new_epic_indicator'},
//         success: {
//             callback: function (json) {
//                 $(form).reset();
//                 $(form).parents('li').removeClass('selected');
//                 Pachno.Core.Pollers.Callbacks.planningPoller();
//             }
//         }
//     });
// };
//
// Pachno.Project.Planning.assignEpic = function (event, ui) {
//     var issue = $(ui.draggable[0]);
//     issue.data('sort-cancel', true);
//     if (issue.hasClass('milestone-issue')) {
//         var epic = $(event.target);
//         var epic_id = $(event.target).data('issue-id');
//         var url = epic.data('assign-issue-url');
//         Pachno.Helpers.fetch(url, {
//             data: 'issue_id=' + issue.data('issue-id'),
//             loading: {indicator: epic.down('.planning_indicator')},
//             complete: {
//                 callback: function (json) {
//                     $('#epic_' + epic_id + '_percentage_filler').css({width: json.closed_pct + '%'});
//                     $('#epic_' + epic_id + '_estimate').html(json.estimate);
//                     $('#epic_' + epic_id + '_child_issues_count').html(json.num_child_issues);
//                     issue.data('parent-' + epic_id, true);
//                     Pachno.Core.Pollers.Callbacks.planningPoller();
//                 }
//             }
//         });
//     }
// };
//
// Pachno.Project.Planning.destroyMilestoneDropSorting = function (milestone) {
//     if (milestone === undefined) {
//         $('.milestone-issues.ui-sortable').sortable('destroy');
//     } else {
//         $(milestone).find('.milestone-issues.ui-sortable').sortable('destroy');
//     }
// };
//
// Pachno.Project.Planning.getMilestoneIssues = function (milestone) {
//     if (milestone.hasClass('initialized')) {
//         return Promise.resolve();
//     }
//
//     let updateMilestoneIssuesContent = function (response) {
//         $('#milestone_' + milestone_id + '_issues').html(response.content);
//         return response;
//     };
//
//     let ti_button = milestone.down('.toggle-issues');
//
//     if (ti_button) {
//         ti_button.addClass('disabled');
//         ti_button.addClass('submitting');
//     }
//
//     var milestone_id = milestone.data('milestone-id');
//
//     return new Promise(function (resolve, reject) {
//         fetch(milestone.data('issues-url'))
//             .then((_) => _.json())
//             .then(updateMilestoneIssuesContent)
//             .then(function (response) {
//                 milestone.addClass('initialized');
//
//                 if (Pachno.Project.Planning.options.dragdrop) {
//                     Pachno.Project.Planning.initializeMilestoneDragDropSorting(milestone);
//                 }
//
//                 if (milestone.hasClass('available')) {
//                     var completed_milestones = $('.milestone-box.available.initialized');
//                     var multiplier = 100 / Pachno.Project.Planning.options.milestone_count;
//                     var pct = Math.floor(completed_milestones.length * multiplier);
//                     $('#planning_percentage_filler').css({width: pct + '%'});
//
//                     if (completed_milestones.length == (Pachno.Project.Planning.options.milestone_count - 1)) {
//                         $('#planning_loading_progress_indicator').hide();
//                         if (!Pachno.Core.Pollers.planningpoller)
//                             Pachno.Core.Pollers.planningpoller = new PeriodicalExecuter(Pachno.Core.Pollers.Callbacks.planningPoller, 15);
//
//                         $('#planning_indicator').hide();
//                         $('#planning_filter_title_input').prop('disabled', false);
//                     }
//                 }
//
//                 if (! milestone.down('.planning_indicator').hidden) milestone.down('.planning_indicator').hide();
//             })
//             .then(Pachno.Project.Planning.calculateAllMilestonesVisibilityDetails)
//             .then(function () {
//                 if (ti_button) {
//                     ti_button.removeClass('disabled');
//                     ti_button.removeClass('submitting');
//                 }
//
//                 resolve();
//             })
//             .catch(function (error) {
//                 milestone.addClass('initialized');
//                 milestone.find('.milestone_error_issues').each(Element.show);
//
//                 reject(error);
//             });
//     });
// };
//
// Pachno.Project.Planning.Whiteboard.addColumn = function(button) {
//     Pachno.Helpers.fetch(button.data('url'), {
//         loading: {
//             indicator: '#planning_indicator'
//         },
//         method: 'POST',
//         success: {
//             callback: function(json) {
//                 $('#planning_whiteboard_columns_form_row').append(json.component);
//                 Pachno.Project.Planning.Whiteboard.setSortOrder();
//             }
//         }
//     });
// };
//
// Pachno.Project.Planning.Whiteboard.toggleEditMode = function() {
//     $('#project_planning').toggleClass('edit-mode');
//     var $onboarding = $('#onboarding-no-board-columns');
//     if ($onboarding) {
//         $onboarding.hide();
//     }
//     Pachno.Main.Profile.clearPopupsAndButtons();
// };
//
// Pachno.Project.Planning.Whiteboard.saveColumns = function() {
//     var url = $('#planning_whiteboard_columns_form').action;
//
//     $('#planning_indicator').show();
//     Pachno.Helpers.fetch(url, {
//         method: 'POST',
//         form: 'planning_whiteboard_columns_form',
//         failure: {
//             hide: 'planning_indicator'
//         }
//     });
// };
//
// Pachno.Project.Planning.Whiteboard.calculateColumnCounts = function() {
//     $('##whiteboard-content .td').each(function (column, index) {
//         var counts = 0;
//         var status_counts = [];
//         column.find('.status-badge').each(function (status) {
//             status_counts[parseInt(status.dataset.statusId)] = 0;
//         });
//         $('##whiteboard .tbody .tr').each(function (row) {
//             row.children().each(function (subcolumn, subindex) {
//                 if (subindex == index) {
//                     var issues = subcolumn.find('.whiteboard-issue');
//                     issues.each(function (issue) {
//                         status_counts[parseInt(issue.dataset.statusId)]++;
//                     });
//                     counts += issues.length;
//                 }
//             });
//         });
//         if (column.down('.column_count.primary')) column.down('.column_count.primary').html(counts);
//         if (column.down('.column_count .count')) column.down('.column_count .count').html(counts);
//         column.find('.status-badge').each(function (status) {
//             status.html(status_counts[parseInt(status.dataset.statusId)]);
//         });
//         if ($('#project_planning').hasClass('type-kanban')) {
//             var min_wi = parseInt(column.dataset.minWorkitems);
//             var max_wi = parseInt(column.dataset.maxWorkitems);
//             if (min_wi !== 0 && counts < min_wi) {
//                 column.down('.under_count').html(counts);
//                 column.removeClass('over-workitems');
//                 column.addClass('under-workitems');
//                 $('##whiteboard .tbody .tr').each(function (row) {
//                     row.children().each(function (subcolumn, subindex) {
//                         if (!subcolumn.hasClass('swimlane-header') && subindex == index) {
//                             subcolumn.removeClass('over-workitems');
//                             subcolumn.addClass('under-workitems');
//                         }
//                     });
//                 });
//             }
//             if (max_wi !== 0 && counts > max_wi) {
//                 column.down('.over_count').html(counts);
//                 column.removeClass('under-workitems');
//                 column.addClass('over-workitems');
//                 $('##whiteboard .tbody .tr').each(function (row) {
//                     row.children().each(function (subcolumn, subindex) {
//                         if (!subcolumn.hasClass('swimlane-header') && subindex == index) {
//                             subcolumn.removeClass('under-workitems');
//                             subcolumn.addClass('over-workitems');
//                         }
//                     });
//                 });
//             }
//         }
//     });
// }
//
// Pachno.Project.Planning.Whiteboard.calculateSwimlaneCounts = function(new_issue_retrieved) {
//     var new_issue_retrieved = new_issue_retrieved || false;
//
//     $('##whiteboard .tbody').each(function (swimlane) {
//         swimlane_rows = swimlane.find('.tr');
//
//         if (swimlane_rows.length != 2) return;
//
//         swimlane_rows[0].down('.swimlane_count').html(swimlane_rows[1].find('.whiteboard-issue').length);
//
//         if (swimlane_rows[1].find('.whiteboard-issue').length == 0) {
//             swimlane.addClass('collapsed');
//         }
//         else if (new_issue_retrieved && swimlane_rows[1].find('.whiteboard-issue').length > 0) {
//             swimlane.removeClass('collapsed');
//         }
//     });
// }
//
// Pachno.Project.Planning.Whiteboard.retrieveWhiteboard = function() {
//     var wb = $('#whiteboard');
//     if (!wb) {
//         $('#whiteboard_indicator').hide();
//         return;
//     }
//
//     wb.removeClass('initialized');
//     var mi = $('#selected_milestone_input');
//     var milestone_id = (mi.dataset.selectedValue) ? parseInt(mi.dataset.selectedValue) : 0;
//
//     Pachno.Helpers.fetch(wb.dataset.whiteboardUrl, {
//         data: '&milestone_id=' + milestone_id,
//         method: 'GET',
//         loading: {
//             indicator: '#whiteboard_indicator',
//             callback: function() {
//                 $('#whiteboard').find('.thead .column_count.primary').each(function (cc) {
//                     cc.html('-');
//                 });
//                 wb.data('milestone-id', milestone_id);
//             }
//         },
//         success: {
//             callback: function(json) {
//                 if (json.swimlanes) {
//                     wb.removeClass('no-swimlanes');
//                     wb.addClass('swimlanes');
//                 }
//                 else {
//                     wb.removeClass('swimlanes');
//                     wb.addClass('no-swimlanes');
//                 }
//                 wb.addClass('initialized');
//                 wb.find('.tbody').each(Element.remove);
//                 $('#whiteboard-content').append(json.component);
//                 setTimeout(function () {
//                     Pachno.Project.Planning.Whiteboard.calculateColumnCounts();
//                     Pachno.Project.Planning.Whiteboard.calculateSwimlaneCounts();
//                     Pachno.Project.Planning.Whiteboard.initializeDragDrop();
//                 }, 250);
//             }
//         }
//     });
// };
//
// Pachno.Project.Planning.Whiteboard.retrieveMilestoneStatus = function(event, item) {
//     var mi = $('#selected_milestone_input');
//     var milestone_id = (event) ? $(item).dataset.inputValue : mi.dataset.selectedValue;
//     var board_id = (event) ? $(item).dataset.boardValue : mi.dataset.selectedBoardValue;
//     Pachno.Helpers.fetch(mi.dataset.statusUrl, {
//         data: '&milestone_id=' + parseInt(milestone_id) + '&board_id=' + parseInt(board_id),
//         method: 'GET',
//         loading: {
//             hide: 'selected_milestone_status_details',
//             indicator: '#selected_milestone_status_indicator'
//         },
//         success: {
//             update: '#selected_milestone_status_details',
//             show: 'selected_milestone_status_details',
//             callback: function () {
//                 $('#reportissue_button').data('milestone-id', milestone_id);
//             }
//         }
//     });
// };
//
// Pachno.Project.Planning.Whiteboard.setSortOrder = function() {
//     $('#planning_whiteboard_columns_form_row').children().each(function(column, index) {
//         column.down('input.sortorder').value(index + 1);
//     });
// };
//
// Pachno.Project.Planning.Whiteboard.updateIssueColumn = function(event, issue, column, startCoordinates) {
//     Pachno.Project.Planning.Whiteboard.moveIssueColumn(issue, column, undefined, undefined, undefined, startCoordinates);
// };
//
// Pachno.Project.Planning.Whiteboard.moveIssueColumn = function(issue, column, transition_id, original_column, issue_index, startCoordinates) {
//     if (! original_column) var original_column = issue.parents('.column');
//     if (! issue_index) var issue_index = issue.index();
//
//     if (issue) {
//         issue.detach().css({left: '0', top: '0', transform: 'inherit'}).prependTo(column);
//     }
//
//     var wb = $('#whiteboard');
//     var parameters = '&issue_id=' + parseInt(issue.data('issue-id')) + '&column_id=' + parseInt(column.data('column-id')) + '&milestone_id=' + parseInt($('#selected_milestone_input').data('selected-value')) + '&swimlane_identifier=' + issue.parents('.tbody').data('swimlane-identifier');
//     var revertIssuePosition = function () {
//         TweenMax.to(issue, .3, startCoordinates);
//
//         if (issue_index <= 0) {
//             issue.prependTo(original_column);
//         }
//         else {
//             issue.insertAfter(original_column.children().eq(issue_index - 1));
//         }
//     };
//     var customEscapeWatcher = function (event) {
//         if (event.keyCode != undefined && event.keyCode != 0 && Event.KEY_ESC != event.keyCode) return;
//         Pachno.Helpers.Backdrop.reset(revertIssuePosition);
//         if ($('#workflow_transition_fullpage')) $('#workflow_transition_fullpage').hide();
//         setTimeout(function() {
//             document.stopObserving('keydown', customEscapeWatcher);
//             $(document).on('keydown', Pachno.Core._escapeWatcher);
//         }, 350);
//     };
//
//     if (transition_id) parameters += '&transition_id=' + transition_id;
//
//     Pachno.Helpers.fetch($('#whiteboard').dataset.whiteboardUrl, {
//         data: parameters,
//         method: 'POST',
//         loading: {
//             indicator: '#fullpage_backdrop',
//             show: 'fullpage_backdrop_indicator',
//             hide: ['fullpage_backdrop_content', 'dialog_backdrop']
//         },
//         success: {
//             callback: function(json) {
//                 if (json.transition_id && json.component) {
//                     document.stopObserving('keydown', Pachno.Core._escapeWatcher);
//                     $(document).on('keydown', customEscapeWatcher);
//                     $('#fullpage_backdrop').show();
//                     $('#fullpage_backdrop_content').html(json.component);
//                     $('#fullpage_backdrop_content').show();
//                     $('#fullpage_backdrop_indicator').fade({duration: 0.2});
//                     Pachno.Issues.showWorkflowTransition(json.transition_id);
//                     $('#transition_working_' + json.transition_id + '_cancel').on('click', function (event) {
//                         Event.stop(event);
//                         customEscapeWatcher(event);
//                     });
//                     $('#transition_working_' + json.transition_id + '_submit').on('click', function (event) {
//                         Event.stop(event);
//                         Pachno.Issues.submitWorkflowTransition($('#workflow_transition_' + json.transition_id + '_form'), function () {
//                             Pachno.Core.Pollers.Callbacks.whiteboardPlanningPoller();
//                         });
//                     });
//                 } else if (json.component) {
//                     document.stopObserving('keydown', Pachno.Core._escapeWatcher);
//                     $(document).on('keydown', customEscapeWatcher);
//                     $('#fullpage_backdrop').show();
//                     $('#fullpage_backdrop_content').html(json.component);
//                     $('#fullpage_backdrop_content').show();
//                     $('#fullpage_backdrop_indicator').fade({duration: 0.2});
//                     $('#transition-selector-close-link').on('click', customEscapeWatcher);
//                     $('.transition-selector-button').each(function (elem) {
//                         elem.observe('click', function (event) {
//                             Pachno.Project.Planning.Whiteboard.moveIssueColumn($('#whiteboard_issue_' + elem.data('issue-id')), $('#swimlane_' + elem.dataset.swimlaneIdentifier + '_column_' + elem.data('column-id')), elem.dataset.transitionId, original_column, issue_index, startCoordinates);
//                         });
//                     });
//                 } else {
//                     $('#fullpage_backdrop_content').html('');
//                     $('#fullpage_backdrop').fade({duration: 0.2});
//                     if (!issue) {
//                         $(json.issue).prependTo(column);
//                     }
//                     Pachno.Core.Pollers.Callbacks.whiteboardPlanningPoller();
//                 }
//             }
//         },
//         failure: {
//             show: issue,
//             callback: function(json) {
//                 if (json.error != undefined && typeof(json.error) == 'string' && json.error.length) {
//                     revertIssuePosition();
//                 }
//             }
//         }
//     });
//
// };
//
// Pachno.Project.Planning.Whiteboard.resetAvailableDropColumns = function(event) {
//     $('.column.drop-valid').each(function (index) {
//         $(this).removeClass('drop-valid');
//         $(this).removeClass('drop-hover');
//     });
// };
//
// Pachno.Project.Planning.Whiteboard.detectAvailableDropColumns = function(event, issue) {
//     var issue = $(issue);
//     var issue_statuses = issue.dataset.validStatusIds.split(',');
//     issue.parents('.row').children().each(function (column) {
//         var column_statuses = column.dataset.statusIds.split(',');
//         var has_status = false;
//         issue_statuses.each(function (status) {
//             if (column_statuses.indexOf(status) != -1) {
//                 has_status = true;
//             }
//         });
//
//         if (!has_status) {
//             $(column).removeClass('gs-droppable');
//         } else {
//             column.addClass('drop-valid');
//             column.addClass('gs-droppable');
//         }
//     });
// };
//
// Pachno.Project.Planning.Whiteboard.initializeDragDrop = function () {
//     if ($('.whiteboard-issue').length > 0) {
//         var overlapThreshold = '30%';
//         var droppablesSelector = '.gs-droppable';
//         GSDraggable.create($('.whiteboard-issue'), {
//             type: 'x',
//             bounds: $('#whiteboard'),
//             onPress: function() {
//                 this.startX = this.x;
//                 this.startY = this.y;
//             },
//             onDragStart: function(ev) {
//                 $(this.target).addClass('gs-draggable');
//                 Pachno.Project.Planning.Whiteboard.detectAvailableDropColumns(ev, this.target);
//             },
//             onDrag: function(ev) {
//                 var droppables = $(droppablesSelector);
//                 var i = droppables.length;
//                 while (--i > -1) {
//                     if (this.hitTest(droppables[i], overlapThreshold)) {
//                         $(droppables[i]).addClass('drop-hover');
//                     } else {
//                         $(droppables[i]).removeClass('drop-hover');
//                     }
//                 }
//             },
//             onDragEnd:function(ev) {
//                 $(this.target).removeClass('gs-draggable');
//                 var droppables = $(droppablesSelector);
//                 var i = droppables.length;
//                 var column_found = false;
//                 while (--i > -1) {
//                     if (this.hitTest(droppables[i], overlapThreshold)) {
//                         Pachno.Project.Planning.Whiteboard.updateIssueColumn(ev, $(this.target), $(droppables[i]), {x: this.startX, y: this.startY});
//                         column_found = true;
//                     }
//                 }
//                 if (! column_found) TweenMax.to(this.target, .3, {x: this.startX, y: this.startY});
//                 Pachno.Project.Planning.Whiteboard.resetAvailableDropColumns(ev);
//             },
//             zIndexBoost: false
//         });
//         var highZIndex = 1010;
//         $('#whiteboard .whiteboard-issue').each(function () {
//             $(this).css('z-index', highZIndex--);
//         });
//     }
//
//     if (!Pachno.Core.Pollers.planningpoller)
//         Pachno.Core.Pollers.planningpoller = new PeriodicalExecuter(Pachno.Core.Pollers.Callbacks.whiteboardPlanningPoller, 6);
// };
//
// Pachno.Project.Planning.Whiteboard.retrieveIssue = function (issue_id, url, existing_element) {
//     var milestone_id = $('#whiteboard').data('milestone-id');
//     var swimlane_type = $('#whiteboard').dataset.swimlaneType;
//     var column_id = ($(existing_element) != null && $(existing_element).data('column-id') != undefined) ? $(existing_element).data('column-id') : '';
//
//     if ($(existing_element) != null) {
//         if ($(existing_element).hasClass('tbody')) {
//             var swimlane_identifier = $(existing_element).dataset.swimlaneIdentifier;
//         }
//         else {
//             var swimlane_identifier = $(existing_element).parents('.tbody').dataset.swimlaneIdentifier;
//         }
//     }
//     else {
//         var swimlane_identifier = $('#whiteboard').down('.tbody').dataset.swimlaneIdentifier;
//     }
//
//     Pachno.Helpers.fetch(url, {
//         params: 'issue_id=' + issue_id + '&milestone_id=' + milestone_id + '&swimlane_type=' + swimlane_type + '&column_id=' + column_id + '&swimlane_identifier=' + swimlane_identifier,
//         method: 'GET',
//         loading: {indicator: (!$(existing_element)) ? 'retrieve_indicator' : 'issue_' + issue_id + '_indicator'},
//         success: {
//             callback: function (json) {
//                 if (swimlane_type != json.swimlane_type) {
//                     Pachno.Project.Planning.Whiteboard.retrieveMilestoneStatus();
//                     Pachno.Project.Planning.Whiteboard.retrieveWhiteboard();
//                     return;
//                 }
//                 if (json.deleted == '1') {
//                     if ($(existing_element)) $(existing_element).remove();
//                 }
//                 else if (!$(existing_element)) {
//                     if (json.issue_details.milestone && json.issue_details.milestone.id == milestone_id && json.component != '') {
//                         if ($('#whiteboard').hasClass('initialized')) {
//                             if ($('#swimlane_'+json.swimlane_identifier+'_column_'+json.column_id)) {
//                                 $('#swimlane_'+json.swimlane_identifier+'_column_'+json.column_id).prepend(json.component);
//                             } else {
//                                 if (json.child_issue == '0') {
//                                     $('#whiteboard-content').append(json.component);
//                                 }
//                             }
//                             Pachno.Project.Planning.Whiteboard.initializeDragDrop();
//                             Pachno.Project.Planning.Whiteboard.calculateColumnCounts();
//                             Pachno.Project.Planning.Whiteboard.calculateSwimlaneCounts(true);
//                             Pachno.Project.Planning.Whiteboard.retrieveMilestoneStatus();
//                         }
//                     }
//                 } else {
//                     var json_milestone_id = (json.issue_details.milestone && json.issue_details.milestone.id != undefined) ? parseInt(json.issue_details.milestone.id) : 0;
//                     if (json_milestone_id == 0 || json.component == '') {
//                         $(existing_element).remove();
//                         Pachno.Project.Planning.Whiteboard.calculateColumnCounts();
//                         Pachno.Project.Planning.Whiteboard.calculateSwimlaneCounts();
//                         Pachno.Project.Planning.Whiteboard.retrieveMilestoneStatus();
//                     } else if (json_milestone_id != milestone_id || json.swimlane_identifier != swimlane_identifier || json.column_id != column_id) {
//                         $(existing_element).remove();
//                         if ($('#whiteboard').hasClass('initialized')) {
//                             if ($('#swimlane_'+json.swimlane_identifier+'_column_'+json.column_id)) {
//                                 $('#swimlane_'+json.swimlane_identifier+'_column_'+json.column_id).prepend(json.component);
//                             } else {
//                                 if (json.child_issue == '0') {
//                                     $('#whiteboard-content').append(json.component);
//                                 }
//                             }
//                             Pachno.Project.Planning.Whiteboard.initializeDragDrop();
//                         }
//                         Pachno.Project.Planning.Whiteboard.calculateColumnCounts();
//                         Pachno.Project.Planning.Whiteboard.calculateSwimlaneCounts();
//                         Pachno.Project.Planning.Whiteboard.retrieveMilestoneStatus();
//                     } else {
//                         $(existing_element).replace(json.component);
//                         Pachno.Project.Planning.Whiteboard.initializeDragDrop();
//                         Pachno.Project.Planning.Whiteboard.calculateColumnCounts();
//                         Pachno.Project.Planning.Whiteboard.calculateSwimlaneCounts();
//                         Pachno.Project.Planning.Whiteboard.retrieveMilestoneStatus();
//                     }
//                 }
//             }
//         }
//     });
// };
//
// Pachno.Core.Pollers.Callbacks.whiteboardPlanningPoller = function () {
//     if (!Pachno.Core.Pollers.Locks.planningpoller && $('#whiteboard').hasClass('initialized')) {
//         Pachno.Core.Pollers.Locks.planningpoller = true;
//         var pc = $('#project_planning');
//         var wb = $('#whiteboard');
//         var data_url = pc.dataset.pollUrl;
//         var retrieve_url = pc.dataset.retrieveIssueUrl;
//         var last_refreshed = pc.dataset.lastRefreshed;
//         Pachno.Helpers.fetch(data_url, {
//             method: 'GET',
//             params: 'last_refreshed=' + last_refreshed + '&milestone_id=' + wb.data('milestone-id'),
//             success: {
//                 callback: function (json) {
//                     if (parseInt(json.milestone_id) == parseInt(wb.data('milestone-id'))) {
//                         for (var i in json.ids) {
//                             if (json.ids.hasOwnProperty(i)) {
//                                 var issue_details = json.ids[i];
//                                 var issue_element = $('#whiteboard_issue_' + issue_details.issue_id);
//                                 if (!issue_element || parseInt(issue_element.dataset.lastUpdated) < parseInt(issue_details.last_updated)) {
//                                     Pachno.Project.Planning.Whiteboard.retrieveIssue(issue_details.issue_id, retrieve_url, 'whiteboard_issue_' + issue_details.issue_id);
//                                 }
//                             }
//                         }
//                         for (var i in json.backlog_ids) {
//                             if (json.backlog_ids.hasOwnProperty(i)) {
//                                 var issue_details = json.backlog_ids[i];
//                                 var issue_element = $('#whiteboard_issue_' + issue_details.issue_id);
//                                 if (!issue_element || parseInt(issue_element.dataset.lastUpdated) < parseInt(issue_details.last_updated)) {
//                                     Pachno.Project.Planning.Whiteboard.retrieveIssue(issue_details.issue_id, retrieve_url, 'whiteboard_issue_' + issue_details.issue_id);
//                                 }
//                             }
//                         }
//                     }
//
//                     pc.dataset.lastRefreshed = get_current_timestamp();
//                     wb.dataset.whiteboardUrl = json.whiteboard_url;
//                     Pachno.Core.Pollers.Locks.planningpoller = false;
//                 }
//             },
//             exception: {
//                 callback: function () {
//                     Pachno.Core.Pollers.Locks.planningpoller = false;
//                 }
//             }
//         });
//     }
// };
//
// Pachno.Project.Planning.Whiteboard.checkNav = function() {
//     if (window.location.hash) {
//         if (parseInt($('#selected_milestone_input').dataset.selectedValue) != parseInt(window.location.hash)) {
//             var hasharray = window.location.hash.substr(1).split('/');
//             var milestone_id = parseInt(hasharray[0]);
//             $('#selected_milestone_input').children().each(function(milestone_li) {
//                 if (parseInt(milestone_li.dataset.inputValue) == milestone_id) {
//                     Pachno.Main.setFancyDropdownValue(milestone_li);
//                     setTimeout(function () {
//                         Pachno.Project.Planning.Whiteboard.retrieveMilestoneStatus();
//                         Pachno.Project.Planning.Whiteboard.retrieveWhiteboard();
//                     }, 150);
//                 }
//             });
//         }
//     }
// }
//
// Pachno.Project.Planning.Whiteboard.initialize = function (options) {
//     $('body').on('click', '#selected_milestone_input li', Pachno.Project.Planning.Whiteboard.retrieveMilestoneStatus);
//     $(window).on('hashchange', Pachno.Project.Planning.Whiteboard.checkNav);
//     Pachno.Project.Planning._initializeFilterSearch(true);
//     if (window.location.hash) {
//         Pachno.Project.Planning.Whiteboard.checkNav();
//     } else {
//         Pachno.Project.Planning.Whiteboard.retrieveMilestoneStatus();
//         Pachno.Project.Planning.Whiteboard.retrieveWhiteboard();
//     }
//
//     $('#planning_whiteboard_columns_form_row').sortable({
//         handle: '.draggable',
//         axis: 'x',
//         update: Pachno.Project.Planning.Whiteboard.setSortOrder
//     });
//
//     $('#planning_indicator').hide();
//     $('#planning_filter_title_input').prop('disabled', false);
// };
//
// Pachno.Project.Planning._initializeFilterSearch = function(whiteboard) {
//     Pachno.ift_observers = {};
//     var pfti = $('#planning_filter_title_input');
//     pfti.data('previous-value', '');
//     var fk = 'pfti';
//     if (whiteboard == undefined) whiteboard = false;
//     pfti.on('keyup', function (event, element) {
//         if (Pachno.ift_observers[fk])
//             clearTimeout(Pachno.ift_observers[fk]);
//         if ((pfti.val().length >= 3 || pfti.val().length == 0) && pfti.val() != pfti.data('last-value')) {
//             Pachno.ift_observers[fk] = setTimeout(function () {
//                 Pachno.Project.Planning.filterTitles(pfti.val(), whiteboard);
//                 pfti.data('last-value', pfti.val());
//             }, 500);
//         }
//     });
// };
//
// Pachno.Project.Planning.toggleMilestoneIssues = function(milestone_id) {
//     var mi_issues = $('#milestone_'+milestone_id+'_issues');
//     var mi = $('#milestone_'+milestone_id);
//     mi.down('.toggle-issues').toggleClass('button-pressed');
//     if (!mi.hasClass('initialized')) {
//         mi.down('.toggle-issues').prop('disabled', true);
//         mi_issues.removeClass('collapsed');
//         Pachno.Project.Planning.getMilestoneIssues(mi);
//     } else {
//         $('#milestone_'+milestone_id+'_issues').toggleClass('collapsed');
//     }
// };
//
// Pachno.Project.Planning.toggleMilestoneSorting = function() {
//     if ($('#project_planning').hasClass('milestone-sort')) {
//         $('#project_planning').removeClass('milestone-sort left_toggled');
//         $('#milestones-list').sortable("destroy");
//         $('.milestone-issues.ui-sortable').sortable('enable');
//     } else {
//         $('#project_planning').addClass('milestone-sort left_toggled');
//
//         $('.milestone-issues.ui-sortable').sortable('disable');
//
//         $('#milestones-list').sortable({
//             update: Pachno.Project.Planning.sortMilestones,
//             axis: 'y',
//             items: '> .milestone-box',
//             helper: 'original',
//             tolerance: 'intersect'
//         });
//     }
// };
//
// Pachno.Project.Planning.initialize = function (options) {
//     Pachno.Project.Planning.options = options;
//
//     $('.milestone-box.unavailable').each(Pachno.Project.Planning.initializeMilestoneDragDropSorting);
//     var milestone_boxes = $('.milestone-box.available');
//     Pachno.Project.Planning.options.milestone_count = milestone_boxes.length + 1;
//     milestone_boxes.each(Pachno.Project.Planning.getMilestoneIssues);
//
//     Pachno.Project.Planning._initializeFilterSearch();
//
//     if ($('#epics-list')) {
//         Pachno.Helpers.fetch($('#epics-list').dataset.epicsUrl, {
//             method: 'GET',
//             success: {
//                 update: '#epics-list',
//                 callback: function (json) {
//                     var completed_milestones = $('.milestone-box.available.initialized');
//                     var multiplier = 100 / Pachno.Project.Planning.options.milestone_count;
//                     var pct = Math.floor((completed_milestones.length + 1) * multiplier);
//                     $('#planning_percentage_filler').css({width: pct + '%'});
//
//                     $('#epics_toggler_button').prop('disabled', false);
//                     Pachno.Project.Planning.initializeEpicDroptargets();
//                     $('body').on('click', '.epic', function (e) {
//                         Pachno.Project.Planning.toggleEpicFilter(this);
//                     });
//                 }
//             }
//         });
//     }
//
//     if ($('#builds-list')) {
//         Pachno.Helpers.fetch($('#builds-list').dataset.releasesUrl, {
//             method: 'GET',
//             success: {
//                 update: '#builds-list',
//                 callback: function (json) {
//                     Pachno.Project.Planning.initializeReleaseDroptargets();
//                     $('body').on('click', '.release', function (e) {
//                         Pachno.Project.Planning.toggleReleaseFilter(this);
//                     });
//                 }
//             }
//         });
//     }
// };
//
// Pachno.Project.Planning.filterTitles = function (title, whiteboard) {
//     $('#planning_indicator').show();
//     if (title !== '') {
//         var matching = new RegExp(title, "i");
//         $('#project_planning').addClass('issue_title_filtered');
//         $(whiteboard ? '.whiteboard-issue' : '.milestone-issue').each(function (issue) {
//             if (whiteboard) {
//                 if (issue.down('.issue_header').innerHTML.search(matching) !== -1) {
//                     issue.addClass('title_unfiltered');
//                 } else {
//                     issue.removeClass('title_unfiltered');
//                 }
//             }
//             else {
//                 if (issue.down('.issue_link').down('a').innerHTML.search(matching) !== -1) {
//                     issue.addClass('title_unfiltered');
//                 } else {
//                     issue.removeClass('title_unfiltered');
//                 }
//             }
//         });
//     } else {
//         $('#project_planning').removeClass('issue_title_filtered');
//         $(whiteboard ? '.whiteboard-issue' : '.milestone-issue').each(function (issue) {
//             issue.removeClass('title_unfiltered');
//         });
//     }
//     $('#planning_indicator').hide();
// };
//
// Pachno.Project.Planning.insertIntoMilestone = function (milestone_id, content, recalculate) {
//     var milestone_list = $('#milestone_' + milestone_id + '_issues');
//     var $milestone_list_container = milestone_list.parents('.milestone-issues-container');
//     $milestone_list_container.removeClass('empty');
//     $('#milestone_' + milestone_id + '_unassigned').hide();
//     if (milestone_id == 0) {
//         milestone_list.append(content);
//     } else {
//         milestone_list.prepend(content);
//     }
//     if (recalculate == 'all') {
//         Pachno.Project.Planning.calculateAllMilestonesVisibilityDetails();
//     } else {
//         Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails(milestone_list);
//     }
//     Pachno.Project.Planning.calculateNewBacklogMilestoneDetails();
//     if (milestone_id != 0) {
//         setTimeout(Pachno.Project.Planning.sortMilestoneIssues({target: 'milestone_' + milestone_id + '_issues'}), 250);
//     }
// };
//
// Pachno.Project.Planning.retrieveIssue = function (issue_id, url, existing_element) {
//     Pachno.Helpers.fetch(url, {
//         params: 'issue_id=' + issue_id,
//         method: 'GET',
//         loading: {indicator: (!$(existing_element)) ? 'retrieve_indicator' : 'issue_' + issue_id + '_indicator'},
//         success: {
//             callback: function (json) {
//                 if (json.deleted == '1') {
//                     if ($(existing_element)) $(existing_element).parents('.milestone-issue').remove();
//                 }
//                 else if (json.epic) {
//                     if (!$(existing_element)) {
//                         $('#add_epic_container').prepend(json.component);
//                         setTimeout(Pachno.Project.Planning.initializeEpicDroptargets, 250);
//                     } else {
//                         $(existing_element).parents('.milestone-issue').replace(json.component);
//                     }
//                 } else {
//                     if (!$(existing_element)) {
//                         if (json.issue_details.milestone && json.issue_details.milestone.id) {
//                             if ($('#milestone_'+json.issue_details.milestone.id).hasClass('initialized')) {
//                                 Pachno.Project.Planning.insertIntoMilestone(json.issue_details.milestone.id, json.component);
//                             }
//                         } else {
//                             Pachno.Project.Planning.insertIntoMilestone(0, json.component);
//                         }
//                     } else {
//                         var json_milestone_id = (json.issue_details.milestone && json.issue_details.milestone.id != undefined) ? parseInt(json.issue_details.milestone.id) : 0;
//                         if (parseInt($(existing_element).parents('.milestone-box').data('milestone-id')) == json_milestone_id) {
//                             $(existing_element).parents('.milestone-issue').replace(json.component);
//                             Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails($('#milestone_' + json_milestone_id + '_issues'));
//                             Pachno.Project.Planning.calculateNewBacklogMilestoneDetails();
//                         } else {
//                             $(existing_element).parents('.milestone-issue').remove();
//                             Pachno.Project.Planning.insertIntoMilestone(json_milestone_id, json.component, 'all');
//                         }
//                     }
//                 }
//                 if (json.issue_details.milestone && json.issue_details.milestone.id && json.milestone_percent_complete != null) {
//                     $('#milestone_' + json.issue_details.milestone.id + '_percentage_filler').css({width: json.milestone_percent_complete + '%'});
//                 }
//                 Pachno.Project.Planning.filterTitles($('#planning_filter_title_input').val());
//             }
//         }
//     });
// };
//
// Pachno.Core.Pollers.Callbacks.planningPoller = function () {
//     var pc = $('#project_planning');
//     if (!Pachno.Core.Pollers.Locks.planningpoller && pc) {
//         Pachno.Core.Pollers.Locks.planningpoller = true;
//         var data_url = pc.dataset.pollUrl;
//         var retrieve_url = pc.dataset.retrieveIssueUrl;
//         var last_refreshed = pc.dataset.lastRefreshed;
//         Pachno.Helpers.fetch(data_url, {
//             method: 'GET',
//             params: 'last_refreshed=' + last_refreshed,
//             success: {
//                 callback: function (json) {
//                     pc.dataset.lastRefreshed = get_current_timestamp();
//                     for (var i in json.ids) {
//                         if (json.ids.hasOwnProperty(i)) {
//                             var issue_details = json.ids[i];
//                             var issue_element = $('#issue_' + issue_details.issue_id);
//                             if (!issue_element || parseInt(issue_element.dataset.lastUpdated) < parseInt(issue_details.last_updated)) {
//                                 Pachno.Project.Planning.retrieveIssue(issue_details.issue_id, retrieve_url, 'issue_' + issue_details.issue_id);
//                             }
//                         }
//                     }
//                     for (var i in json.backlog_ids) {
//                         if (json.backlog_ids.hasOwnProperty(i)) {
//                             var issue_details = json.backlog_ids[i];
//                             var issue_element = $('#issue_' + issue_details.issue_id);
//                             if (!issue_element || parseInt(issue_element.dataset.lastUpdated) < parseInt(issue_details.last_updated)) {
//                                 Pachno.Project.Planning.retrieveIssue(issue_details.issue_id, retrieve_url, 'issue_' + issue_details.issue_id);
//                             }
//                         }
//                     }
//                     for (var i in json.epic_ids) {
//                         if (json.epic_ids.hasOwnProperty(i)) {
//                             var issue_details = json.epic_ids[i];
//                             var issue_element = $('#epic_' + issue_details.issue_id);
//                             if (!issue_element || parseInt(issue_element.dataset.lastUpdated) < parseInt(issue_details.last_updated)) {
//                                 Pachno.Project.Planning.retrieveIssue(issue_details.issue_id, retrieve_url, 'epic_' + issue_details.issue_id);
//                             }
//                         }
//                     }
//                     Pachno.Core.Pollers.Locks.planningpoller = false;
//                 }
//             },
//             exception: {
//                 callback: function () {
//                     Pachno.Core.Pollers.Locks.planningpoller = false;
//                 }
//             }
//         });
//     }
// };
//
// Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails = function (list) {
//     var list_issues = $(list).find('.issue-container').not('.child_issue');
//     var closed_issues = $(list).find('.issue-container.issue_closed').not('.child_issue');
//     var visible_issues = list_issues.filter(':visible');
//     var sum_estimated_points = 0;
//     var sum_estimated_hours = 0;
//     var sum_estimated_minutes = 0;
//     var sum_spent_points = 0;
//     var sum_spent_hours = 0;
//     var sum_spent_minutes = 0;
//     visible_issues.each(function (index) {
//         var elm = $(this);
//         if (!elm.hasClass('child_issue')) {
//             if (elm.dataset.estimatedPoints !== undefined)
//                 sum_estimated_points += parseInt(elm.dataset.estimatedPoints);
//             if (elm.dataset.estimatedHours !== undefined)
//                 sum_estimated_hours += parseInt(elm.dataset.estimatedHours);
//             if (elm.dataset.estimatedMinutes !== undefined)
//                 sum_estimated_minutes += parseInt(elm.dataset.estimatedMinutes);
//             if (elm.dataset.spentPoints !== undefined)
//                 sum_spent_points += parseInt(elm.dataset.spentPoints);
//             if (elm.dataset.spentHours !== undefined)
//                 sum_spent_hours += parseInt(elm.dataset.spentHours);
//             if (elm.dataset.spentMinutes !== undefined)
//                 sum_spent_minutes += parseInt(elm.dataset.spentMinutes);
//         }
//     });
//     var num_visible_issues = visible_issues.length;
//     var milestone_id = $(list).parents('.milestone-box').data('milestone-id');
//
//     if (num_visible_issues === 0) {
//         if (list_issues.length > 0) {
//             $('#milestone_' + milestone_id + '_unassigned').hide();
//             $('#milestone_' + milestone_id + '_unassigned_filtered').show();
//         } else {
//             $('#milestone_' + milestone_id + '_unassigned').show();
//             $('#milestone_' + milestone_id + '_unassigned_filtered').hide();
//         }
//         $(list).parents('.milestone-issues-container').addClass('empty');
//     } else {
//         $('#milestone_' + milestone_id + '_unassigned').hide();
//         $('#milestone_' + milestone_id + '_unassigned_filtered').hide();
//         $(list).parents('.milestone-issues-container').removeClass('empty');
//     }
//     if (num_visible_issues !== list_issues.length && milestone_id != '0') {
//         $('#milestone_' + milestone_id + '_issues_count').html(num_visible_issues + ' (' + list_issues.length + ')');
//     } else {
//         $('#milestone_' + milestone_id + '_issues_count').html(num_visible_issues);
//     }
//     sum_spent_hours += Math.floor(sum_spent_minutes / 60);
//     sum_estimated_hours += Math.floor(sum_estimated_minutes / 60);
//     sum_spent_minutes = sum_spent_minutes % 60;
//     sum_estimated_minutes = sum_estimated_minutes % 60;
//     $('#milestone_' + milestone_id + '_points_count').html(sum_spent_points + ' / ' + sum_estimated_points);
//     if (sum_spent_minutes != 0) {
//         sum_spent_hours += ':' + ((sum_spent_minutes.toString().length == 1) ? '0' : '') + sum_spent_minutes;
//     }
//     if (sum_estimated_minutes != 0) {
//         sum_estimated_hours += ':' + ((sum_estimated_minutes.toString().length == 1) ? '0' : '') + sum_estimated_minutes;
//     }
//     $('#milestone_' + milestone_id + '_hours_count').html(sum_spent_hours + ' / ' + sum_estimated_hours);
// };
//
// Pachno.Project.Planning.calculateAllMilestonesVisibilityDetails = function () {
//     $('.milestone-box.initialized').find('.milestone-issues').each(function (index) {
//         var was_collapsed = $(this).hasClass('collapsed');
//         $(this).removeClass('collapsed');
//         Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails(this);
//         if (was_collapsed && parseInt($(this).parents('.milestone-box').data('milestone-id')) !== 0) $(this).addClass('collapsed');
//     });
// };
//
// Pachno.Project.Planning.calculateNewBacklogMilestoneDetails = function (event, ui) {
//     if (event === undefined || $(ui.item).hasClass('new_milestone_marker')) {
//         var nbmm = (event === undefined) ? $('#new_backlog_milestone_marker') : $(ui.placeholder[0]);
//         var num_issues = 0;
//         var sum_points = 0;
//         var sum_hours = 0;
//         var sum_minutes = 0;
//         var include_closed = $('#milestones-list').hasClass('show_closed');
//         $('.milestone-issue').removeClass('included');
//         nbmm.parents('.milestone-issues').children().each(function (elm) {
//             elm.addClass('included');
//             if (!(elm.hasClass('new_milestone_marker') && !elm.hasClass('ui-sortable-helper')) && !elm.hasClass('ui-element-placeholder')) {
//                 if (!elm.hasClass('new_milestone_marker')) {
//                     if (include_closed || !elm.hasClass('issue_closed'))
//                         num_issues++;
//                     if (!elm.hasClass('child_issue')) {
//                         if (elm.down('.issue-container').dataset.estimatedPoints !== undefined)
//                             sum_points += parseInt(elm.down('.issue-container').dataset.estimatedPoints);
//                         if (elm.down('.issue-container').dataset.estimatedHours !== undefined)
//                             sum_hours += parseInt(elm.down('.issue-container').dataset.estimatedHours);
//                         if (elm.down('.issue-container').dataset.estimatedMinutes !== undefined)
//                             sum_minutes += parseInt(elm.down('.issue-container').dataset.estimatedMinutes);
//                     }
//                 }
//             } else {
//                 throw $break;
//             }
//         });
//         sum_hours += Math.floor(sum_minutes / 60);
//         sum_minutes = sum_minutes % 60;
//         $('#new_backlog_milestone_issues_count').html(num_issues);
//         $('#new_backlog_milestone_points_count').html(sum_points);
//         if (sum_minutes != 0) {
//             sum_hours += ':' + ((sum_minutes.toString().length == 1) ? '0' : '') + sum_minutes;
//         }
//         $('#new_backlog_milestone_hours_count').html(sum_hours);
//     }
// };
//
// Pachno.Project.Planning.sortMilestones = function (event, ui) {
//     var list = $(event.target);
//     var url = list.data('sort-url');
//     var items = '';
//     list.children().each(function (milestone, index) {
//         if (milestone.data('milestone-id') !== undefined) {
//             items += '&milestone_ids['+index+']=' + milestone.data('milestone-id');
//         }
//     });
//     Pachno.Helpers.fetch(url, {
//         method: 'POST',
//         data: items,
//         loading: {indicator: '#planning_indicator'}
//     });
// };
//
// Pachno.Project.Planning.doSortMilestoneIssues = function (list) {
//     var url = list.parents('.milestone-box').data('issues-url');
//     var items = '';
//     list.children().each(function (issue) {
//         if (issue.data('issue-id') !== undefined) {
//             items += '&issue_ids[]=' + issue.data('issue-id');
//         }
//     });
//     Pachno.Helpers.fetch(url, {
//         method: 'POST',
//         data: items,
//         loading: {indicator: list.parents('.milestone-box').down('.planning_indicator')}
//     });
// };
//
// Pachno.Project.Planning.sortMilestoneIssues = function (event, ui) {
//     var list = $(event.target);
//     var issue = $(ui.item[0]);
//     if (issue.dataset.sortCancel) {
//         issue.dataset.sortCancel = null;
//         $(this).sortable("cancel");
//     } else {
//         if (ui !== undefined && ui.item.hasClass('new_milestone_marker')) {
//             Pachno.Project.Planning.calculateNewBacklogMilestoneDetails();
//         } else {
//             Pachno.Project.Planning.doSortMilestoneIssues(list);
//         }
//     }
// };
//
// Pachno.Project.Planning.moveIssue = function (event, ui) {
//     var issue = $(ui.item[0]);
//     if (issue.dataset.sortCancel) {
//         issue.dataset.sortCancel = null;
//         $(this).sortable("cancel");
//     } else {
//         if (issue.hasClass('milestone-issue')) {
//             var list = $(event.target);
//             var url = list.parents('.milestone-box').data('assign-issue-url');
//             var original_list = $(ui.sender[0]);
//             Pachno.Helpers.fetch(url, {
//                 data: 'issue_id=' + issue.data('issue-id'),
//                 loading: {indicator: list.parents('.milestone-box').down('.planning_indicator')},
//                 complete: {
//                     callback: function (json) {
//                         if (list.parents('.milestone-box').hasClass('initialized')) {
//                             issue.down('.issue-container').dataset.lastUpdated = get_current_timestamp();
//                             Pachno.Project.Planning.doSortMilestoneIssues(list);
//                             Pachno.Core.Pollers.Callbacks.planningPoller();
//                             Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails(list);
//                             Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails(original_list);
//                         } else {
//                             issue.remove();
//                             var milestone_id = list.parents('.milestone-box').data('milestone-id');
//                             $('#milestone_' + milestone_id + '_issues_count').html(json.issues);
//                             $('#milestone_' + milestone_id + '_points_count').html(json.points);
//                             $('#milestone_' + milestone_id + '_hours_count').html(json.hours);
//                         }
//                     }
//                 }
//             });
//         }
//     }
// };
//
// Pachno.Project.Planning.toggleSwimlaneDetails = function (selected_item) {
//     $('#agileboard-swimlane-details-container').children().each(Element.hide);
//     $('#agileboard_swimlane_' + $(selected_item).val() + '_container').show();
// };
//
// Pachno.Project.Planning.toggleSwimlaneExpediteDetails = function(selected_item) {
//     $('#agileboard_swimlane_expedite_container_details').children().each(Element.hide);
//     $('#swimlane_expedite_identifier_' + $(selected_item).val() + '_values').show();
// };
//
// Pachno.Project.Planning.removeAgileBoard = function (url) {
//     Pachno.Helpers.fetch(url, {
//         method: 'delete',
//         loading: {
//             indicator: '#dialog_indicator',
//             callback: function () {
//                 ['dialog_yes', 'dialog_no'].each(function (elm) {
//                     elm.addClass('disabled');
//                 });
//             }
//         },
//         success: {
//             callback: function (json) {
//                 $('#agileboard_' + json.board_id).remove();
//                 Pachno.Helpers.Dialog.dismiss();
//                 if ($('#agileboards').children().length == 0) {
//                     $('#onboarding-no-boards').show();
//                 }
//             }
//         }
//     });
// };
//
// Pachno.Project.Planning.saveAgileBoard = function (item) {
//     var url = item.action;
//     Pachno.Helpers.fetch(url, {
//         form: 'edit-agileboard-form',
//         success: {
//             callback: function (json) {
//                 if ($('#agileboards')) {
//                     if ($('#agileboard_' + json.id)) {
//                         $('#agileboard_' + json.id).replace(json.component);
//                     } else {
//                         $('#onboarding-no-boards').hide();
//                         var container = $('#agileboards');
//                         container.append(json.component);
//                     }
//                     clearFormSubmit($(item));
//                     Pachno.Helpers.Backdrop.reset();
//                 } else if ($('#project_planning') && parseInt($('#project_planning').dataset.boardId) == parseInt(json.id) && $('#project_planning').hasClass('whiteboard')) {
//                     Pachno.Helpers.Backdrop.reset();
//                     Pachno.Project.Planning.Whiteboard.retrieveMilestoneStatus();
//                     Pachno.Project.Planning.Whiteboard.retrieveWhiteboard();
//                 } else if ($('#project_planning') && parseInt($('#project_planning').dataset.boardId) == parseInt(json.id)) {
//                     var backlog = $('#milestone_0');
//                     Pachno.Helpers.Backdrop.reset();
//                     if (backlog.dataset.backlogSearch != json.backlog_search) {
//                         $('#planning_indicator').show();
//                         window.location.reload(true);
//                     } else {
//                         backlog.removeClass('initialized');
//                         $('#milestone_0_issues').html('');
//                         $('#milestone_0_issues').removeClass('ui-sortable');
//                         backlog.down('.planning_indicator').show();
//                         Pachno.Project.Planning.initialize(Pachno.Project.Planning.options);
//                     }
//                 }
//             }
//         }
//     });
// };
//
// Pachno.Main.updateFancyDropdownLabel = function ($dropdown) {
//     var $label = $dropdown.find('> .value');
//     if ($label.length > 0) {
//         var auto_close = false;
//         var values = [];
//         $dropdown.find('input[type=checkbox],input[type=radio]').each(function () {
//             var $input = $(this);
//
//             if ($input.attr('type') == 'radio') {
//                 auto_close = true;
//             }
//
//             if ($input.is(':checked')) {
//                 var $label = $($input.next('label')),
//                     $value = $($label.find('.value')[0]);
//
//                 if ($value.text() != '') {
//                     values.push($value.text());
//                 }
//             }
//         });
//
//         if (values.length > 0) {
//             $dropdown.removeClass('no-value');
//             $label.html(values.join(', '));
//         } else {
//             $dropdown.addClass('no-value');
//             $label.html($dropdown.data('default-label'));
//         }
//
//         if (auto_close) {
//             $dropdown.removeClass('active');
//         }
//     }
// };
//
// Pachno.Project.Milestone.markFinished = function (form) {
//     var url = form.action;
//     var milestone_id = form.data('milestone-id');
//     Pachno.Helpers.fetch(url, {
//         form: form,
//         loading: {
//             indicator: '#milestone_edit_indicator',
//             callback: function () {
//                 $('#mark_milestone_finished_form').find('input.button').each(Element.disable);
//             }
//         },
//         success: {
//             remove: 'milestone_' + milestone_id,
//             callback: function (json) {
//                 Pachno.Helpers.Backdrop.reset();
//                 if (json.component) {
//                     $('#milestones-list').append(json.component);
//                     setTimeout(function () {
//                         Pachno.Project.Planning.getMilestoneIssues($('#milestone_' + json.new_milestone_id), Pachno.Project.Planning.initializeDragDropSorting);
//                     }, 250);
//                 } else {
//                     Pachno.Core.Pollers.Callbacks.planningPoller();
//                 }
//             }
//         },
//         failure: {
//             callback: function () {
//                 $('#mark_milestone_finished_form').find('input.button').each(Element.enable);
//             }
//         }
//     });
// };
//
// Pachno.Project.Milestone.save = function (form, on_board) {
//     var submit_button = $(form).find('.form-row.submit-container button[type=submit]');
//
//     if (submit_button.length) {
//         submit_button.prop('disabled', true);
//         submit_button.addClass('submitting');
//     }
//
//     var url = form.action;
//     var include_selected_issues = $('#include_selected_issues').val() == 1;
//
//     var data = new FormData(form);
//     if (include_selected_issues) {
//         $('.milestone-issue.included').each(function (issue) {
//             data.append( "issues[]", issue.data('issue-id'));
//         });
//     }
//
//     return new Promise(function (resolve, reject) {
//         fetch(url, {
//                 method: 'POST',
//                 body: data
//             })
//             .then((_) => _.json())
//             .then(function (json) {
//                 if ($('#no_milestones')) {
//                     $('#no_milestones').hide();
//                 }
//
//                 $('.milestone-issue.included').each(function (issue) { issue.remove(); });
//                 Pachno.Helpers.Backdrop.reset();
//                 if ($('#milestones-list').length) {
//                     $('#milestones-list').append(json.component);
//                 }
//
//                 if (on_board) {
//                     if (!include_selected_issues) {
//                         setTimeout(function () {
//                             Pachno.Project.Planning.getMilestoneIssues($('#milestone_' + json.milestone_id));
//                         }, 250);
//                     } else {
//                         Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails($('#milestone_0_issues'));
//                         // Pachno.Project.Planning.initializeDragDropSorting();
//                     }
//                 }
//             });
//     });
//     // Pachno.Helpers.fetch(url, {
//     //     form: form,
//     //     data: issues,
//     //     loading: {indicator: '#milestone_edit_indicator'},
//     //     success: {
//     //         reset: 'edit_milestone_form',
//     //         hide: 'no_milestones',
//     //         callback: function (json) {
//     //             $('.milestone-issue.included').each(function (issue) { issue.remove(); });
//     //             Pachno.Helpers.Backdrop.reset();
//     //             if ($('#milestone_' + json.milestone_id)) {
//     //                 $('#milestone_' + json.milestone_id).replace(json.component);
//     //             } else {
//     //                 $('#milestones-list').append(json.component);
//     //             }
//     //             if (on_board) {
//     //                 if (!include_selected_issues) {
//     //                     setTimeout(function () {
//     //                         Pachno.Project.Planning.getMilestoneIssues($('#milestone_' + json.milestone_id), Pachno.Project.Planning.initializeDragDropSorting);
//     //                     }, 250);
//     //                 } else {
//     //                     Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails($('#milestone_0_issues'));
//     //                     Pachno.Project.Planning.initializeDragDropSorting();
//     //                 }
//     //             }
//     //             Pachno.Project.Milestone.selectFromHash();
//     //         }
//     //     }
//     // });
// }
//
// Pachno.Project.Milestone.remove = function (url, milestone_id) {
//     Pachno.Helpers.fetch(url, {
//         method: 'delete',
//         loading: {
//             indicator: '#dialog_indicator',
//         },
//         success: {
//             callback: function (json) {
//                 $('#milestone_' + milestone_id).remove();
//                 Pachno.Helpers.Dialog.dismiss();
//                 Pachno.Helpers.Backdrop.reset();
//                 if ($('#milestones-list').children().length == 0)
//                     $('#no_milestones').show();
//                 Pachno.Core.Pollers.Callbacks.planningPoller();
//             }
//         }
//     });
// }
//
// Pachno.Project.Build.remove = function (url, bid, b_type, edition_id) {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             show: 'fullpage_backdrop_indicator',
//             indicator: '#fullpage_backdrop',
//             hide: ['fullpage_backdrop_content', 'dialog_backdrop', 'build_' + bid + '_info'],
//             callback: function () {
//                 $('#build_' + bid + '_indicator').addClass('selected_red');
//             }
//         },
//         success: {
//             remove: ['show_build_' + bid],
//             callback: function () {
//                 Pachno.Helpers.Dialog.dismiss();
//                 if ($(b_type + '_builds_' + edition_id).children().length == 0) {
//                     $('#no_' + b_type + '_builds_' + edition_id).show();
//                 }
//             }
//         },
//         failure: {
//             show: 'build_' + bid + '_info',
//             hide: 'del_build_' + bid,
//             callback: function () {
//                 $('#build_' + bid + '_indicator').removeClass('selected_red');
//             }
//         }
//     });
// };
//
// Pachno.Project.Build.add = function (url, edition_id) {
//     Pachno.Helpers.fetch(url, {
//         form: 'add_build_form',
//         loading: {indicator: '#build_add_indicator'},
//         success: {
//             reset: 'add_build_form',
//             hide: 'no_builds_' + edition_id,
//             update: {element: 'builds_' + edition_id, insertion: true, from: 'html'}
//         }
//     });
// };
//
// Pachno.Project.Component.remove = function (url, id) {
//     fetch(url, { method: 'DELETE' })
//         .then(function (response) {
//             response.json().then(function (json) {
//                 Pachno.Helpers.Dialog.dismiss();
//                 if (response.ok) {
//                     $('#[data-component][data-id=' + id + ']').remove();
//                 } else {
//                     Pachno.Helpers.Message.error(json.error);
//                 }
//             })
//                 .catch(function (error) {
//                     Pachno.Helpers.Dialog.dismiss();
//                     Pachno.Helpers.Message.error(error);
//                 });
//         });
// }
//
// Pachno.Project.Edition.remove = function (url, id) {
//     fetch(url, { method: 'DELETE' })
//         .then(function (response) {
//             response.json().then(function (json) {
//                 Pachno.Helpers.Dialog.dismiss();
//                 if (response.ok) {
//                     $('#[data-edition][data-id=' + id + ']').remove();
//                     $('#project-editions-list-container').removeClass('active');
//                     $('#selected-edition-options').html('');
//                 } else {
//                     Pachno.Helpers.Message.error(json.error);
//                 }
//             })
//                 .catch(function (error) {
//                     Pachno.Helpers.Dialog.dismiss();
//                     Pachno.Helpers.Message.error(error);
//                 });
//         });
// }
//
// Pachno.Project.findDevelopers = function (url) {
//     Pachno.Helpers.fetch(url, {
//         form: 'find_dev_form',
//         loading: {indicator: '#find_dev_indicator'},
//         success: {
//             update: '#find_dev_results',
//             callback: function () {
//                 let $form = $('#find_dev_form');
//                 $form.removeClass('submitting');
//                 $form.find('button[type=submit]').each(function () {
//                     var $button = $(this);
//                     $button.removeClass('auto-disabled');
//                     $button.prop('disabled', false);
//                 })
//             }
//         }
//     });
// }
//
// Pachno.Project._updateUserFromJSON = function (object, field) {
//     if (object.id == 0) {
//         $(field + '_name').hide();
//         $('#no_' + field).show();
//     } else {
//         $(field + '_name').html(object.name);
//         $('#no_' + field).hide();
//         $(field + '_name').show();
//     }
// }
//
// Pachno.Project.setUser = function (url, field) {
//     Pachno.Helpers.fetch(url, {
//         loading: {indicator: field + '_spinning'},
//         success: {
//             hide: field + '_change',
//             callback: function (json) {
//                 Pachno.Project._updateUserFromJSON(json.field, field);
//             }
//         }
//     });
// }
//
// Pachno.Project.assign = function (url, container_id) {
//     var role_id = $(container_id).down('select').val();
//     var parameters = "&role_id=" + role_id;
//     Pachno.Helpers.fetch(url, {
//         params: parameters,
//         loading: {indicator: '#assign_dev_indicator'},
//         success: {update: '#assignees_list'}
//     });
// }
//
// Pachno.Project.removeAssignee = function (url, type, id) {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: '#remove_assignee_' + type + '_' + id + '_indicator',
//             hide: 'assignee_' + type + '_' + id + '_link'
//         },
//         success: {
//             remove: 'assignee_' + type + '_' + id + '_row',
//             callback: function () {
//                 if ($('#project_team_' + type + 's').children().length == 0) {
//                     $('#project_team_' + type + 's').hide();
//                     $('#no_project_team_' + type + 's').show();
//                 }
//             }
//         }
//     });
// }
//
// Pachno.Project.workflow = function (url) {
//     Pachno.Helpers.fetch(url, {
//         form: 'workflow_form2',
//         loading: {indicator: '#update_workflow_indicator'},
//         success: {callback: function () {
//             Pachno.Helpers.Backdrop.reset();
//         }}
//     });
// };
//
// Pachno.Project.workflowtable = function (url) {
//     Pachno.Helpers.fetch(url, {
//         form: 'workflow_form',
//         loading: {
//             indicator: '#change_workflow_indicator'
//         },
//         success: {
//             update: '#change_workflow_table',
//             hide: 'change_workflow_box',
//             show: 'change_workflow_table'
//         }
//     });
// };
//
// Pachno.Project.updatePrefix = function (url, project_id) {
//     Pachno.Helpers.fetch(url, {
//         form: 'project_info',
//         success: {
//             update: '#project_key_input',
//             callback: function () {
//                 clearFormSubmit($('#project_info'));
//             }
//         }
//     });
// };
//
// Pachno.Project.clearReleaseCenterFilters = function () {
//     var prcc = $('#project_release_center_container');
//     ['only_archived', 'only_active', 'only_downloads'].each(function (cn) {
//         prcc.removeClass(cn);
//     });
// };
//
// Pachno.Project.checkAndToggleNoBuildsMessage = function () {
//     $('.simple-list').each(function (elem) {
//         // If this list does not contain builds continue.
//         if (elem.id.indexOf('active_builds_') !== 0) return;
//
//         // We assume no build is visible.
//         var one_build_visible = false;
//
//         $(elem).children().each(function (elem) {
//             // If this child - build is not visible continue.
//             if (! $('#' + elem.id).is(':visible')) return;
//
//             // Once we find visible build set flag and break this loop.
//             one_build_visible = true;
//             return false;
//         });
//
//         // Hide or show no builds message based on one build visible flag.
//         if (one_build_visible) {
//             $('#no_' + elem.id).hide();
//         }
//         else {
//             $('#no_' + elem.id).show();
//         }
//     });
// };
//
// Pachno.Project.clearRoadmapFilters = function () {
//     var prp = $('#project_roadmap_page');
//     ['upcoming', 'past'].each(function (cn) {
//         prp.removeClass(cn);
//     });
//
//     var hash = window.location.hash;
//
//     if (hash != undefined && hash.indexOf('roadmap_milestone_') == 1) {
//         window.location.hash = '';
//     }
// };
//
// Pachno.Project.showRoadmap = function () {
//     $('#milestone_details_overview').hide();
//     $('#project_roadmap').show();
//     $('#planning_board_settings_gear').show();
// }
//
// Pachno.Project.showMilestoneDetails = function (url, milestone_id, force) {
//     $('body')[0].css({'overflow': 'auto'});
//
//     var force = force || false;
//
//     if (force && $('#milestone_details_' + milestone_id)) {
//         $('#milestone_details_' + milestone_id).remove();
//     }
//
//     $('#project_planning_action_strip .more_actions_dropdown, #planning_board_settings_gear').hide();
//
//     if (!$('#milestone_details_' + milestone_id)) {
//         window.location.hash = 'roadmap_milestone_' + milestone_id;
//
//         Pachno.Helpers.fetch(url, {
//             method: 'GET',
//             loading: {
//                 indicator: '#fullpage_backdrop',
//                 show: 'fullpage_backdrop_indicator',
//                 hide: ['fullpage_backdrop_content', 'project_roadmap']
//             },
//             success: {
//                 show: 'milestone_details_overview',
//                 update: '#milestone_details_overview'
//             }
//         });
//     } else {
//         $('#project_roadmap').hide();
//         $('#milestone_details_overview').show();
//     }
// }
//
// Pachno.Project.toggleLeftSelection = function (item) {
//     $(item).parents('.list-mode').children().each(function (elm) {
//         elm.removeClass('selected');
//     });
//     $(item).addClass('selected');
// };
//
// Pachno.Config.Import.importCSV = function (url) {
//     Pachno.Helpers.fetch(url, {
//         form: 'import_csv_form',
//         loading: {
//             indicator: '#csv_import_indicator',
//             hide: 'csv_import_error'
//         },
//         failure: {
//             show: 'csv_import_error',
//             callback: function (json) {
//                 $('#csv_import_error_detail').html(json.errordetail);
//             }
//         }
//     });
// }
//
// Pachno.Config.Import.getImportCsvIds = function (url) {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: '#id_zone_indicator',
//             hide: 'id_zone_content'
//         },
//         success: {
//             update: '#id_zone_content',
//             show: 'id_zone_content'
//         }
//     });
// }
//
// Pachno.Config.updateCheck = function (url) {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: '#update_spinner',
//             hide: 'update_button'
//         },
//         success: {
//             callback: function (json) {
//                 (json.uptodate) ?
//                     Pachno.Helpers.Message.success(json.title, json.message) :
//                     Pachno.Helpers.Message.error(json.title, json.message);
//             }
//         },
//         complete: {
//             show: 'update_button'
//         }
//     });
// }
//
// Pachno.Config.Issuetype.remove = function (url, id) {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: '#fullpage_backdrop',
//             show: 'fullpage_backdrop_indicator',
//             hide: ['fullpage_backdrop_content', 'dialog_backdrop']
//         },
//         success: {
//             remove: 'issuetype_' + id + '_box',
//             callback: Pachno.Helpers.Dialog.dismiss
//         }
//     });
// }
//
// Pachno.Config.IssuetypeScheme.save = function (form) {
//     const $form = $(form),
//         data = new FormData($form[0]);
//
//     $form.find('.error-container').removeClass('invalid');
//     $form.find('.error-container > .error').html('');
//     $form.addClass('submitting');
//
//     fetch($form.attr('action'), {
//         method: 'POST',
//         body: data
//     })
//         .then(function (response) {
//             response.json().then(function (json) {
//                 if (!response.ok) {
//                     $form.find('.error-container > .error').html(json.error);
//                     $form.find('.error-container').addClass('invalid');
//                 }
//                 $form.removeClass('submitting');
//             });
//         });
// };
//
// Pachno.Config.IssuetypeScheme.addField = function (url, key) {
//     const $container = $('#issue-type-fields-list'),
//         $add_list = $('#add-issue-field-list');
//
//     fetch(url, {
//         method: 'GET'
//     })
//         .then(function (response) {
//             response.json().then(function (json) {
//                 if (response.ok) {
//                     $container.append(json.content);
//                     $('.list-item[data-issue-field][data-id=' + key + ']').addClass('disabled');
//                 } else {
//                     Pachno.Helpers.Message.error(json.error);
//                 }
//             });
//         });
// };
//
// Pachno.Config.IssuetypeScheme.saveOptions = function (form) {
//     const $container = $('#issue-type-configuration-container'),
//         $form = $(form),
//         data = new FormData($form[0]),
//         $options = $('#selected-issue-type-options');
//
//     $form.find('.error-container').removeClass('invalid');
//     $form.find('.error-container > .error').html('');
//     $form.addClass('submitting');
//     $form.find('.button.primary').prop('disabled', true);
//
//     fetch($form.attr('action'), {
//         method: 'POST',
//         body: data
//     })
//         .then(function (response) {
//             response.json().then(function (json) {
//                 if (response.ok) {
//                     $container.removeClass('active');
//                     $container.find('.issue-type-scheme-issue-type').removeClass('active');
//                     $options.html('');
//                 } else {
//                     $form.find('.error-container > .error').html(json.error);
//                     $form.find('.error-container').addClass('invalid');
//                 }
//             });
//         });
// };
//
// Pachno.Config.IssuetypeScheme.copy = function (url, scheme_id) {
//     Pachno.Helpers.fetch(url, {
//         form: 'copy_issuetype_scheme_' + scheme_id + '_form',
//         loading: {
//             indicator: '#copy_issuetype_scheme_' + scheme_id + '_indicator'
//         },
//         success: {
//             hide: 'copy_scheme_' + scheme_id + '_popup',
//             update: {element: 'issuetype_schemes_list', insertion: true}
//         }
//     });
// }
//
// Pachno.Config.IssuetypeScheme.remove = function (url, scheme_id) {
//     Pachno.Helpers.fetch(url, {
//         form: 'delete_issuetype_scheme_' + scheme_id + '_form',
//         loading: {
//             indicator: '#delete_issuetype_scheme_' + scheme_id + '_indicator'
//         },
//         success: {
//             remove: ['delete_scheme_' + scheme_id + '_popup', 'copy_scheme_' + scheme_id + '_popup', 'issuetype_scheme_' + scheme_id],
//             update: {element: 'issuetype_schemes_list', insertion: true},
//             callback: function () {
//                 Pachno.Helpers.Dialog.dismiss();
//             }
//         }
//     });
// }
//
// Pachno.Config.Issuefields.saveOrder = function (container, type, url) {
//     Pachno.Helpers.fetch(url, {
//         data: Sortable.serialize(container),
//         loading: {
//             indicator: type + '_sort_indicator'
//         }
//     });
// };
//
// Pachno.Config.Issuefields.Options.save = function (form) {
//     var $form = $(form),
//         data = new FormData($form[0]);
//
//     if ($form.hasClass('submitting')) return;
//
//     $form.find('.error-container').removeClass('invalid');
//     $form.find('.error-container > .error').html('');
//     $form.addClass('submitting');
//
//     fetch($form.attr('action'), {
//         method: 'POST',
//         body: data
//     })
//         .then(function (response) {
//             response.json().then(function (json) {
//                 if (response.ok) {
//                     const $issue_option_container = $('#[data-issue-field-option][data-id='+json.item.id+']');
//                     if ($issue_option_container.length > 0) {
//                         $issue_option_container.replaceWith(json.component);
//                     } else {
//                         const $issue_options_container = $('#field-options-list');
//                         if ($issue_options_container.length > 0) {
//                             $issue_options_container.append(json.component);
//                         }
//                         if (sortable_options != undefined) {
//                             Sortable.destroy('field-options-list');
//                             Sortable.create('field-options-list', sortable_options);
//                         }
//                         Pachno.Helpers.initializeColorPicker();
//                     }
//                     $form[0].reset();
//                 } else {
//                     $form.find('.error-container > .error').html(json.error);
//                     $form.find('.error-container').addClass('invalid');
//                 }
//
//                 $form.removeClass('submitting');
//                 $form.find('.button.primary').prop('disabled', false);
//             })
//                 .catch(function (error) {
//                     $form.find('.error-container > .error').html(error);
//                     $form.find('.error-container').addClass('invalid');
//
//                     $form.removeClass('submitting');
//                 });
//         });
// }
//
// Pachno.Config.Issuefields.Options.update = function (url, type, id) {
//     Pachno.Helpers.fetch(url, {
//         form: 'edit_' + type + '_' + id + '_form',
//         loading: {indicator: '#edit_' + type + '_' + id + '_indicator'},
//         success: {
//             show: 'item_option_' + type + '_' + id + '_content',
//             hide: 'edit_item_option_' + id,
//             callback: function (json) {
//                 $(type + '_' + id + '_name').html($(type + '_' + id + '_name_input').val());
//                 if ($(type + '_' + id + '_itemdata_input') && $(type + '_' + id + '_itemdata'))
//                     $(type + '_' + id + '_itemdata').style.backgroundColor = $(type + '_' + id + '_itemdata_input').val();
//                 if ($(type + '_' + id + '_value_input') && $(type + '_' + id + '_value'))
//                     $(type + '_' + id + '_value').html($(type + '_' + id + '_value_input').val());
//             }
//         }
//     });
// }
//
// Pachno.Config.Issuefields.Options.remove = function (url, id) {
//     fetch(url, { method: 'POST' })
//         .then(function (response) {
//             response.json().then(function (json) {
//                 Pachno.Helpers.Dialog.dismiss();
//                 if (response.ok) {
//                     $('#[data-issue-field-option][data-id=' + id + ']').remove();
//                 } else {
//                     Pachno.Helpers.Message.error(json.error);
//                 }
//             })
//             .catch(function (error) {
//                 Pachno.Helpers.Dialog.dismiss();
//                 Pachno.Helpers.Message.error(error);
//             });
//         });
// }
//
// Pachno.Config.Issuefields.Custom.update = function (url, type) {
//     Pachno.Helpers.fetch(url, {
//         form: 'edit_custom_type_' + type + '_form',
//         loading: {indicator: '#edit_custom_type_' + type + '_indicator'},
//         success: {
//             hide: 'edit_custom_type_' + type + '_form',
//             callback: function (json) {
//                 $('#custom_type_' + type + '_description_span').html(json.description);
//                 $('#custom_type_' + type + '_instructions_span').html(json.instructions);
//                 if (json.instructions != '') {
//                     $('#custom_type_' + type + '_instructions_div').show();
//                     $('#custom_type_' + type + '_no_instructions_div').hide();
//                 } else {
//                     $('#custom_type_' + type + '_instructions_div').hide();
//                     $('#custom_type_' + type + '_no_instructions_div').show();
//                 }
//                 $('#custom_type_' + type + '_name').html(json.name);
//             },
//             show: 'custom_type_' + type + '_info'
//         }
//     });
// }
//
// Pachno.Config.Issuefields.Custom.remove = function (url, id) {
//     fetch(url, { method: 'POST' })
//         .then(function (response) {
//             response.json().then(function (json) {
//                 Pachno.Helpers.Dialog.dismiss();
//                 if (response.ok) {
//                     $('#[data-issue-field][data-id=' + id + ']').remove();
//                     const $container = $('#issue-fields-configuration-container'),
//                         $options = $('#selected-issue-field-options');
//
//                     $container.removeClass('active');
//                     $container.find('.issue-type-scheme-issue-type').removeClass('active');
//                     $options.html('');
//                 } else {
//                     Pachno.Helpers.Message.error(json.error);
//                 }
//             })
//                 .catch(function (error) {
//                     Pachno.Helpers.Dialog.dismiss();
//                     Pachno.Helpers.Message.error(error);
//                 });
//         });
// };
//
// Pachno.Config.Permissions.set = function (url, field) {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: field + '_indicator',
//             callback: function (json) {
//                 $('##' + field + ' .image img').each(function (element) {
//                     $(element).hide();
//                 });
//             }
//         },
//         success: {update: field + '_wrapper'}
//     });
// };
//
// Pachno.Config.Permissions.getOptions = function (url, field) {
//     $(field).toggle();
//     if ($(field).children().length == 0) {
//         Pachno.Helpers.fetch(url, {
//             loading: {indicator: field + '_indicator'},
//             success: {update: field}
//         });
//     }
// }
//
// Pachno.Config.Roles.update = function (url, role_id) {
//     Pachno.Helpers.fetch(url, {
//         form: 'role_' + role_id + '_form',
//         loading: {indicator: '#role_' + role_id + '_form_indicator'},
//         success: {
//             hide: 'role_' + role_id + '_permissions_edit',
//             callback: function (json) {
//                 $('#role_' + role_id + '_permissions_count').html(json.permissions_count);
//                 $('#role_' + role_id + '_permissions_list').html('');
//                 $('#role_' + role_id + '_permissions_list').hide();
//                 $('#role_' + role_id + '_name').html(json.role_name);
//             }
//         }
//     });
// }
//
// Pachno.Config.Roles.remove = function (url, role_id) {
//     Pachno.Helpers.fetch(url, {
//         method: 'POST',
//         loading: {
//             indicator: '#fullpage_backdrop',
//             show: 'fullpage_backdrop_indicator',
//             hide: ['fullpage_backdrop_content', 'dialog_backdrop']
//         },
//         success: {
//             callback: function () {
//                 var rc = $('#role_' + role_id + '_container');
//                 if (rc.parents('ul').children().length == 2) {
//                     rc.parents('ul').down('li.no_roles').show();
//                 }
//                 rc.remove();
//             }
//         }
//     });
// }
//
// Pachno.Config.Roles.add = function (url) {
//     Pachno.Helpers.fetch(url, {
//         form: 'new_role_form',
//         loading: {indicator: '#new_role_form_indicator'},
//         success: {
//             update: {element: 'global_roles_list', insertion: true},
//             hide: ['global_roles_no_roles'],
//             callback: function  () {
//                 $('#add_new_role_input').value('');
//             }
//         }
//     });
// };
//
// Pachno.Project.Roles.add = function (url, pid) {
//     Pachno.Helpers.fetch(url, {
//         form: 'new_project' + pid + '_role_form',
//         loading: {indicator: '#new_project' + pid + '_role_form_indicator'},
//         success: {
//             update: {element: 'project' + pid + '_roles_list', insertion: true},
//             hide: ['project' + pid + '_roles_no_roles', 'new_project' + pid + '_role']
//         }
//     });
// };
//
// Pachno.Config.User.show = function (url, findstring) {
//     Pachno.Helpers.fetch(url, {
//         params: '&findstring=' + findstring,
//         loading: {indicator: '#find_users_indicator'},
//         success: {update: '#users_results'}
//     });
// };
//
// Pachno.Config.User.add = function (url, callback_function_for_import, form) {
//     f = (form !== undefined) ? form : 'createuser_form';
//     Pachno.Helpers.fetch(url, {
//         form: f,
//         loading: {
//             indicator: '#createuser_form_indicator'
//         },
//         success: {
//             hide: ['createuser_form_indicator', 'createuser_form_quick_indicator'],
//             update: '#users_results',
//             callback: function (json) {
//                 $('#adduser_div').hide();
//                 Pachno.Config.User._updateLinks(json);
//                 $(f).reset();
//             }
//         },
//         failure: {
//             hide: ['createuser_form_indicator', 'createuser_form_quick_indicator'],
//             callback: function (json) {
//                 if (json.allow_import || false) {
//                     callback_function_for_import();
//                 }
//             }
//         }
//     });
// };
//
// Pachno.Config.User.addToScope = function (url) {
//     Pachno.Helpers.fetch(url, {
//         form: 'createuser_form',
//         loading: {indicator: '#dialog_indicator'},
//         success: {
//             update: '#users_results',
//             callback: function (json) {
//                 Pachno.Helpers.Dialog.dismiss();
//                 Pachno.Config.User._updateLinks(json);
//             }
//         }
//     });
// };
//
// Pachno.Config.User.remove = function (url, user_id) {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: '#fullpage_backdrop',
//             show: 'fullpage_backdrop_indicator',
//             hide: ['fullpage_backdrop_content', 'dialog_backdrop']
//         },
//         success: {
//             remove: ['users_results_user_' + user_id, 'user_' + user_id + '_edit_spinning', 'user_' + user_id + '_edit_tr', 'users_results_user_' + user_id + '_permissions_row'],
//             callback: Pachno.Config.User._updateLinks
//         }
//     });
// };
//
// Pachno.Config.User._updateLinks = function (json) {
//     if (json == null) return;
//     if ($('#current_user_num_count'))
//         $('#current_user_num_count').html(json.total_count);
//     (json.more_available) ? $('#adduser_form_container').show() : $('#adduser_form_container').hide();
//     Pachno.Config.Collection.updateDetailsFromJSON(json);
// };
//
// Pachno.Config.User.update = function (url, user_id) {
//     Pachno.Helpers.fetch(url, {
//         form: 'edit_user_' + user_id + '_form',
//         loading: {indicator: '#edit_user_' + user_id + '_indicator'},
//         success: {
//             update: '#users_results_user_' + user_id,
//             show: 'users_results_user_' + user_id,
//             hide: 'user_' + user_id + '_edit_tr',
//             callback: function (json) {
//                 $('#password_' + user_id + '_leave').checked = true;
//                 $('#new_password_' + user_id + '_1').val('');
//                 $('#new_password_' + user_id + '_2').val('');
//                 Pachno.Config.Collection.updateDetailsFromJSON(json);
//             }
//         }
//     });
// };
//
// Pachno.Config.User.updateScopes = function (url, user_id) {
//     Pachno.Helpers.fetch(url, {
//         form: 'edit_user_' + user_id + '_scopes_form',
//         loading: {indicator: '#edit_user_' + user_id + '_scopes_form_indicator'},
//         success: {
//             callback: Pachno.Helpers.Backdrop.reset
//         }
//     });
// };
//
// Pachno.Config.User.getPermissionsBlock = function (url, user_id) {
//     $('#users_results_user_' + user_id + '_permissions_row').toggle();
//     if ($('#users_results_user_' + user_id + '_permissions').innerHTML == '') {
//         Pachno.Helpers.fetch(url, {
//             loading: {
//                 indicator: '#permissions_' + user_id + '_indicator'
//             },
//             success: {
//                 update: '#users_results_user_' + user_id + '_permissions',
//                 show: 'users_results_user_' + user_id + '_permissions'
//             }
//         });
//     }
// };
//
// Pachno.Config.Collection.add = function (url, type, callback_function) {
//     Pachno.Helpers.fetch(url, {
//         form: 'create_' + type + '_form',
//         loading: {indicator: '#create_' + type + '_indicator'},
//         success: {
//             update: {element: type + 'config_list', insertion: true},
//             callback: callback_function
//         }
//     });
// };
//
// Pachno.Config.Collection.remove = function (url, type, cid, callback_function) {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: '#fullpage_backdrop',
//             show: 'fullpage_backdrop_indicator',
//             hide: ['fullpage_backdrop_content', 'dialog_backdrop']
//         },
//         success: {
//             remove: type + 'box_' + cid,
//             callback: function (json) {
//                 if (callback_function)
//                     callback_function(json);
//             }
//         }
//     });
// };
//
// Pachno.Config.Collection.clone = function (url, type, cid, callback_function) {
//     Pachno.Helpers.fetch(url, {
//         form: 'clone_' + type + '_' + cid + '_form',
//         loading: {indicator: '#clone_' + type + '_' + cid + '_indicator'},
//         success: {
//             update: {element: type + 'config_list', insertion: true},
//             hide: 'clone_' + type + '_' + cid,
//             callback: callback_function
//         }
//     });
// };
//
// Pachno.Config.Collection.showMembers = function (url, type, cid) {
//     $(type + '_members_' + cid + '_container').toggle();
//     if ($(type + '_members_' + cid + '_list').innerHTML == '') {
//         Pachno.Helpers.fetch(url, {
//             loading: {indicator: type + '_members_' + cid + '_indicator'},
//             success: {update: type + '_members_' + cid + '_list'},
//             failure: {hide: type + '_members_' + cid + '_container'}
//         });
//     }
// };
//
// Pachno.Config.Collection.removeMember = function (url, type, cid, user_id) {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: type + '_members_' + cid + '_indicator',
//             hide: 'dialog_backdrop'
//         },
//         success: {
//             callback: function (json) {
//                 $(type + '_' + cid + '_' + user_id + '_item').remove();
//                 Pachno.Config.Collection.updateDetailsFromJSON(json, false);
//                 var ul = $(type + '_members_' + cid + '_list').down('ul');
//                 if (ul != undefined && ul.children().length == 0)
//                     $(type + '_members_' + cid + '_no_users').show();
//             }
//         }
//     });
// };
//
// Pachno.Config.Collection.addMember = function (url, type, cid, user_id) {
//     Pachno.Helpers.fetch(url, {
//         loading: {indicator: type + '_members_' + cid + '_indicator'},
//         success: {
//             callback: function (json) {
//                 Pachno.Config.Collection.updateDetailsFromJSON(json, false);
//                 var ul = $(type + '_members_' + cid + '_list').down('ul');
//                 if (ul != undefined && ul.children().length == 0) {
//                     $(type + '_members_' + cid + '_no_users').hide();
//                 }
//                 $(type + '_members_' + cid + '_list').down('ul').append(json[type + 'listitem']);
//             }
//         }
//     });
// };
//
// Pachno.Config.Collection.updateDetailsFromJSON = function (json, clear) {
//     if (json.update_groups) {
//         json.update_groups.ids.each(function (group_id) {
//             if ($('#group_' + group_id + '_membercount'))
//                 $('#group_' + group_id + '_membercount').html(json.update_groups.membercounts[group_id]);
//             if (clear == undefined || clear == true) {
//                 $('#group_members_' + group_id + '_container').hide();
//                 $('#group_members_' + group_id + '_list').html('');
//             }
//         });
//     }
//     if (json.update_teams) {
//         json.update_teams.ids.each(function (team_id) {
//             if ($('#team_' + team_id + '_membercount'))
//                 $('#team_' + team_id + '_membercount').html(json.update_teams.membercounts[team_id]);
//             if (clear == undefined || clear == true) {
//                 $('#team_members_' + team_id + '_container').hide();
//                 $('#team_members_' + team_id + '_list').html('');
//             }
//         });
//     }
//     if (json.update_clients) {
//         json.update_clients.ids.each(function (client_id) {
//             if ($('#client_' + client_id + '_membercount'))
//                 $('#client_' + client_id + '_membercount').html(json.update_clients.membercounts[client_id]);
//             if (clear == undefined || clear == true) {
//                 $('#client_members_' + client_id + '_container').hide();
//                 $('#client_members_' + client_id + '_list').html('');
//             }
//         });
//     }
// };
//
// Pachno.Config.Group.add = function (url) {
//     Pachno.Config.Collection.add(url, 'group');
// };
//
// Pachno.Config.Group.remove = function (url, group_id) {
//     Pachno.Config.Collection.remove(url, 'group', group_id);
// };
//
// Pachno.Config.Group.clone = function (url, group_id) {
//     Pachno.Config.Collection.clone(url, 'group', group_id);
// };
//
// Pachno.Config.Group.showMembers = function (url, group_id) {
//     Pachno.Config.Collection.showMembers(url, 'group', group_id);
// }
//
// Pachno.Config.Team.updateLinks = function (json) {
//     if ($('#current_team_num_count'))
//         $('#current_team_num_count').html(json.total_count);
//     $('.copy_team_link').each(function (element) {
//         (json.more_available) ? $(element).show() : $(element).hide();
//     });
//     (json.more_available) ? $('#add_team_div').show() : $('#add_team_div').hide();
// }
//
// Pachno.Config.Team.getPermissionsBlock = function (url, team_id) {
//     if ($('#team_' + team_id + '_permissions').innerHTML == '') {
//         Pachno.Helpers.fetch(url, {
//             loading: {
//                 show: 'team_' + team_id + '_permissions_container',
//                 indicator: '#team_' + team_id + '_permissions_indicator'
//             },
//             success: {
//                 update: '#team_' + team_id + '_permissions',
//             }
//         });
//     }
//     else {
//         $('#team_' + team_id + '_permissions_container').show();
//     }
// };
//
// Pachno.Config.Team.add = function (url) {
//     Pachno.Config.Collection.add(url, 'team', Pachno.Config.Team.updateLinks);
// }
//
// Pachno.Config.Team.remove = function (url, team_id) {
//     Pachno.Config.Collection.remove(url, 'team', team_id, Pachno.Config.Team.updateLinks);
// };
//
// Pachno.Config.Team.clone = function (url, team_id) {
//     Pachno.Config.Collection.clone(url, 'team', team_id, Pachno.Config.Team.updateLinks);
// }
//
// Pachno.Config.Team.showMembers = function (url, team_id) {
//     Pachno.Config.Collection.showMembers(url, 'team', team_id);
// }
//
// Pachno.Config.Team.removeMember = function (url, team_id, member_id) {
//     Pachno.Config.Collection.removeMember(url, 'team', team_id, member_id);
// }
//
// Pachno.Config.Team.addMember = function (url, team_id, member_id) {
//     Pachno.Config.Collection.addMember(url, 'team', team_id, member_id);
// }
//
// Pachno.Config.Client.add = function (url) {
//     Pachno.Config.Collection.add(url, 'client');
// }
//
// Pachno.Config.Client.remove = function (url, client_id) {
//     Pachno.Config.Collection.remove(url, 'client', client_id);
// }
//
// Pachno.Config.Client.showMembers = function (url, client_id) {
//     Pachno.Config.Collection.showMembers(url, 'client', client_id);
// }
//
// Pachno.Config.Client.removeMember = function (url, client_id, member_id) {
//     Pachno.Config.Collection.removeMember(url, 'client', client_id, member_id);
// }
//
// Pachno.Config.Client.addMember = function (url, client_id, member_id) {
//     Pachno.Config.Collection.addMember(url, 'client', client_id, member_id);
// }
//
// Pachno.Config.Client.update = function (url, client_id) {
//     Pachno.Helpers.fetch(url, {
//         form: 'edit_client_' + client_id + '_form',
//         loading: {indicator: '#edit_client_' + client_id + '_indicator'},
//         success: {
//             hide: 'edit_client_' + client_id,
//             update: '#client_' + client_id + '_item'
//         }
//     });
// };
//
// Pachno.Config.fetchComponentUpdateHandler = function (type) {
//     return function ([$form, response]) {
//         response.json().then(function (json) {
//             if (response.ok) {
//                 const $scheme_container = $('#[data-' + type + '][data-id='+json.item.id+']');
//                 if ($scheme_container.length > 0) {
//                     $scheme_container.replaceWith(json.component);
//                 } else {
//                     const $schemes_container = $('#workflow-schemes-list');
//                     if ($schemes_container.length > 0) {
//                         $schemes_container.append(json.component);
//                     }
//                 }
//                 $form[0].reset();
//                 Pachno.Helpers.Backdrop.reset();
//             } else {
//                 $form.find('.error-container > .error').html(json.error);
//                 $form.find('.error-container').addClass('invalid');
//             }
//
//             $form.removeClass('submitting');
//             $form.find('.button.primary').prop('disabled', false);
//         })
//             .catch(function (error) {
//                 $form.find('.error-container > .error').html(error);
//                 $form.find('.error-container').addClass('invalid');
//
//                 $form.removeClass('submitting');
//                 $form.find('.button.primary').prop('disabled', false);
//             });
//     };
// };
//
// Pachno.Config.loadComponentOptions = function (options, $item) {
//     return new Promise(function (resolve, reject) {
//         const $container = $(options.container),
//             $options = $(options.options),
//             url = $item.data('options-url');
//
//         $options.html('<div><i class="fas fa-spin fa-spinner"></i></div>');
//         $container.addClass('active');
//         $container.find(options.component).removeClass('active');
//         $item.addClass('active');
//
//         fetch(url, {
//             method: 'GET'
//         })
//             .then(function (response) {
//                 response.json().then(function (json) {
//                     if (response.ok) {
//                         $options.html(json.content);
//                         Pachno.Main.updateWidgets()
//                             .then(resolve);
//                     }
//                 });
//             });
//     });
// };
//
// Pachno.Config.Workflows.Scheme.save = function (form) {
//     Pachno.Core.fetchPostHelper(form)
//         .then(Pachno.Config.fetchComponentUpdateHandler('workflow-scheme'));
// };
//
// Pachno.Config.Workflows.Scheme.remove = function (url, id) {
//     fetch(url, { method: 'POST' })
//         .then(function (response) {
//             response.json().then(function (json) {
//                 Pachno.Helpers.Dialog.dismiss();
//                 if (response.ok) {
//                     $('#[data-workflow-scheme][data-id=' + id + ']').remove();
//                 } else {
//                     Pachno.Helpers.Message.error(json.error);
//                 }
//             })
//                 .catch(function (error) {
//                     Pachno.Helpers.Dialog.dismiss();
//                     Pachno.Helpers.Message.error(error);
//                 });
//         });
// };
//
// Pachno.Config.Workflows.Workflow.save = function (form) {
//     Pachno.Core.fetchPostHelper(form)
//         .then(Pachno.Core.fetchPostDefaultFormHandler);
// };
//
// Pachno.Config.Workflows.Workflow.copy = function (url, workflow_id) {
//     Pachno.Helpers.fetch(url, {
//         form: 'copy_workflow_' + workflow_id + '_form',
//         loading: {indicator: '#copy_workflow_' + workflow_id + '_indicator'},
//         success: {
//             hide: 'copy_workflow_' + workflow_id + '_popup',
//             update: {element: 'workflows_list', insertion: true},
//             callback: Pachno.Config.Workflows._updateLinks
//         }
//     });
// };
//
// Pachno.Config.Workflows.Workflow.remove = function (url, workflow_id) {
//     Pachno.Helpers.fetch(url, {
//         form: 'delete_workflow_' + workflow_id + '_form',
//         loading: {indicator: '#delete_workflow_' + workflow_id + '_indicator'},
//         success: {
//             remove: ['delete_workflow_' + workflow_id + '_popup', 'copy_workflow_' + workflow_id + '_popup', 'workflow_' + workflow_id],
//             update: {element: 'workflows_list', insertion: true},
//             callback: Pachno.Config.Workflows._updateLinks
//         }
//     });
// };
//
// Pachno.Config.Workflows.Workflow.Step.show = function ($item) {
//     Pachno.Config.loadComponentOptions(
//         {
//             container: '#workflow-steps-container',
//             options: '#selected-workflow-step-options',
//             component: '.workflow-step'
//         },
//         $item
//     );
// };
//
// Pachno.Config.Workflows.Workflow.Step.save = function (form) {
//     Pachno.Core.fetchPostHelper(form)
//         .then(Pachno.Core.fetchPostDefaultFormHandler);
// };
//
// Pachno.Config.Workflows.Transition.save = function (form) {
//     Pachno.Core.fetchPostHelper(form)
//         .then(Pachno.Core.fetchPostDefaultFormHandler);
// };
//
// Pachno.Config.Workflows.Transition.remove = function (url, transition_id, direction) {
//     $('#transition_' + transition_id + '_delete_form').submit();
// };
//
// Pachno.Config.Workflows.Transition.Validations.add = function (url, mode, key) {
//     Pachno.Helpers.fetch(url, {
//         loading: {indicator: '#workflowtransition' + mode + 'validationrule_add_indicator'},
//         success: {
//             hide: ['no_workflowtransition' + mode + 'validationrules', 'add_workflowtransition' + mode + 'validationrule_' + key],
//             update: {element: 'workflowtransition' + mode + 'validationrules_list', insertion: true}
//         }
//     });
// }
//
// Pachno.Config.Workflows.Transition.Validations.update = function (url, rule_id) {
//     Pachno.Helpers.fetch(url, {
//         form: 'workflowtransitionvalidationrule_' + rule_id + '_form',
//         loading: {indicator: '#workflowtransitionvalidationrule_' + rule_id + '_indicator'},
//         success: {
//             hide: ['workflowtransitionvalidationrule_' + rule_id + '_cancel_button', 'workflowtransitionvalidationrule_' + rule_id + '_edit'],
//             update: '#workflowtransitionvalidationrule_' + rule_id + '_value',
//             show: ['workflowtransitionvalidationrule_' + rule_id + '_edit_button', 'workflowtransitionvalidationrule_' + rule_id + '_delete_button', 'workflowtransitionvalidationrule_' + rule_id + '_description']
//         }
//     });
// }
//
// Pachno.Config.Workflows.Transition.Validations.remove = function (url, rule_id, type, mode) {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: '#fullpage_backdrop',
//             show: 'fullpage_backdrop_indicator',
//             hide: ['fullpage_backdrop_content', 'dialog_backdrop']
//         },
//         success: {
//             remove: ['workflowtransitionvalidationrule_' + rule_id],
//             show: ['add_workflowtransition' + type + 'validationrule_' + mode],
//             callback: function () {
//                 Pachno.Helpers.Dialog.dismiss();
//             }
//         }
//     });
// }
//
// Pachno.Config.Workflows.Transition.Actions.add = function (url, key) {
//     Pachno.Helpers.fetch(url, {
//         loading: {indicator: '#workflowtransitionaction_add_indicator'},
//         success: {
//             hide: ['no_workflowtransitionactions', 'add_workflowtransitionaction_' + key],
//             update: {element: 'workflowtransitionactions_list', insertion: true}
//         }
//     });
// }
//
// Pachno.Config.Workflows.Transition.Actions.update = function (url, action_id) {
//     Pachno.Helpers.fetch(url, {
//         form: 'workflowtransitionaction_' + action_id + '_form',
//         loading: {indicator: '#workflowtransitionaction_' + action_id + '_indicator'},
//         success: {
//             hide: ['workflowtransitionaction_' + action_id + '_cancel_button', 'workflowtransitionaction_' + action_id + '_edit'],
//             update: '#workflowtransitionaction_' + action_id + '_value',
//             show: ['workflowtransitionaction_' + action_id + '_edit_button', 'workflowtransitionaction_' + action_id + '_delete_button', 'workflowtransitionaction_' + action_id + '_description']
//         }
//     });
// }
//
// Pachno.Config.Workflows.Transition.Actions.remove = function (url, action_id, type) {
//     Pachno.Helpers.fetch(url, {
//         loading: {indicator: '#workflowtransitionaction_' + action_id + '_delete_indicator'},
//         success: {
//             hide: ['workflowtransitionaction_' + action_id + '_delete', 'workflowtransitionaction_' + action_id],
//             show: ['add_workflowtransitionaction_' + type],
//             callback: function () {
//                 Pachno.Helpers.Dialog.dismiss();
//             }
//         }
//     });
// }
//
// /**
//  * Displays the workflow transition popup dialog
//  */
// Pachno.Issues.showWorkflowTransition = function (transition_id) {
//     var existing_container = $('#workflow_transition_fullpage').down('.workflow_transition');
//     if (existing_container) {
//         existing_container.hide();
//         $('#workflow_transition_container').append(existing_container);
//     }
//     var workflow_div = $('#issue_transition_container_' + transition_id);
//     $('#workflow_transition_fullpage').append(workflow_div);
//     $('#workflow_transition_fullpage').show();
//     workflow_div.appear({duration: 0.2, afterFinish: function () {
//         if ($('#duplicate_finder_transition_' + transition_id)) {
//             $('#viewissue_find_issue_' + transition_id + '_input').on('keypress', function (event) {
//                 if (event.keyCode == Event.KEY_RETURN) {
//                     Pachno.Issues.findDuplicate($('#duplicate_finder_transition_' + transition_id).val(), transition_id);
//                     event.stop();
//                 }
//             });
//         }
//
//     }});
// };
//
// Pachno.Issues.submitWorkflowTransition = function (form, callback) {
//     Pachno.Helpers.fetch(form.action, {
//         form: form,
//         loading: {
//             indicator: '#fullpage_backdrop',
//             show: 'fullpage_backdrop_indicator',
//             hide: ['fullpage_backdrop_content', 'dialog_backdrop', 'workflow_transition_fullpage']
//         },
//         success: {
//             hide: 'workflow_transition_fullpage',
//             callback: callback
//         },
//         failure: {
//             show: 'workflow_transition_fullpage'
//         }
//     });
// };
//
// Pachno.Issues.refreshRelatedIssues = function (url) {
//     if ($('#related_child_issues_inline')) {
//         Pachno.Helpers.fetch(url, {
//             loading: {indicator: '#related_issues_indicator'},
//             success: {
//                 hide: 'no_child_issues',
//                 update: {element: 'related_child_issues_inline'},
//                 callback: function () {
//                     $('#viewissue_related_issues_count').html($('#related_child_issues_inline').children().length);
//                 }
//             }
//         });
//     }
// };
//
// Pachno.Issues.findRelated = function (url) {
//     Pachno.Helpers.fetch(url, {
//         form: 'viewissue_find_issue_form',
//         loading: {indicator: '#viewissue_find_issue_indicator'},
//         success: {update: '#viewissue_relation_results'}
//     });
//     return false;
// };
//
// Pachno.Issues.findDuplicate = function (url, transition_id) {
//     Pachno.Helpers.fetch(url, {
//         data: 'searchfor=' + $('#viewissue_find_issue_' + transition_id + '_input').val(),
//         loading: {indicator: '#viewissue_find_issue_' + transition_id + '_indicator'},
//         success: {update: '#viewissue_' + transition_id + '_duplicate_results'}
//     });
// };
//
// Pachno.Issues.editTimeEntry = function (form) {
//     var url = form.action;
//     Pachno.Helpers.fetch(url, {
//         form: form,
//         loading: {
//             indicator: '#fullpage_backdrop_indicator',
//             hide: 'fullpage_backdrop_content'
//         },
//         success: {
//             callback: function (json) {
//                 $('#fullpage_backdrop_content').html(json.timeentries);
//                 $('#fullpage_backdrop_content').show();
//                 if (json.timesum == 0) {
//                     $('#no_spent_time_' + json.issue_id).show();
//                     $('#spent_time_' + json.issue_id + '_name').hide();
//                 } else {
//                     $('#no_spent_time_' + json.issue_id).hide();
//                     $('#spent_time_' + json.issue_id + '_name').show();
//                     $('#spent_time_' + json.issue_id + '_value').html(json.spenttime);
//                 }
//                 Pachno.Issues.Field.updateEstimatedPercentbar(json);
//             }
//         }
//     });
// };
//
// Pachno.Issues.deleteTimeEntry = function (url, entry_id) {
//     Pachno.Helpers.fetch(url, {
//         loading: {indicator: '#dialog_indicator'},
//         success: {
//             callback: function (json) {
//                 Pachno.Helpers.Dialog.dismiss();
//                 $('#issue_spenttime_' + entry_id).remove();
//                 if ($('#issue_spenttime_' + entry_id + '_comment'))
//                     $('#issue_spenttime_' + entry_id + '_comment').remove();
//                 if (json.timesum == 0) {
//                     $('#no_spent_time_' + json.issue_id).show();
//                     $('#spent_time_' + json.issue_id + '_name').hide();
//                 } else {
//                     $('#no_spent_time_' + json.issue_id).hide();
//                     $('#spent_time_' + json.issue_id + '_name').show();
//                     $('#spent_time_' + json.issue_id + '_value').html(json.spenttime);
//                 }
//                 Pachno.Issues.Field.updateEstimatedPercentbar(json);
//             }
//         }
//     });
// };
//
// Pachno.Issues.Field.updateEstimatedPercentbar = function (data) {
//     $('#estimated_percentbar').html(data.percentbar);
//     if ($('#no_estimated_time_' + data.issue_id).visible()) {
//         $('#estimated_percentbar').hide();
//     }
//     else {
//         $('#estimated_percentbar').show();
//     }
// };
//
// Pachno.Issues.Add = function (url, btn) {
//     var btn = btn != undefined ? $(btn) : $('#reportissue_button');
//     var data_query = '';
//
//     if (btn.dataset != undefined && btn.data('milestone-id') != undefined && parseInt(btn.data('milestone-id')) > 0) {
//         data_query += '/milestone_id/' + btn.data('milestone-id');
//     }
//
//     if (url.indexOf('issuetype') !== -1) {
//         Pachno.Helpers.Backdrop.show(url +  data_query, function () {
//             $('#reportissue_container').addClass('huge');
//             $('#reportissue_container').removeClass('large');
//         });
//     }
//     else {
//         Pachno.Helpers.Backdrop.show(url +  data_query);
//     }
// };
//
// Pachno.Issues.relate = function (url) {
//
//     Pachno.Helpers.fetch(url, {
//         form: 'viewissue_relate_issues_form',
//         loading: {indicator: '#relate_issues_indicator'},
//         success: {
//             update: {element: 'related_child_issues_inline', insertion: true},
//             hide: 'no_child_issues',
//             callback: function (json) {
//                 if ($('.milestone_details_link.selected').eq(0).find('> a:first-child').length) {
//                     $('.milestone_details_link.selected').eq(0).find('> a:first-child').trigger('click');
//                 }
//                 else {
//                     Pachno.Helpers.Backdrop.reset();
//                 }
//                 if ($('#viewissue_related_issues_count')) $('#viewissue_related_issues_count').html(json.count);
//                 if (json.count > 0 && $('#no_related_issues').visible()) $('#no_related_issues').hide();
//             }
//         }
//     });
//     return false;
// };
//
// Pachno.Issues.removeRelated = function (url, issue_id) {
//     Pachno.Helpers.fetch(url, {
//         loading: {indicator: '#related_issues_indicator'},
//         success: {
//             remove: 'related_issue_' + issue_id,
//             callback: function () {
//                 var childcount = $('#related_child_issues_inline').children().length;
//                 $('#viewissue_related_issues_count').html(childcount);
//                 if (childcount == 0) {
//                     $('#no_related_issues').show();
//                 }
//             }
//         }
//     });
// };
//
// Pachno.Issues.removeDuplicated = function (url, issue_id) {
//     Pachno.Helpers.fetch(url, {
//         loading: {indicator: '#duplicate_issues_indicator'},
//         success: {
//             remove: 'duplicated_issue_' + issue_id,
//             callback: function () {
//                 var childcount = $('#related_duplicate_issues_inline').children().length;
//                 $('#viewissue_duplicate_issues_count').html(childcount);
//                 if (childcount == 0) {
//                     $('#no_duplicated_issues').show();
//                 }
//             }
//         }
//     });
// };
//
// Pachno.Issues.move = function (form, issue_id) {
//     Pachno.Helpers.fetch(form.action, {
//         form: form,
//         loading: {
//             indicator: '#move_issue_indicator'
//         },
//         success: {
//             remove: 'issue_' + issue_id,
//             update: '#viewissue_move_issue_div'
//         }
//     });
// };
//
// Pachno.Issues._addVote = function (url, direction) {
//     var opp_direction = (direction == 'up') ? 'down' : 'up';
//
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: '#vote_' + direction + '_indicator',
//             hide: 'vote_' + direction + '_link'},
//         success: {
//             update: '#issue_votes',
//             hide: ['vote_' + direction + '_link', 'vote_' + opp_direction + '_faded'],
//             show: ['vote_' + direction + '_faded', 'vote_' + opp_direction + '_link']
//         }
//     });
// };
//
// Pachno.Issues.voteUp = function (url) {
//     Pachno.Issues._addVote(url, 'up');
// };
//
// Pachno.Issues.voteDown = function (url) {
//     Pachno.Issues._addVote(url, 'down');
// };
//
// Pachno.Issues.toggleFavourite = function (url, issue_id_user_id)
// {
//     var issue_id = new String(issue_id_user_id).indexOf('_') !== -1
//         ? issue_id_user_id.substr(0, issue_id_user_id.indexOf('_'))
//         : issue_id_user_id;
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             callback: function () {
//                 if ($('#popup_find_subscriber_' + issue_id) != null && $('#popup_find_subscriber_' + issue_id).visible() && $('#popup_find_subscriber_' + issue_id + '_spinning')) {
//                     $('#popup_find_subscriber_' + issue_id + '_spinning').show();
//                 }
//                 else {
//                     Pachno.Core._processCommonAjaxPostEvents({
//                         show: 'issue_favourite_indicator_' + issue_id_user_id,
//                         hide: ['issue_favourite_normal_' + issue_id_user_id, 'issue_favourite_faded_' + issue_id_user_id]
//                     });
//                 }
//             }
//         },
//         success: {
//             hide: 'popup_find_subscriber_' + issue_id,
//             callback: function (json) {
//                 if ($('#popup_find_subscriber_' + issue_id + '_spinning')) {
//                     $('#popup_find_subscriber_' + issue_id + '_spinning').hide();
//                 }
//                 else {
//                     Pachno.Core._processCommonAjaxPostEvents({
//                         hide: 'issue_favourite_indicator_' + issue_id_user_id,
//                     });
//                 }
//                 if ($('#issue_favourite_faded_' + issue_id_user_id)) {
//                     if (json.starred) {
//                         $('#issue_favourite_faded_' + issue_id_user_id).hide();
//                         $('#issue_favourite_indicator_' + issue_id_user_id).hide();
//                         $('#issue_favourite_normal_' + issue_id_user_id).show();
//                     } else {
//                         $('#issue_favourite_normal_' + issue_id_user_id).hide();
//                         $('#issue_favourite_indicator_' + issue_id_user_id).hide();
//                         $('#issue_favourite_faded_' + issue_id_user_id).show();
//                     }
//                 } else if (json.subscriber != '') {
//                     $('#subscribers_list').append(json.subscriber);
//                 }
//                 if (json.count != undefined && $('#subscribers_field_count')) {
//                     $('#subscribers_field_count').html(json.count);
//                 }
//             }
//         }
//     });
// }
//
// Pachno.Issues.toggleBlocking = function (url, issue_id)
// {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: '#fullpage_backdrop',
//             show: 'fullpage_backdrop_indicator',
//             hide: 'fullpage_backdrop_content'
//         },
//         success: {
//             callback: function (json) {
//                 $('#more_actions_mark_notblocking_link_' + issue_id).toggle();
//                 $('#more_actions_mark_blocking_link_' + issue_id).toggle();
//
//                 if ($('#blocking_div')) {
//                     $('#blocking_div').toggle();
//                 }
//                 if ($('#issue_' + issue_id)) {
//                     $('#issue_' + issue_id).toggleClass('blocking');
//                 }
//             }
//         }
//     });
// }
//
// Pachno.Issues.Link.add = function (url) {
//     Pachno.Helpers.fetch(url, {
//         form: 'attach_link_form',
//         loading: {
//             indicator: '#attach_link_indicator',
//             callback: function () {
//                 $('#attach_link_submit').prop('disabled', true);
//             }
//         },
//         success: {
//             reset: 'attach_link_form',
//             hide: ['attach_link', 'viewissue_no_uploaded_files'],
//             update: {element: 'viewissue_uploaded_links', insertion: true},
//             callback: function (json) {
//                 if ($('#viewissue_uploaded_attachments_count'))
//                     $('#viewissue_uploaded_attachments_count').html(json.attachmentcount);
//                 Pachno.Helpers.Backdrop.reset();
//             }
//         },
//         complete: {
//             callback: function () {
//                 $('#attach_link_submit').prop('disabled', false);
//             }
//         }
//     });
// }
//
// Pachno.Issues.Link.remove = function (url, link_id) {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: '#viewissue_links_' + link_id + '_remove_indicator',
//             hide: link_id + '_remove_link',
//             callback: Pachno.Helpers.Dialog.dismiss
//         },
//         success: {
//             remove: ['viewissue_links_' + link_id, 'viewissue_links_' + link_id + '_remove_confirm'],
//             callback: function (json) {
//                 if (json.attachmentcount == 0 && $('#viewissue_no_uploaded_files')) $('#viewissue_no_uploaded_files').show();
//                 if ($('#viewissue_uploaded_attachments_count')) $('#viewissue_uploaded_attachments_count').html(json.attachmentcount);
//             }
//         },
//         failure: {
//             show: link_id + '_remove_link'
//         }
//     });
// }
//
// Pachno.Issues.File.remove = function (url, file_id) {
//     Pachno.Core._detachFile(url, file_id, 'viewissue_files_', 'dialog_indicator');
// }
//
// Pachno.Issues.Field.setPercent = function (url, mode) {
//     Pachno.Helpers.fetch(url, {
//         loading: {indicator: '#percent_complete_spinning'},
//         success: {
//             callback: function (json) {
//                 Pachno.Main.updatePercentageLayout(json.percent);
//                 (mode == 'set') ? Pachno.Issues.markAsChanged('percent_complete') : Pachno.Issues.markAsUnchanged('percent_complete');
//             },
//             hide: 'percent_complete_change'
//         }
//     });
// }
//
// Pachno.Issues.Field.Updaters.dualFromJSON = function (issue_id, dualfield, field) {
//     if (dualfield.id == 0) {
//         $(field + '_table').hide();
//         $('#no_' + field).show();
//     } else {
//         $(field + '_content').html(dualfield.name);
//         if (field == 'status')
//             $('#status_' + issue_id + '_color').css({backgroundColor: dualfield.color});
//         else if (field == 'issuetype')
//             $('#issuetype_image').src = dualfield.src;
//         if ($('#no_' + field))
//             $('#no_' + field).hide();
//         if ($(field + '_table'))
//             $(field + '_table').show();
//     }
// }
//
// Pachno.Issues.Field.Updaters.fromObject = function (issue_id, object, field) {
//     var fn = field + '_' + issue_id + '_name';
//     var nf = 'no_' + field + '_' + issue_id;
//     if (!$(fn)) {
//         fn = field + '_name';
//         nf = 'no_' + field;
//     }
//     if ((Object.isUndefined(object.id) == false && object.id == 0) || (object.value && object.value == '')) {
//         $(fn).hide();
//         $(nf).show();
//     } else {
//         $(fn).html(object.name);
//         if (object.url)
//             $(fn).href = object.url;
//         $(nf).hide();
//         $(fn).show();
//     }
// }
//
// Pachno.Issues.Field.Updaters.timeFromObject = function (issue_id, object, values, field) {
//     var fn = field + '_' + issue_id + '_name';
//     var nf = 'no_' + field + '_' + issue_id;
//     if ($(fn) && $(nf)) {
//         if (object.id == 0) {
//             $(fn).hide();
//             $(nf).show();
//         } else {
//             $(fn).html(object.name);
//             $(nf).hide();
//             $(fn).show();
//         }
//     }
//     ['points', 'minutes', 'hours', 'days', 'weeks', 'months'].each(function (unit) {
//         if (field != 'spent_time' && $(field + '_' + issue_id + '_' + unit + '_input'))
//             $(field + '_' + issue_id + '_' + unit + '_input').value(values[unit]);
//
//         if ($(field + '_' + issue_id + '_' + unit)) {
//             $(field + '_' + issue_id + '_' + unit).html(values[unit]);
//             if (values[unit] == 0) {
//                 $(field + '_' + issue_id + '_' + unit).addClass('faded_out');
//             } else {
//                 $(field + '_' + issue_id + '_' + unit).removeClass('faded_out');
//             }
//         }
//     });
// }
//
// Pachno.Issues.Field.Updaters.allVisible = function (visible_fields) {
//     Pachno.available_fields.each(function (field)
//     {
//         if ($(field + '_field')) {
//             if (visible_fields[field] != undefined) {
//                 $(field + '_field').show();
//                 if ($(field + '_additional'))
//                     $(field + '_additional').show();
//             } else {
//                 $(field + '_field').hide();
//                 if ($(field + '_additional'))
//                     $(field + '_additional').hide();
//             }
//         }
//     });
// }
//
// /**
//  * This function is triggered every time an issue is updated via the web interface
//  * It sends a request that performs the update, and gets JSON back
//  *
//  * Depending on the JSON return value, it updates fields, shows/hides boxes on the
//  * page, and sets some class values
//  *
//  * @param url The URL to request
//  * @param field The field that is being changed
//  * @param serialize_form Whether a form is being serialized
//  */
// Pachno.Issues.Field.set = function (url, field, serialize_form) {
//     var post_form = undefined;
//     if (['description', 'reproduction_steps', 'title', 'shortname'].indexOf(field) != -1) {
//         post_form = field + '_form';
//     } else if (serialize_form != undefined) {
//         post_form = serialize_form + '_form';
//     }
//
//     var loading_show = (field == 'issuetype') ? 'issuetype_indicator_fullpage' : undefined;
//
//     Pachno.Helpers.fetch(url, {
//         form: post_form,
//         loading: {
//             indicator: loading_show != undefined ? loading_show : field + '_spinning',
//             clear: field + '_change_error',
//             hide: field + '_change_error'
//         },
//         success: {
//             callback: function (json) {
//                 if (json.field != undefined)
//                 {
//                     if (field == 'status' || field == 'issuetype')
//                         Pachno.Issues.Field.Updaters.dualFromJSON(json.issue_id, json.field, field);
//                     else if (field == 'percent_complete')
//                         Pachno.Main.updatePercentageLayout(json.percent);
//                     else if (field == 'estimated_time') {
//                         Pachno.Issues.Field.Updaters.timeFromObject(json.issue_id, json.field, json.values, field);
//                         $(field + '_' + json.issue_id + '_change').hide();
//                         Pachno.Issues.Field.updateEstimatedPercentbar(json);
//                     }
//                     else if (field == 'spent_time') {
//                         Pachno.Issues.Field.Updaters.timeFromObject(json.issue_id, json.field, json.values, field);
//                         $(field + '_' + json.issue_id + '_change').hide();
//                     }
//                     else
//                         Pachno.Issues.Field.Updaters.fromObject(json.issue_id, json.field, field);
//
//                     if (field == 'issuetype')
//                         Pachno.Issues.Field.Updaters.allVisible(json.visible_fields);
//                     else if (field == 'pain_bug_type' || field == 'pain_likelihood' || field == 'pain_effect')
//                     {
//                         $('#issue_user_pain').html(json.user_pain);
//                         if (json.user_pain_diff_text != '') {
//                             $('#issue_user_pain_calculated').html(json.user_pain_diff_text);
//                             $('#issue_user_pain_calculated').show();
//                         } else {
//                             $('#issue_user_pain_calculated').hide();
//                         }
//                     }
//                 }
//                 (json.changed == true) ? Pachno.Issues.markAsChanged(field) : Pachno.Issues.markAsUnchanged(field);
//                 if (field == 'description' && $('#description_edit')) {
//                     $('#description_edit').style.display = '';
//                 }
//                 else if (field == 'title') {
//                     $('#title-field').toggleClass('editing');
//                 }
//             },
//             hide: field + '_change'
//         },
//         failure: {
//             update: field + '_change_error',
//             show: field + '_change_error',
//             callback: function (json) {
//                 new Effect.Pulsate($(field + '_change_error'));
//             }
//         }
//     });
// }
//
// Pachno.Issues.Field.setTime = function (url, field, issue_id) {
//     Pachno.Helpers.fetch(url, {
//         form: field + '_' + issue_id + '_form',
//         loading: {
//             indicator: field + '_' + issue_id + '_spinning',
//             clear: field + '_' + issue_id + '_change_error',
//             hide: field + '_' + issue_id + '_change_error'
//         },
//         success: {
//             callback: function (json) {
//                 Pachno.Issues.Field.Updaters.timeFromObject(json.issue_id, json.field, json.values, field);
//                 (json.changed == true) ? Pachno.Issues.markAsChanged(field) : Pachno.Issues.markAsUnchanged(field);
//                 if ($('#issue_' + issue_id)) {
//                     ['points', 'hours', 'minutes'].each(function (unit) {
//                         if (field == 'estimated_time') {
//                             Pachno.Issues.Field.updateEstimatedPercentbar(json);
//                             $('#issue_' + issue_id).attr('data-estimated-' + unit, json.values[unit]);
//                             $('#issue_' + issue_id).down('.issue_estimate.' + unit).html(json.values[unit]);
//                             (parseInt(json.values[unit]) > 0) ? $('#issue_' + issue_id).down('.issue_estimate.' + unit).show() : $('#issue_' + issue_id).down('.issue_estimate.' + unit).hide();
//                         } else {
//                             $('#issue_' + issue_id).attr('data-spent-' + unit, json.values[unit]);
//                             $('#issue_' + issue_id).down('.issue_spent.' + unit).html(json.values[unit]);
//                             (parseInt(json.values[unit]) > 0) ? $('#issue_' + issue_id).down('.issue_spent.' + unit).show() : $('#issue_' + issue_id).down('.issue_spent.' + unit).hide();
//                         }
//                         $('#issue_' + issue_id).dataset.lastUpdated = get_current_timestamp();
//                     });
//                     var fields = $('#issue_' + issue_id).find('.sc_' + field);
//                     if (fields.length > 0) {
//                         fields.each(function (sc_element) {
//                             if (json.field.name) {
//                                 $(sc_element).html(json.field.name);
//                                 $(sc_element).removeClass('faded_out');
//                             } else {
//                                 $(sc_element).html('-');
//                                 $(sc_element).addClass('faded_out');
//                             }
//                         });
//                     }
//                 }
//                 if ($('#milestones-list')) {
//                     Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails($('#issue_' + issue_id).parents('.milestone-issues'));
//                     Pachno.Project.Planning.calculateNewBacklogMilestoneDetails();
//                 }
//             },
//             hide: field + '_' + issue_id + '_change'
//         },
//         failure: {
//             update: field + '_' + issue_id + '_change_error',
//             show: field + '_' + issue_id + '_change_error',
//             callback: function (json) {
//                 new Effect.Pulsate($(field + '_' + issue_id + '_change_error'));
//             }
//         }
//     });
// }
//
// Pachno.Issues.Field.revert = function (url, field)
// {
//     var loading_show = (field == 'issuetype') ? 'issuetype_indicator_fullpage' : undefined;
//
//     Pachno.Issues.markAsUnchanged(field);
//
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: loading_show != undefined ? loading_show : field + '_undo_spinning'
//         },
//         success: {
//             callback: function (json) {
//                 if (json.field != undefined) {
//                     if (field == 'status' || field == 'issuetype')
//                         Pachno.Issues.Field.Updaters.dualFromJSON(json.issue_id, json.field, field);
//                     else if (field == 'estimated_time') {
//                         Pachno.Issues.Field.Updaters.timeFromObject(json.issue_id, json.field, json.values, field);
//                         Pachno.Issues.Field.updateEstimatedPercentbar(json);
//                     }
//                     else if (field == 'spent_time')
//                         Pachno.Issues.Field.Updaters.timeFromObject(json.issue_id, json.field, json.values, field);
//                     else if (field == 'percent_complete')
//                         Pachno.Main.updatePercentageLayout(json.field);
//                     else
//                         Pachno.Issues.Field.Updaters.fromObject(json.issue_id, json.field, field);
//
//                     if (field == 'issuetype')
//                         Pachno.Issues.Field.Updaters.allVisible(json.visible_fields);
//                     else if (field == 'description' || field == 'reproduction_steps')
//                         $(field + '_form_value').html(json.field.form_value);
//                     else if (field == 'pain_bug_type' || field == 'pain_likelihood' || field == 'pain_effect')
//                         $('#issue_user_pain').html(json.field.user_pain);
//
//                     if (field == 'description') {
//                         $('#description_edit').style.display = '';
//                         $('#description_change').hide();
//                     }
//                 }
//
//             }
//         },
//         failure: {
//             callback: function () {
//                 Pachno.Issues.markAsChanged(field);
//             }
//         }
//     });
// }
//
// Pachno.Issues.Field.incrementTimeMinutes = function (minutes, input)
// {
//     if (minutes > 60 || minutes < 0) return;
//
//     var hour_input = input.replace('minutes', 'hours');
//
//     // Increment hour by one for 60 minutes
//     if (minutes == 60 && $(hour_input)) {
//       $(hour_input).value((parseInt($(hour_input).val()) || 0) + 1);
//       return;
//     }
//
//     if (! $(input)) return;
//
//     var new_minutes = (parseInt($(input).val()) || 0) + minutes;
//
//     if (new_minutes >= 60 && $(hour_input)) {
//         $(hour_input).value((parseInt($(hour_input).val()) || 0) + 1);
//         new_minutes = new_minutes - 60;
//     }
//
//     $(input).value(new_minutes);
// }
//
// Pachno.Issues.markAsChanged = function (field)
// {
//     if ($('#viewissue_changed') != undefined) {
//         if (!$('#viewissue_changed').visible()) {
//             $('#viewissue_changed').show();
//             Effect.Pulsate($('#issue-messages-container'), {pulses: 3, duration: 2});
//         }
//
//         $(field + '_field').addClass('issue_detail_changed');
//         if (field == 'issuetype') {
//             $("#workflow-actions input[type='submit'], #workflow-actions input[type='button']").prop("disabled", true);
//             $("#workflow-actions a").off('click');
//         }
//     }
//
//     if ($('#comment_save_changes'))
//         $('#comment_save_changes').checked = true;
// }
//
// Pachno.Issues.markAsUnchanged = function (field)
// {
//     if ($(field + '_field') && $('#issue_view')) {
//         $(field + '_field').removeClass('issue_detail_changed');
//         $(field + '_field').removeClass('issue_detail_unmerged');
//         if ($('#issue_view').find('.issue_detail_changed').length == 0) {
//             $('#viewissue_changed').hide();
//             $('#viewissue_merge_errors').hide();
//             $('#viewissue_unsaved').hide();
//             if ($('#comment_save_changes'))
//                 $('#comment_save_changes').checked = false;
//         }
//         if (field == 'issuetype') {
//             $("#workflow-actions input[type='submit'], #workflow-actions input[type='button']").prop("disabled", false);
//             $("#workflow-actions a").on('click');
//         }
//     }
// }
//
// Pachno.Issues.ACL.toggle_checkboxes = function (element, issue_id, val) {
//     if (! $(element).is(':checked')) return;
//
//     switch (val) {
//         case 'public':
//             $('#acl_' + issue_id + '_public').show();
//             $('#acl_' + issue_id + '_restricted').hide();
//             $('#issue_' + issue_id + '_public_category_access_list').hide();
//             $('#issue_access_public_category_input').prop('disabled', true);
//             $('#acl-users-teams-selector').hide();
//             break;
//         case 'public_category':
//             $('#acl_' + issue_id + '_public').show();
//             $('#acl_' + issue_id + '_restricted').hide();
//             $('#issue_' + issue_id + '_public_category_access_list').show();
//             $('#issue_access_public_category_input').prop('disabled', false);
//             $('#acl-users-teams-selector').show();
//             break;
//         case 'restricted':
//             $('#acl_' + issue_id + '_public').hide();
//             $('#acl_' + issue_id + '_restricted').show();
//             $('#acl-users-teams-selector').show();
//             break;
//     }
// };
//
// Pachno.Issues.ACL.toggle_custom_access = function (element) {
//     if ($(element).is(':checked')) {
//         $('.report-issue-custom-access-container').show();
//         $('.report-issue-custom-access-container input[name=issue_access]').trigger('change');
//     }
//     else {
//         $('.report-issue-custom-access-container').hide();
//     }
// };
//
// Pachno.Issues.ACL.addTarget = function (url, issue_id) {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: '#popup_find_acl_' + issue_id + '_spinning'
//         },
//         success: {
//             update: {},
//             callback: function(json) {
//                 $('#issue_' + issue_id + '_restricted_access_list').append(json.content);
//                 $('#issue_' + issue_id + '_public_category_access_list').append(json.content);
//                 $('#issue_' + issue_id + '_restricted_access_list_none').hide();
//                 $('#issue_' + issue_id + '_public_category_access_list_none').hide();
//             },
//             hide: 'popup_find_acl_' + issue_id
//         }
//     });
// };
//
// Pachno.Issues.ACL.set = function (url, issue_id, mode) {
//     Pachno.Helpers.fetch(url, {
//         form: 'acl_' + issue_id + '_' + mode + 'form',
//         loading: {
//             indicator: '#fullpage_backdrop',
//             show: 'fullpage_backdrop_indicator',
//             hide: ['fullpage_backdrop_content', 'dialog_backdrop']
//         },
//         success: {
//             callback: Pachno.Helpers.Backdrop.reset
//         }
//     });
// };
//
// Pachno.Issues.Affected.toggleConfirmed = function (url, affected)
// {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             callback: function () {
//                 $('#affected_' + affected + '_state').parents('.affected-state').addClass('loading');
//             }
//         },
//         success: {
//             callback: function (json) {
//                 $('#affected_' + affected + '_state').html(json.text);
//                 $('#affected_' + affected + '_state').parents('.affected-state').toggleClass('unconfirmed');
//                 $('#affected_' + affected + '_state').parents('.affected-state').toggleClass('confirmed');
//                 $('#affected_' + affected + '_state').parents('.affected-state').removeClass('loading');
//             }
//         },
//         complete: {
//             callback: function () {
//                 $('#affected_' + affected + '_state').parents('.affected-state').removeClass('loading');
//             }
//         }
//     });
// }
//
// Pachno.Issues.Affected.remove = function (url, affected)
// {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: '#fullpage_backdrop',
//             show: 'fullpage_backdrop_indicator',
//             hide: ['fullpage_backdrop_content', 'dialog_backdrop']
//         },
//         success: {
//             update: {element: 'viewissue_affects_count', from: 'itemcount'},
//             remove: ['affected_' + affected + '_delete', 'affected_' + affected],
//             callback: function (json) {
//                 if (json.itemcount == 0)
//                     $('#no_affected').show();
//             }
//         }
//     });
// }
//
// Pachno.Issues.Affected.setStatus = function (url, affected)
// {
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: '#affected_' + affected + '_status_spinning'
//         },
//         success: {
//             callback: function (json) {
//                 $('#affected_' + affected + '_status').css({
//                     backgroundColor: json.colour,
//                 });
//             },
//             update: {element: 'affected_' + affected + '_status', from: 'name'},
//             hide: 'affected_' + affected + '_status_change'
//         },
//         failure: {
//             update: {element: 'affected_' + affected + '_status_error', from: 'error'},
//             show: 'affected_' + affected + '_status_error',
//             callback: function (json) {
//                 new Effect.Pulsate($('#affected_' + affected + '_status_error'));
//             }
//         }
//     });
// }
//
// Pachno.Issues.Affected.add = function (url)
// {
//     Pachno.Helpers.fetch(url, {
//         form: 'viewissue_add_item_form',
//         loading: {
//             indicator: '#add_affected_spinning'
//         },
//         success: {
//             callback: function (json) {
//                 if ($('#viewissue_affects_count'))
//                     $('#viewissue_affects_count').html(json.itemcount);
//                 if (json.itemcount != 0 && $('#no_affected'))
//                     $('#no_affected').hide();
//                 Pachno.Helpers.Backdrop.reset();
//             },
//             update: {element: 'affected_list', insertion: true},
//         }
//     });
// }
//
// Pachno.Issues.updateWorkflowAssignee = function (url, assignee_id, assignee_type, transition_id, teamup)
// {
//     teamup = (teamup == undefined) ? 0 : 1;
//     Pachno.Helpers.fetch(url, {
//         loading: {
//             indicator: '#popup_assigned_to_name_indicator_' + transition_id,
//             hide: 'popup_no_assigned_to_' + transition_id,
//             show: 'popup_assigned_to_name_' + transition_id
//         },
//         success: {
//             update: '#popup_assigned_to_name_' + transition_id
//         },
//         complete: {
//             callback: function () {
//                 $('#popup_assigned_to_id_' + transition_id).value(assignee_id);
//                 $('#popup_assigned_to_type_' + transition_id).value(assignee_type);
//                 $('#popup_assigned_to_teamup_' + transition_id).value(teamup);
//                 if (teamup) {
//                     $('#popup_assigned_to_teamup_info_' + transition_id).show();
//                 } else {
//                     $('#popup_assigned_to_teamup_info_' + transition_id).hide();
//                 }
//             },
//             hide: ['popup_assigned_to_teamup_info_' + transition_id, 'popup_assigned_to_change_' + transition_id]
//         }
//     });
// }
//
// Pachno.Issues.updateWorkflowAssigneeTeamup = function (url, assignee_id, assignee_type, transition_id)
// {
//     Pachno.Issues.updateWorkflowAssignee(url, assignee_id, assignee_type, transition_id, true);
// }
//
// Pachno.Search.deleteSavedSearch = function (url, id) {
//     Pachno.Helpers.fetch(url, {
//         loading: {indicator: '#delete_search_' + id + '_indicator'},
//         success: {hide: 'saved_search_' + id + '_container'}
//     });
// };
//
// Pachno.Search.toPage = function (url, parameters, offset, button) {
//     parameters += '&offset=' + offset;
//     Pachno.Helpers.fetch(url, {
//         params: parameters,
//         loading: {
//             callback: function() {
//                 $(this).addClass('submitting');
//             }
//         },
//         success: {
//             update: '#search-results',
//             callback: function() {
//                 $(this).removeClass('submitting');
//             }
//         }
//     });
// };
//
// Pachno.Search.bulkContainerChanger = function () {
//     var selected_radio_value = $('#input[name=search_bulk_action]:checked', '#search-bulk-action-form').val(),
//         sub_container_id = 'bulk_action_subcontainer_' + selected_radio_value;
//
//     $('.bulk_action_subcontainer').each(function (element) {
//         element.hide();
//     });
//     if ($(sub_container_id)) {
//         $(sub_container_id).show();
//         $('#bulk_action_submit').removeClass('disabled');
//         var dropdown_element = $(sub_container_id + '').down('.focusable');
//         if (dropdown_element != undefined)
//             dropdown_element.focus();
//     } else {
//         $('#bulk_action_submit').addClass('disabled');
//     }
// };
//
// Pachno.Search.bulkChanger = function (mode) {
//     var sub_container_id = 'bulk_action_' + $('#bulk_action_selector_' + mode).val();
//     var opp_mode = (mode == 'top') ? 'bottom' : 'top';
//
//     if ($('#bulk_action_selector_' + mode).val() == '') {
//         $('#bulk_action_submit_' + mode).addClass('disabled');
//         $('#bulk_action_submit_' + opp_mode).addClass('disabled');
//     } else if (!$('#search-bulk-actions_' + mode).hasClass('unavailable')) {
//         $('#bulk_action_submit_' + mode).removeClass('disabled');
//         $('#bulk_action_submit_' + opp_mode).removeClass('disabled');
//     }
//     $(sub_container_id + '_' + opp_mode).val($(sub_container_id + '_' + mode).val());
// }
//
// Pachno.Search.bulkPostProcess = function (json) {
//     if (json.last_updated) {
//         if (json.milestone_name != undefined && json.milestone_id) {
//             if ($('#milestones-list') != undefined) {
//                 if ($('#milestone_' + json.milestone_id) == undefined) {
//                     Pachno.Project.Milestone.retrieve(json.milestone_url, json.milestone_id, json.issue_ids);
//                 }
//             }
//             if ($('#bulk_action_assign_milestone_top') != undefined && $('#bulk_action_assign_milestone_top_' + json.milestone_id) == undefined) {
//                 $('#bulk_action_assign_milestone_top').append('<option value="' + json.milestone_id + '" id="bulk_action_assign_milestone_top_' + json.milestone_id + '">' + json.milestone_name + '</option>');
//                 $('#bulk_action_assign_milestone_top').value(json.milestone_id);
//                 $('#bulk_action_assign_milestone_top_name').hide();
//             }
//             if ($('#bulk_action_assign_milestone_bottom') != undefined && $('#bulk_action_assign_milestone_bottom_' + json.milestone_id) == undefined) {
//                 $('#bulk_action_assign_milestone_bottom').append('<option value="' + json.milestone_id + '" id="bulk_action_assign_milestone_bottom_' + json.milestone_id + '">' + json.milestone_name + '</option>');
//                 $('#bulk_action_assign_milestone_bottom').value(json.milestone_id);
//                 $('#bulk_action_assign_milestone_bottom_name').hide();
//             }
//         }
//         json.issue_ids.each(function (issue_id) {
//             var issue_elm = $('#issue_' + issue_id);
//             if (issue_elm != undefined) {
//                 if (json.milestone_name != undefined) {
//                     var milestone_container = issue_elm.down('.sc_milestone');
//                     if (milestone_container != undefined) {
//                         milestone_container.html(json.milestone_name);
//                         if (json.milestone_name != '-') {
//                             milestone_container.removeClass('faded_out');
//                         } else {
//                             milestone_container.addClass('faded_out');
//                         }
//                     }
//                 }
//                 if (json.status != undefined) {
//                     var status_container = issue_elm.down('.sc_status');
//                     if (status_container != undefined) {
//                         status_container.down('.sc_status_name').html(json.status['name']);
//                         var status_color_item = status_container.down('.sc_status_color');
//                         if (status_color_item)
//                             status_color_item.css({backgroundColor: json.status['color']});
//                     }
//                 }
//                 ['resolution', 'priority', 'category', 'severity'].each(function (action) {
//                     if (json[action] != undefined) {
//                         var data_container = issue_elm.down('.sc_' + action);
//                         if (data_container != undefined) {
//                             data_container.html(json[action]['name']);
//                             if (json[action]['name'] != '-') {
//                                 data_container.removeClass('faded_out');
//                             } else {
//                                 data_container.addClass('faded_out');
//                             }
//                         }
//                         if ($(action + '_selector_' + issue_id) != undefined) {
//                             $(action + '_selector_' + issue_id).value(json[action]['id']);
//                         }
//                     }
//                 });
//                 var last_updated_container = issue_elm.down('.sc_last_updated');
//                 if (last_updated_container != undefined) {
//                     last_updated_container.html(json.last_updated);
//                 }
//                 if (json.closed != undefined) {
//                     if (json.closed) {
//                         issue_elm.addClass('closed');
//                     } else {
//                         issue_elm.removeClass('closed');
//                     }
//                 }
//             }
//         });
//         Pachno.Search.liveUpdate(true);
//     }
// }
//
// Pachno.Search.interactiveWorkflowTransition = function (url, transition_id, form) {
//     Pachno.Helpers.fetch(url, {
//         form: form,
//         loading: {
//             indicator: '#transition_working_' + transition_id + '_indicator',
//             callback: function () {
//                 $('.workflow_transition_submit_button').each(function (element) {
//                     $(element).addClass('disabled');
//                     $(element).writeAttribute('disabled');
//                 });
//             }
//         },
//         success: {
//             callback: function (json) {
//                 Pachno.Core.Pollers.Callbacks.planningPoller();
//                 Pachno.Helpers.Backdrop.reset();
//                 Pachno.Search.liveUpdate(true);
//             }
//         },
//         complete: {
//             callback: function () {
//                 $('.workflow_transition_submit_button').each(function (element) {
//                     $(element).removeClass('disabled');
//                     $(element).writeAttribute('disabled', false);
//                 });
//             }
//         }
//     });
// }
//
// Pachno.Search.nonInteractiveWorkflowTransition = function () {
//     // No need to remove 'disabled' class and attribute since form that is submitted
//     // will refresh page.
//     $('.workflow_transition_submit_button').each(function (element) {
//         $(element).addClass('disabled');
//         $(element).writeAttribute('disabled');
//     });
// }
//
// Pachno.Search.bulkWorkflowTransition = function (url, transition_id) {
//     Pachno.Helpers.fetch(url, {
//         form: 'bulk_workflow_transition_form',
//         loading: {
//             indicator: '#transition_working_' + transition_id + '_indicator',
//             callback: function () {
//                 $('.workflow_transition_submit_button').each(function (element) {
//                     $(element).addClass('disabled');
//                     $(element).writeAttribute('disabled');
//                 });
//             }
//         },
//         success: {
//             callback: function (json) {
//                 Pachno.Search.bulkPostProcess(json)
//                 Pachno.Helpers.Backdrop.reset();
//             }
//         },
//         complete: {
//             callback: function () {
//                 $('.workflow_transition_submit_button').each(function (element) {
//                     $(element).removeClass('disabled');
//                     $(element).writeAttribute('disabled', false);
//                 });
//             }
//         }
//     });
// };
//
// Pachno.Search.bulkUpdate = function (url) {
//     if ($('#bulk_action_selector').val() == '')
//         return;
//     var issues = '';
//     $('#search-results').find('tbody input[type=checkbox]').each(function (element) {
//         if (element.checked)
//             issues += '&issue_ids[' + element.val() + ']=' + element.val();
//     });
//
//     if ($('#bulk_action_selector').val() == 'perform_workflow_step') {
//         Pachno.Helpers.Backdrop.show($('#bulk_action_subcontainer_perform_workflow_step_url').val() + issues);
//     } else {
//         Pachno.Helpers.fetch(url, {
//             form: 'search-bulk-action-form',
//             data: issues,
//             loading: {
//                 indicator: '#fullpage_backdrop',
//                 show: 'fullpage_backdrop_indicator',
//                 hide: 'fullpage_backdrop_content'
//             },
//             success: {
//                 callback: Pachno.Search.bulkPostProcess
//             }
//         });
//     }
// };
//
// Pachno.Search.download = function (format) {
//     var fif = $('#find_issues_form');
//     var parameters = fif.serialize();
//     window.location = fif.dataset.historyUrl + '?' + parameters + '&format=' + format;
// };
//
// Pachno.Search.saveSearch = function () {
//     var fif = $('#find_issues_form');
//     var find_parameters = fif.serialize();
//     var ssf = $('#save_search_form');
//     var p = find_parameters + '&' + ssf.serialize();
//
//     var button = ssf.down('input[type=submit]');
//     Pachno.Helpers.fetch(ssf.action, {
//         params: p,
//         loading: {
//             indicator: '#save_search_indicator',
//             callback: function () {
//                 button.prop('disabled', true);
//             }
//         },
//         complete: {
//             callback: function () {
//                 button.prop('disabled', false);
//             }
//         }
//     });
// };
//
// Pachno.Main.loadParentArticles = function (form) {
//     Pachno.Helpers.fetch(form.action, {
//         params: $(form).serialize(),
//         loading: {
//             indicator: '#parent_selector_container_indicator',
//         },
//         complete: {
//             callback: function (json) {
//                 $('#parent_articles_list').html(json.list);
//             }
//         }
//     });
// };
//
// Pachno.Main.initializeMentionable = function (textarea) {
//     if ($(textarea).hasClass('mentionable') && !$(textarea).hasClass('mentionable-initialized')) {
//         Pachno.Helpers.fetch(Pachno.data_url, {
//             method: 'GET',
//             params: 'say=get_mentionables&target_type=' + $(textarea).dataset.targetType + '&target_id=' + $(textarea).dataset.targetId,
//             success: {
//                 callback: function (json) {
//                     $('#' + textarea.id).mention({
//                         delimiter: '@',
//                         sensitive: true,
//                         emptyQuery: true,
//                         queryBy: ['name', 'username'],
//                         typeaheadOpts: {
//                             items: 10 // Max number of items you want to show
//                         },
//                         users: json.mentionables
//                     });
//                     $(textarea).addClass('mentionable-initialized');
//                 }
//             }
//         });
//     }
//     ;
// };
//
// Pachno.Helpers.initializeColorPicker = function () {
//     $('#input.color').each(function (index, element) {
//         var input = $(element);
//         input.spectrum({
//             cancelText: input.data('cancel-text'),
//             chooseText: input.data('choose-text'),
//             showInput: true,
//             preferredFormat: 'hex'
//         });
//     });
// };
//
// Pachno.Core.getPluginUpdates = function (type) {
//     var params = '',
//         plugins = $('#installed-'+type+'s-list').children();
//     plugins.each(function (plugin) {
//         if (type == 'theme' || !plugin.hasClass('disabled')) {
//             params += '&addons[]=' + plugin.dataset[type+'Key'];
//         }
//     });
//     Pachno.Helpers.fetch($('#main_container').data('url'), {
//         method: 'GET',
//         params: 'say=get_'+type+'_updates' + params,
//         loading: {
//             indicator: '#installed_'+type+'s_indicator'
//         },
//         success: {
//             update: '#installed_'+type+'s_indicator',
//             callback: function (json) {
//                 plugins.each(function (plugin) {
//                     if (json[plugin.dataset[type+'Key']] !== undefined) {
//                         if (plugin.dataset.version != json[plugin.dataset[type+'Key']].version) {
//                             plugin.addClass('can-update');
//                             var link = $(type + '_'+plugin.dataset[type+'Key']+'_download_location');
//                             link.attr('href', json[plugin.dataset[type+'Key']].download);
//                             $('body').on('click', '.update-'+type+'-menu-item', function (e) {
//                                 var pluginbox = $(this).parents('li.'+type);
//                                 $('#update_'+type+'_help_' + pluginbox.data('id')).show();
//                                 if (!Pachno.Core.Pollers.pluginupdatepoller)
//                                     Pachno.Core.Pollers.pluginupdatepoller = new PeriodicalExecuter(Pachno.Core.validatePluginUpdateUploadedPoller(type, pluginbox.data('module-key')), 5);
//                             });
//                         }
//                     }
//                 })
//             }
//         },
//         failure: {
//             callback: function (response) {
//             }
//         }
//     });
// };
//
// Pachno.Core.cancelManualUpdatePoller = function () {
//     Pachno.Core.Pollers.Locks.pluginupdatepoller = false;
//     if (Pachno.Core.Pollers.pluginupdatepoller) {
//         Pachno.Core.Pollers.pluginupdatepoller.stop();
//         Pachno.Core.Pollers.pluginupdatepoller = undefined;
//     }
// };
//
// Pachno.Core.validatePluginUpdateUploadedPoller = function (type, pluginkey) {
//     return function () {
//         if (!Pachno.Core.Pollers.Locks.pluginupdatepoller) {
//             Pachno.Core.Pollers.Locks.pluginupdatepoller = true;
//             Pachno.Helpers.fetch($('#main_container').data('url'), {
//                 method: 'GET',
//                 params: '&say=verify_'+type+'_update_file&'+type+'_key='+pluginkey,
//                 success: {
//                     callback: function (json) {
//                         if (json.verified == '1') {
//                             $('#'+type+'_'+pluginkey+'_perform_update').children('input[type=submit]').prop('disabled', false);
//                             Pachno.Core.cancelManualUpdatePoller();
//                         }
//                         Pachno.Core.Pollers.Locks.pluginupdatepoller = false;
//                     }
//                 },
//                 exception: {
//                     callback: function () {
//                         Pachno.Core.Pollers.Locks.pluginupdatepoller = false;
//                     }
//                 }
//             });
//         }
//     }
// };
//
// Pachno.Core.getAvailablePlugins = function (type, callback) {
//     Pachno.Helpers.fetch($('#main_container').data('url'), {
//         method: 'GET',
//         params: '&say=get_'+type,
//         loading: {
//             indicator: '#available_'+type+'_loading_indicator'
//         },
//         success: {
//             update: '#available_'+type+'_container',
//             callback: function () {
//                 $('body').on('click', '.install-button', callback);
//             }
//         }
//     });
// };
//
// Pachno.Core.installPlugin = function (button, type) {
//     button = $(button);
//     button.addClass('installing');
//     button.prop('disabled', true);
//     Pachno.Helpers.fetch($('#main_container').data('url'), {
//         method: 'POST',
//         params: '&say=install-'+type+'&'+type+'_key='+button.data('key'),
//         success: {
//             callback: function (json) {
//                 if (json.installed) {
//                     $('#online-'+type+'-' + json[type+'_key']).addClass('installed');
//                     $('#installed-'+type+'s-list').append(json[type]);
//                 }
//             }
//         },
//         failure: {
//             callback: function () {
//                 button.removeClass('installing');
//                 button.prop('disabled', false);
//             }
//         }
//     });
// };
//
// Pachno.Modules.getModuleUpdates = function () {
//     Pachno.Core.getPluginUpdates('module');
// };
//
// Pachno.Modules.getAvailableOnline = function () {
//     Pachno.Core.getAvailablePlugins('modules', Pachno.Modules.install);
// };
//
// Pachno.Modules.install = function (event) {
//     Pachno.Core.installPlugin(this, 'module');
// };
//
// Pachno.Themes.getThemeUpdates = function () {
//     Pachno.Core.getPluginUpdates('theme');
// };
//
// Pachno.Themes.getAvailableOnline = function () {
//     Pachno.Core.getAvailablePlugins('themes', Pachno.Themes.install);
// };
//
// Pachno.Themes.install = function (event) {
//     Pachno.Core.installPlugin(this, 'theme');
// };
