import $ from 'jquery';
import Pachno from "../classes/pachno";

const showLogin = function () {
    const $trigger = $(this);
    const section = $trigger.data('login-section');

    const $login_backdrop = $('#login_backdrop');
    $login_backdrop.find('.logindiv').removeClass('active');

    $(section).addClass('active');

    if (section !== '#register' && $('#registration-button-container')) {
        $('#registration-button-container').addClass('active');
    }
    $login_backdrop.show();
    setTimeout(function () {
        if (section === '#register') {
            $('#fieldusername').focus();
        } else if (section === '#regular_login_container') {
            $('#pachno_username').focus();
        }
    }, 250);
};

const setupListeners = function () {
    const $body = $('body');
    $body.on('click', Pachno.TRIGGERS.showLogin, showLogin)
}

export default setupListeners;
