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

const doLogin = function () {
//     var $form = $('#login_form'),
//         $login_button = $('#login_button'),
//         url = $form.attr('action');
//
//     $('#login-error-container').removeClass('invalid');
//     $login_button.addClass('submitting');
//     $login_button.prop('disabled', true);
//
//     fetch(url, {
//         method: 'POST',
//         body: new FormData($form[0])
//     })
//         .then(function (response) {
//             response.json().then(function (json) {
//                 $login_button.removeClass('submitting');
//                 $login_button.prop('disabled', false);
//
//                 if (response.ok) {
//                     if (json.forward) {
//                         window.location = json.forward;
//                     } else {
//                         window.location.reload();
//                     }
//                 } else {
//                     console.error(json);
//                     $('#login-error-message').html(json.error);
//                     $('#login-error-container').addClass('invalid');
//                 }
//             });
//         })
//         .catch(function (error) {
//             $('#login-error-message').html(error);
//             $('#login-error-container').addClass('invalid');
//             console.error(error);
//         });
//
};

const inviteUser = function (event) {
    event.preventDefault();
    event.stopPropagation();

    const $button = $(this);
    const $row = $button.parents('.row');
    const $table = $row.parents('.flexible-table');
    const email = $row.data('email');

    $row.addClass('submitting');
    $button.attr('disabled', true);

    Pachno.fetch(Pachno.data_url, {
        method: 'POST',
        data: {
            say: 'invite-user',
            email
        }
    }).then((json) => {
        $button.html(Pachno.UI.fa_image_tag('check', { classes: 'icon' }));
    }).catch(() => {
        $button.removeAttr('disabled');
        $row.removeClass('submitting')
    });
};

const setupListeners = function () {
    const $body = $('body');
    $body.on('click', Pachno.TRIGGERS.showLogin, showLogin)
    $body.on('click', ".trigger-invite-user", inviteUser);
}

export default setupListeners;
