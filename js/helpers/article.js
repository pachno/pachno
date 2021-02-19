import $ from "jquery";
import Pachno from "../classes/pachno";
import UI from "./ui";

const setupListeners = () => {
    const $body = $('body');
    const user_url_template = $body.data('user-backdrop-url');

    const $usermentions = $('.inline-mention.user-link');
    for (const element of $usermentions) {
        const $element = $(element);
        const link_text = $element.html();
        const url = user_url_template.replace('user_id', $element.data('user-id'));
        if ($element.hasClass('completed')) {
            const $link_element = $(`<a href="javascript:void(0)" class="inline-mention user-link trigger-backdrop" data-url="${url}">${UI.fa_image_tag('user', { classes: 'icon' }, 'fas')}<span>${link_text}</span></a>`);
            $element.replaceWith($link_element);
        } else {
            const prefix = ($element.hasClass('user-link')) ? '@' : '[';
            $element.replaceWith(prefix + $element.html());
        }

    }
};

export {
    setupListeners
}