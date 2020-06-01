import $ from "jquery";
import OpenID from "../helpers/openid";
import Debugger from "./debugger";
import UI from "../helpers/ui";
import {fetchHelper} from "../helpers/fetch";
import widgetSetupListeners from "../widgets";

class PachnoApplication {

    get EVENTS() {
        return {
            ready: 'pachno-ready'
        }
    }

    constructor() {
        this.debug = false;
        this.basepath = '';
        this.data_url = '';
        this.autocompleter_url = '';
        this.debugger = undefined;
        this.listeners = {};
    }

    initialize(options) {
        this.debug = options.debug;
        this.basepath = options.basepath;
        this.data_url = options.dataUrl;
        this.autocompleter_url = options.autocompleterUrl;

        if (this.debug) {
            this.debugger = new Debugger(options.debugUrl);
        }

        this._initialize();
    }

    get UI() {
        return {
            ...UI
        }
    }

    fetch(url, options) {
        return fetchHelper(url, options);
    }

    on(key, callback) {
        if (this.listeners[key] === undefined) {
            this.listeners[key] = [];
        }

        this.listeners[key].push(callback);
    }

    trigger(key, data) {
        if (this.listeners[key] === undefined) {
            return;
        }

        for (let callback of this.listeners[key]) {
            callback(this, data);
        }
    }

    setupListeners() {
        // $(window).on('resize', Core._resizeWatcher);
        // $(document).on('keydown', Core._escapeWatcher);
        widgetSetupListeners();
        // $('#fullpage_backdrop_content').on('click', Core._resizeWatcher);
    }

    _initialize() {
        this.setupListeners();
        // Core._initializeAutocompleter();
        // $(window).on('scroll', Pachno.Core._scrollWatcher);
        // Core._resizeWatcher();
        // Core._scrollWatcher();
        if ($('.dashboard_view_container').length > 0) {
            $('.dashboard_view_container').each(function () {
                let view = $(this);
                // Pachno.Main.Dashboard.View.init(parseInt(view.data('view-id')));
            });
        } else {
            $('html').css({'cursor': 'default'});
        }

        // Core.Pollers.Callbacks.dataPoller();
        //Pachno.Main.Profile.toggleNotifications(false);
        OpenID.init();

        // Mimick browser scroll to element with id as hash once header get 'fixed' class
        // from _scrollWatcher method.
        setTimeout(function () {
            var hash = window.location.hash;
            if (hash != undefined && hash.indexOf('comment_') == 1 && typeof(window.location.href) == 'string') {
                window.location.href = window.location.href;
            }
        }, 1000);

    }
}

const Pachno = new PachnoApplication();
window.Pachno = Pachno;

$(document).ready(() => {
    const $body = $('#pachno-body');
    const debug = $body.data('debug-mode') == 1;
    const webroot = $body.data('webroot');
    const dataUrl = $body.data('data-url');
    const debugUrl = $body.data('debug-url');
    const autocompleterUrl = $body.data('autocompleter-url');

    Pachno.initialize({
        debug,
        webroot,
        dataUrl,
        debugUrl,
        autocompleterUrl
    });

    Pachno.trigger(Pachno.EVENTS.ready);
});

export default Pachno;
