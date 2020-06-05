import $ from "jquery";
import Pachno from "../classes/pachno";

const initDashboard = function ($view) {
    const view_id = parseInt($view.data('view-id'));
    const dashboard_container = $view.parents('.dashboard');
    const url = dashboard_container.data('url').replace('{view_id}', view_id);

    if ($view.data('preloaded') == "0") {
        Pachno.fetch(url, {
            method: 'GET',
            loading: {indicator: '#dashboard_view_' + view_id + '_indicator'},
            success: {update: '#dashboard_view_' + view_id}
        });
    }
};

const addViewPopup = function (event, element) {
    event.stopPropagation();
    const backdrop_url = element.parents('.dashboard').data('add-view-url') + '&column=' + element.parents('.dashboard_column').data('column');
    Pachno.UI.Backdrop.show(backdrop_url);
};

const initializeDashboards = function () {
    let dashboardPromises = [];

    $('.dashboard_view_container').each(function () {
        let $view = $(this);
        if ($view.data('view-id')) {
            dashboardPromises.push(initDashboard($view));
        }
    });

    return Promise.all(dashboardPromises);
};

const setupDashboardListeners = function () {
    $('body').on('click', '.dashboard_add_view_container', addViewPopup);
};

export {
    initializeDashboards,
    setupDashboardListeners
};
