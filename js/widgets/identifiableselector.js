import Pachno from "../classes/pachno";

const findIdentifiable = function (url, field) {
    Pachno.fetch(url, {
        form: field + '_form',
        loading: {indicator: '#' + field + '_spinning'},
        success: {
            update: '#' + field + '_results',
            show: '#' + field + '_results_container'
        }
    });
};

const setupListeners = function () {
    $('body').on('submit', 'form[data-identifiable-selector-form]', function (event) {
        event.preventDefault();

        const $form = $(this);
        const form_base_id = $form.data('base-id');
        const url = $form.action;

        findIdentifiable(url, form_base_id);
    });
};

export default setupListeners;