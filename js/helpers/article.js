import $ from "jquery";
import Pachno from "../classes/pachno";
import UI from "./ui";

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
                    $(`.inline-mention.article-link[data-article-id="${article.id}"] .indicator`).remove();
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
            const $link_element = $(`<a href="javascript:void(0)" class="inline-mention article-link" data-article-id="${article_id}">${UI.fa_image_tag('file-alt', { classes: 'icon' }, 'far')}<span class="name">${link_text}</span>${UI.fa_image_tag('circle-notch', { classes: 'icon fa-spin indicator' }, 'fas')}</a>`);
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
                }
            });
    }
};

export {
    setupListeners
}