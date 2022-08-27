import $ from "jquery";
import Pachno from "../classes/pachno";

const loadComments = function(target_type, target_id) {
    Pachno.fetch(Pachno.data_url + '&say=loadcomments&target_type='+target_type+'&target_id='+target_id, {
        method: 'GET',
        loading: {
            indicator: '#comments_loading_indicator'
        }}).then((json) => {
            $('#comments_box[data-target-type=' + target_type + '][data-target-id=' + target_id + ']').html(json.comments);
        });
};

const toggleOrder = function (event) {
    const $element = $(this);
    const target_type = $element.data('target-type');
    const target_id = $element.data('target-id');
    Pachno.fetch(Pachno.data_url, {
            method: 'POST',
            data: { say: 'togglecommentsorder' }
        })
        .then(() => loadComments(target_type, target_id));
};

const removeComment = function (PachnoApplication, data) {
    const { url, id, count_element } = data;
    Pachno.UI.Dialog.setSubmitting();
    Pachno.fetch(url, {
        method: 'DELETE'
    })
    .then(function (json) {
        $('#comment_' + id).remove();
        Pachno.UI.Dialog.dismiss();
        $('#dialog_indicator').hide();
        if ($('#comments_box').children().length == 0) {
            $('#comments-list-none').show();
        }
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

    $body.off('click', '#comment_add .closer');
    $body.on('click', '#comment_add .closer', () => {
        $('#comment_add').hide();
    });

    Pachno.on(Pachno.EVENTS.formSubmitResponse, addOrUpdateComment);
    Pachno.on(Pachno.EVENTS.comment.remove, removeComment);
    Pachno.on(Pachno.EVENTS.issue.updateJson, function (PachnoApplication, data) {
        const issue_json = (data.json.issue !== undefined) ? data.json.issue : data.json;

        debugger;
        if ($('#comments_box[data-target-type=' + TARGET_TYPES.issue + '][data-target-id=' + issue_json.id + ']').length == 0) {
            return;
        }

        let missing = false;
        for (const comment_id in issue_json.comments) {
            if (!issue_json.comments.hasOwnProperty(comment_id)) {
                continue;
            }

            const comment = issue_json.comments[comment_id];
            if ($('#comment_view_' + comment.id).length == 0) {
                missing = true;
                break;
            }
        }

        if (missing) {
            $('#comments-list-none').remove();
            loadComments(TARGET_TYPES.issue, issue_json.id);
        }
    });
}

export const TARGET_TYPES = {
    issue: 1,
    article: 2
}

export {
    setupListeners
}
