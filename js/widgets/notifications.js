import $ from "jquery";
import Pachno from "../classes/pachno";

const loadMoreNotifications = function (event, _loadToTop) {
    let loadToTop = _loadToTop || false;
    if (Main.Notifications.loadingLocked !== true || loadToTop) {
        if (! loadToTop) Main.Notifications.loadingLocked = true;
        var unl = $('#user_notifications_list'),
            unl_data = unl.dataset;
        if (unl) {
            if (loadToTop && unl.find('li').length) {
                var url = unl_data.notificationsUrl+'&first_notification_id='+unl.find('li:not(.disabled)')[0].data('notification-id');
            }
            else if (! loadToTop && unl.find("li:not(.disabled):last-child") != undefined && unl.find("li:not(.disabled):last-child")[0] != undefined) {
                var url = unl_data.notificationsUrl+'&last_notification_id='+unl.find("li:not(.disabled):last-child")[0].data('notification-id');
            }
            if (url != undefined) {
                Pachno.fetch(url, {
                    method: 'GET',
                    loading: {
                        indicator: '#user_notifications_loading_indicator'
                    },
                    success: {
                        update: { element: '', insertion: true },
                        callback: function (json) {
                            if (loadToTop) {
                                if ($('.faded_out', unl).length) {
                                    unl.html(json.content);
                                }
                                else {
                                    unl.prepend(json.content);
                                }
                            }
                            else {
                                if ($('.faded_out', unl).length) {
                                    unl.html(json.content);
                                }
                                else {
                                    unl.append(json.content);
                                }
                            }
                            if ($('#user_notifications_list_wrapper_nano')) $("#user_notifications_list_wrapper_nano").nanoScroller();
                            if (! loadToTop) Main.Notifications.loadingLocked = false;
                        }
                    },
                    exception: {
                        callback: function () {
                            if (! loadToTop) Main.Notifications.loadingLocked = false;
                        }
                    }
                });
            }
        }
    }
}

const loadNotifications = function () {
    if ($('#user_notifications_list').children().length == 0) {
        Pachno.fetch($('#user_notifications_list').data('notifications-url'), {
            method: 'GET',
            loading: {
                indicator: '#user_notifications_loading_indicator'
            },
            success: {
                update: '#user_notifications_list',
                callback: function () {
                    $('#user_notifications_list_wrapper_nano').on('scrollend', loadMoreNotifications);
                }
            }
        });
    }
};

const toggleNotifications = function (toggle_classes) {
    let $user_notifications = $('#user_notifications');
    let $user_notifications_container = $('#user_notifications_container');
    if (!$user_notifications.length || !$user_notifications_container.length) {
        return false;
    }

    if (toggle_classes == null) toggle_classes = true;
    if (toggle_classes) $user_notifications_container.toggleClass('active');
    if ($user_notifications.hasClass('active')) {
        $user_notifications.removeClass('active');
    } else {
        if (toggle_classes) $user_notifications.addClass('active');
        loadNotifications();
    }
};

const setupListeners = function () {
    $("body").on("click", "#user_notifications_container", toggleNotifications);
    Pachno.fetch(Pachno.data_url, {
        method: 'GET',
        success: {
            callback: function (json) {
                const $user_notifications_count = $('#user_notifications_count');
                if ($user_notifications_count.length) {
                    $user_notifications_count.html(json.unread_notifications_count);
                    if (parseInt(json.unread_notifications_count) > 0) {
                        $user_notifications_count.addClass('unread');
                    }
                }
            }
        }
    });
};

// Pachno.Main.Notifications.Web.GrantPermissionOrSendTest = function (title, body, icon) {
//     if (!Notify.needsPermission) {
//         Pachno.Main.Notifications.Web.Send(title, body, 'test', icon);
//     } else if (Notify.isSupported()) {
//         Notify.requestPermission();
//     }
// }
//
// Pachno.Main.Notifications.Web.Send = function (title, body, tag, icon, click_callback) {
//     if (Notify.needsPermission) return;
//
//     new Notify(title, {
//         body: body,
//         tag: tag,
//         icon: icon,
//         timeout: 8,
//         closeOnClick: true,
//         notifyClick: click_callback
//     }).show();
// }
//

export default setupListeners;