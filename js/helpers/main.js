import $ from "jquery";
import UI from "./helpers";
const Main = {
    Profile: {
        toggleNotifications: (toggle_classes) => {
            let un = $('#user_notifications');
            let unc = $('#user_notifications_container');
            if (! un || ! unc) return false;
            if (toggle_classes == null) toggle_classes = true;
            if (toggle_classes) unc.toggleClass('active');
            if (un.hasClass('active')) {
                un.removeClass('active');
            } else {
                if (toggle_classes) un.addClass('active');
                if ($('#user_notifications_list').children().length == 0) {
                    Pachno.Helpers.fetch($('#user_notifications_list').data('notifications-url'), {
                        method: 'GET',
                        loading: {
                            indicator: 'user_notifications_loading_indicator'
                        },
                        success: {
                            update: '#user_notifications_list',
                            callback: function () {
                                $('#user_notifications_list_wrapper_nano').nanoScroller();
                                $('#user_notifications_list_wrapper_nano').bind('scrollend', Pachno.Main.Notifications.loadMore);
                            }
                        }
                    });
                }
            }
        }
    },
    Notifications: {
        loadMore: function (event, _loadToTop) {
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
                        UI.fetch(url, {
                            method: 'GET',
                            loading: {
                                indicator: 'user_notifications_loading_indicator'
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
        },
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

};

export default Main;
