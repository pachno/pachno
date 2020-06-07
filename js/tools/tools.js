import $ from "jquery";

export const is_string = function (element) {
    return (typeof element == 'string');
}

export const get_current_timestamp = function () {
    return Math.round(Date.now() / 1000);
}

export const clearFormSubmit = function ($form) {
    if ($form !== undefined) {
        $form.removeClass('submitting');
        $form.find('button[type=submit].auto-disabled').each(function () {
            let $button = $(this);
            $button.prop("disabled", false);
            $button.removeClass('auto-disabled');
        })
    }
}

export const debounce = function (func, wait, immediate) {
    let timeout;
    return function() {
        let context = this, args = arguments;
        let later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        let callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
};