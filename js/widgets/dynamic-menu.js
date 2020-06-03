import $ from "jquery";
import Pachno from "../classes/pachno";

const loadDynamicMenu = function ($menu) {
    if ($menu.hasClass('populate-once') && $menu.data('is-loaded')) {
        return;
    }

    const url = $menu.data('menu-url');
    Pachno.fetch(url, {
        method: 'GET',
        success: {
            callback: function (json) {
                $menu.replaceWith(json.menu);
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
