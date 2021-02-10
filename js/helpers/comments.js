import Pachno from "../classes/pachno";

const toggleOrder = function (event) {
    const $element = $(this);
    const target_type = $element.data('target-type');
    const target_id = $element.data('target-id');
    Pachno.fetch(Pachno.data_url, {
        method: 'POST',
        loading: {
            indicator: '#comments_loading_indicator'
        },
        params: '&say=togglecommentsorder'
        })
        .then(() => {
            Pachno.fetch(Pachno.data_url, {
                method: 'GET',
                loading: {
                    indicator: '#comments_loading_indicator'
                },
                params: '&say=loadcomments&target_type='+target_type+'&target_id='+target_id,
                success: {
                    callback: function (json) {
                        $('#comments_box').html(json.comments);
                    }
                }
            });
        });
};

const removeComment = function (PachnoApplication, data) {
    const { url, comment_id, commentcount_span } = data;
    $('#dialog_indicator').show();
    Pachno.fetch(url, {
        method: 'DELETE'
    })
    .then(function (json) {
        $('#comment_' + comment_id).remove();
        Pachno.UI.Dialog.dismiss();
        $('#dialog_indicator').hide();
        if ($('#comments_box').children().length == 0) {
            $('#comments-list-none').show();
        }
        $(commentcount_span).html($('#comments_box').children().length);
    });
};

const addOrUpdateComment = function (PachnoApplication, data) {
    const json = data.json;
    const $form = $('#' + data.form);

    if ($form.data('comment-id')) {
        const comment_id = $form.data('comment-id'),
            $comment_container = $('#comment_' + comment_id + '_content');
            $comment_container.html(json.comment_data);

        $('#comment_edit_' + comment_id).removeClass('active');
        $('#comment_' + comment_id + '_body').show();
        $('#comment_view_' + comment_id).show();
    } else if ($form.data('comment-reply-id')) {
        const reply_comment_id = $form.data('comment-reply-id');
        const $comments_container = $('#comment_' + reply_comment_id + '_replies')

        $comments_container.append(json.comment_data);
        window.location.hash = "#comment_" + json.comment_id;
        $form[0].reset();

        $('#comment_reply_controls_' + reply_comment_id).show();
        $('#comment_reply_' + reply_comment_id).removeClass('active');
    } else {
        switch (data.form) {
            case 'add-comment-form':
                const $count_span = $('#' + $form.data('comment-count-element'));
                const $comments_container = $('#comments_box');

                $comments_container.append(json.comment_data);
                $('#comments-list-none').remove();
                window.location.hash = "#comment_" + json.comment_id;
                $count_span.html(json.commentcount);
                $form[0].reset();
                $('#comment_add').hide();
                break;
        }
    }
};

const showPost = function () {
    $('.comment-editor').hide();
    $('#comment_add').show();
    $('#comment_bodybox').focus();
}

const setupListeners = function() {
    const $body = $('body');

    $body.off('click', '.trigger-show-comment-post');
    $body.on('click', '.trigger-show-comment-post', showPost);

    $body.off('click', '.trigger-comment-sort');
    $body.on('click', '.trigger-comment-sort', toggleOrder);

    Pachno.on(Pachno.EVENTS.formSubmitResponse, addOrUpdateComment);
    Pachno.on(Pachno.EVENTS.comment.remove, removeComment);
}

export {
    setupListeners
}
