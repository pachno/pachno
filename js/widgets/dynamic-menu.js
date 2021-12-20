import $ from "jquery";
import Pachno from "../classes/pachno";

const loadDynamicMenu = function ($menu) {
    if ($menu.hasClass('populate-once') && $menu.data('is-loaded')) {
        return;
    }
    const populateOnce = $menu.hasClass('populate-once');

    const url = $menu.data('menu-url');
    Pachno.fetch(url, {
        method: 'GET',
        success: {
            callback: function (json) {
                const $newMenu = json.menu !== undefined ? $(json.menu) : $(json.content);
                $newMenu.data('menu-url', url);
                if (populateOnce) {
                    $newMenu.addClass('populate-once');
                }
                $menu.replaceWith($newMenu);
            }
        }
    });
};

const setupListeners = function () {
    $("body").on("click", ".dynamic_menu_link", function (e) {
        let $menu = $(this).next();
        if (!$menu.length) {
            $menu = $(this).parent().next();
        }
        if ($menu.length && $menu.hasClass('dynamic_menu')) {
            loadDynamicMenu($menu);
        }
    });
};

export default setupListeners;
