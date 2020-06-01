const _is = function(obj, type) {
    return toString.call(obj).slice(8, -1) === type;
};

const _forEach = function(obj, iterator, arrayLike) {
    if (!obj) return;
    if (arrayLike == null) arrayLike = _is(obj, 'Array');
    if (arrayLike) {
        for (let i = 0, l = obj.length; i < l; i++) iterator(obj[i], i, obj);
    } else {
        for (let key in obj) {
            if (obj.hasOwnProperty(key)) iterator(obj[key], key, obj);
        }
    }
};

// copy props from a obj
const _copy = function(defaults, source) {
    _forEach(source, function (value, key) {
        defaults[key] = _is(value, 'Object') ? _copy({}, value) :
            _is(value, 'Array') ? _copy([], value) : value;
    });
    return defaults;
};

const _delayExec = function (fn) {
    let timer = null;
    return function (delay) {
        clearTimeout(timer);
        timer = setTimeout(function() {
            fn();
        }, delay || 1);
    };
};

// merge: make it easy to have a fallback
const _merge = function(config) {

    // default settings
    let defaults = {
        class: 'pen',
        debug: false,
        toolbar: null, // custom toolbar
        stay: config.stay || !config.debug,
        stayMsg: 'Are you going to leave here?',
        textarea: '<textarea name="content"></textarea>',
        list: [
            'blockquote', 'h2', 'h3', 'p', 'code', 'insertorderedlist', 'insertunorderedlist', 'inserthorizontalrule',
            'indent', 'outdent', 'bold', 'italic', 'underline', 'createlink', 'insertimage'
        ],
        titles: {},
        cleanAttrs: ['id', 'class', 'style', 'name'],
        cleanTags: ['script'],
        linksInNewWindow: false
    };

    // user-friendly config
    if (config.nodeType === 1) {
        defaults.editor = config;
    } else if (config.match && config.match(/^#[\S]+$/)) {
        defaults.editor = document.getElementById(config.slice(1));
    } else {
        defaults = _copy(defaults, config);
    }

    return defaults;
};

const utils = {
    is: _is,
    copy: _copy,
    merge: _merge,
    delayExec: _delayExec,
    forEach: _forEach
};

export default utils;