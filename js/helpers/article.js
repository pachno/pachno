import $ from "jquery";
import Pachno from "../classes/pachno";
import UI from "./ui";
import {getEditor} from "../widgets/editor";

const setupListeners = () => {
    const $body = $('body');
    const user_url_template = $body.data('user-backdrop-url');

    const $usermentions = $('.inline-mention.user-link');
    let user_ids = [];
    for (const element of $usermentions) {
        const $element = $(element);
        const link_text = $element.html();
        const user_id = $element.data('user-id');
        const url = user_url_template.replace('_user_id', user_id);
        if ($element.hasClass('completed')) {
            const $link_element = $(`<a href="javascript:void(0)" class="inline-mention user-link trigger-backdrop" data-user-id="${user_id}" data-url="${url}">${UI.fa_image_tag('user', { classes: 'icon' }, 'fas')}<span class="name">${link_text}</span>${UI.fa_image_tag('circle-notch', { classes: 'icon fa-spin indicator' }, 'fas')}</a>`);
            $element.replaceWith($link_element);
            user_ids.push(parseInt(user_id));
        } else {
            $element.replaceWith(`@${$element.html()}`);
        }
    }

    if (user_ids.length) {
        Pachno.fetch(`${Pachno.data_url}?say=get_usernames&user_ids=${user_ids.join(',')}`, { method: 'GET' })
            .then((json) => {
                for (const user of json.users) {
                    $(`.inline-mention.user-link[data-user-id="${user.id}"] .name`).html(user.name);
                    $(`.inline-mention.user-link[data-user-id="${user.id}"] .indicator`).remove();
                }
            });
    }

    const $articlementions = $('.inline-mention.article-link');
    let article_ids = [];
    for (const element of $articlementions) {
        const $element = $(element);
        const link_text = $element.html();
        if ($element.hasClass('completed')) {
            const article_id = $element.data('article-id');
            const $link_element = $(`<a href="javascript:void(0)" class="inline-mention article-link loading" data-article-id="${article_id}">${UI.fa_image_tag('file-alt', { classes: 'icon' }, 'far')}<span class="name">${link_text}</span>${UI.fa_image_tag('circle-notch', { classes: 'icon fa-spin indicator' }, 'fas')}</a>`);
            $element.replaceWith($link_element);
            article_ids.push(article_id);
        } else {
            $element.replaceWith(`[${$element.html()}]`);
        }
    }

    if (article_ids.length) {
        Pachno.fetch(`${Pachno.data_url}?say=get_articlenames&article_ids=${article_ids.join(',')}`, { method: 'GET' })
            .then((json) => {
                for (const article of json.articles) {
                    $(`.inline-mention.article-link[data-article-id="${article.id}"] .name`).html(article.name);
                    $(`.inline-mention.article-link[data-article-id="${article.id}"] .indicator`).remove();
                    $(`.inline-mention.article-link[data-article-id="${article.id}"]`).attr('href', article.url);
                    $(`.inline-mention.article-link[data-article-id="${article.id}"]`).removeClass('loading');
                }

                $(`.inline-mention.article-link.loading .indicator`).remove();
                $(`.inline-mention.article-link.loading`).addClass('invalid').removeClass('loading');
            });
    }

    const $issuementions = $('.inline-mention.issue-link');
    let issue_ids = [];
    for (const element of $issuementions) {
        const $element = $(element);
        const link_text = $element.html();
        if (true || $element.hasClass('completed')) {
            const issue_id = $element.data('issue-id');
            const $link_element = $(`<a href="javascript:void(0)" class="inline-mention issue-link completed" data-issue-id="${issue_id}"><span class="name">${link_text}</span>${UI.fa_image_tag('circle-notch', { classes: 'icon fa-spin indicator' }, 'fas')}</a>`);
            $element.replaceWith($link_element);
            issue_ids.push(issue_id);
        } else {
            $element.replaceWith(`[${$element.html()}]`);
        }
    }

    if (issue_ids.length) {
        Pachno.fetch(`${Pachno.data_url}?say=get_issues&issue_ids=${issue_ids.join(',')}`, { method: 'GET' })
            .then((json) => {
                for (const issue of json.issues) {

                    $(`.inline-mention.issue-link[data-issue-id="${issue.id}"]`).html(`${UI.fa_image_tag(issue.issue_type.fa_icon, {classes: `icon issuetype-icon issuetype-${issue.issue_type.type}`})}<span class="name">${issue.title}</span>`);
                    $(`.inline-mention.issue-link[data-issue-id="${issue.id}"]`).attr('href', issue.url);
                }
            });
    }

    Pachno.on(Pachno.EVENTS.article.delete, function (PachnoApplication, data) {
        Pachno.UI.Dialog.setSubmitting();
        $('[data-article][data-id=' + data.article_id + ']').remove();
        Pachno.fetch(data.url, { method: 'DELETE' })
            .then(json => {
                if (json.forward === undefined) {
                    Pachno.UI.Dialog.dismiss();
                }
            });
    });

    $('body').on('change', '.article.editable .trigger-toggle-checklist', function (event) {
        event.preventDefault();

        const $input = $(this);
        const checked = ($input.is(':checked')) ? 1 : 0;
        const $article = $input.parents('.article');
        const url = $article.data('url');

        Pachno.fetch(url, {
            method: 'POST',
            data: {
                update: true,
                article_action: 'update-checklist-item',
                block_index: $input.parent('.checklist').data('index'),
                list_index: $input.data('index'),
                checked
            }
        });

        return false;
    });

    $('body').on('click', '.trigger-embed', function (event) {
        event.preventDefault();
        event.stopPropagation();

        const editor = getEditor('article-editor');
        const url = $(this).data('url');
        const image_data = {
            file: { url },
            'caption': '',
            'withBorder': false,
            'withBackground': false,
            'stretched': false
        };

        editor.blocks.insert('image', image_data, {}, editor.blocks.getCurrentBlockIndex() + 1);
    });
};

export {
    setupListeners
}