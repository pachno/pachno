import $ from "jquery";
import OpenID from "../helpers/openid";
import Debugger from "./debugger";
import UI, { setupListeners as uiSetupListeners } from "../helpers/ui";
import {fetchHelper, formSubmitHelper, setupListeners as formSetupListeners} from "../helpers/fetch";
import widgetSetupListeners, { calendars } from "../widgets";
import profileSetupListeners from "../helpers/profile";
import {initializeDashboards} from "../helpers/dashboard";
import {setupListeners as issueSetupListeners} from "../helpers/issues";
import {setupListeners as commentSetupListeners} from "../helpers/comments";
import {setupListeners as favouriteSetupListeners} from "../helpers/favourites";
import {setupListeners as agileSetupListeners} from "../helpers/agile";
import Board from "./board";
import Search from "./search";
import Issuereporter from "./issuereporter";
import Issue from "./issue";
import Uploader from "./uploader";
import Roadmap from "./roadmap";
import Quicksearch, { TYPES as QuicksearchTypes } from "./quicksearch";
import strings_en_US from "../../i18n/en_US/strings.json";

const translations = {
    en_US: strings_en_US
};

class PachnoApplication {
    get EVENTS() {
        return {
            ready: 'pachno-ready',
            agile: {
                deleteBoard: 'agile-delete-board',
            },
            article: {
                removeFile: 'article-remove-file',
            },
            configuration: {
                deleteComponent: 'configuration-delete-component',
            },
            formSubmit: 'form-submit',
            formSubmitResponse: 'form-submit-response',
            formSubmitError: 'form-submit-error',
            issue: {
                removeFile: 'issue-remove-file',
                update: 'issue-update',
                triggerUpdate: 'issue-trigger-update',
                triggerEdit: 'issue-trigger-edit',
                updateDone: 'issue-update-done',
                updateJson: 'issue-update-json',
                loadDynamicChoices: 'issue-load-dynamic-choices',
            },
            upload: {
                complete: 'upload-complete',
            },
            quicksearchTrigger: 'quicksearch-trigger',
            quicksearchAddDefaultChoice: 'quicksearch-add-default-choice',
            quicksearchUpdateChoices: 'quicksearch-update-choices',
            comment: {
                remove: 'comment-remove'
            }
        }
    }

    get TRIGGERS() {
        return {
            showLogin: '.trigger-show-login'
        }
    }

    get T() {
        return translations[this.language] || translations.en_US;
    }

    constructor() {
        this.debug = false;
        this.basepath = '';
        this.data_url = '';
        this.quicksearch = undefined;
        this.debugger = undefined;
        this.listeners = {};
        this.language = document.body.dataset.language;
        this.issues = {};
    }

    initialize(options) {
        this.debug = options.debug;
        this.basepath = options.basepath;
        this.data_url = options.dataUrl;
        this.quicksearch = new Quicksearch(options.autocompleterUrl);

        this.trigger(this.EVENTS.quicksearchAddDefaultChoice, {
            icon: { name: 'search', type: 'fas'},
            shortcut: 'find',
            name: 'Find something',
            description: 'Search through issues, projects, documentation and people',
            action: {
                type: QuicksearchTypes.navigate,
                url: '/account'
            }
        });
        this.trigger(this.EVENTS.quicksearchAddDefaultChoice, {
            icon: { name: 'search', type: 'fas'},
            shortcut: 'show',
            name: 'Show an issue',
            description: 'Go directly to an issue',
            action: {
                type: QuicksearchTypes.event,
                event: '/find'
            }
        });

        if (this.debug) {
            this.debugger = new Debugger(options.debugUrl);
        }

        this._initialize();
    }

    get UI() {
        return {
            ...UI,
            calendars
        }
    }

    fetch(url, options) {
        return fetchHelper(url, options);
    }

    submit(url, form_id) {
        return formSubmitHelper(url, form_id);
    }

    on(key, callback) {
        if (this.listeners[key] === undefined) {
            this.listeners[key] = [];
        }

        this.listeners[key].push(callback);
    }

    trigger(key, data) {
        return new Promise((resolve, reject) => {
            if (this.listeners[key] === undefined) {
                return resolve();
            }

            try {
                let promises = [];
                for (let callback of this.listeners[key]) {
                    promises.push(callback(this, data));
                }
                Promise.all(promises)
                    .then(resolve)
                    .catch(reject);
            } catch (error) {
                reject(error);
            }
        });
    }

    setupListeners() {
        // $(window).on('resize', Core._resizeWatcher);
        // $(document).on('keydown', Core._escapeWatcher);
        agileSetupListeners();
        commentSetupListeners();
        favouriteSetupListeners();
        formSetupListeners();
        issueSetupListeners();
        profileSetupListeners();
        uiSetupListeners();
        widgetSetupListeners();
        // $('#fullpage_backdrop_content').on('click', Core._resizeWatcher);
    }

    _initialize() {
        this.setupListeners();
        // Core._initializeAutocompleter();
        // $(window).on('scroll', Pachno.Core._scrollWatcher);
        // Core._resizeWatcher();
        // Core._scrollWatcher();

        initializeDashboards()
            .then(() => {
                $('html').css({'cursor': 'default'});
            });

        OpenID.init();
    }

    loadComponentOptions(options, $item) {
        return new Promise(function (resolve, reject) {
            const $container = $(options.container),
                $options = $(options.options),
                url = $item.data('options-url');

            $options.html('<div><i class="fas fa-spin fa-spinner"></i></div>');
            $container.addClass('active');
            $container.find(options.component).removeClass('active');
            $item.addClass('active');

            fetchHelper(url)
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
    }

    /**
     *
     * @param json
     * @param board_id
     * @returns Issue
     */
    addIssue(json, board_id) {
        if (this.issues[json.id] !== undefined) {
            return this.issues[json.id];
        }

        this.issues[json.id] = new Issue(json, board_id);

        return this.issues[json.id];
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
