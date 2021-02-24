import $ from "jquery";
import {debounce} from "../tools/tools";
import Pachno from "../classes/pachno";
import UI from "./ui";

/**
 * @type {Mentionsearch}
 */
let searchInstance = null;

/**
 * @property {HTMLElement} mentionNode
 */
class Mentionsearch {

    /**
     * @returns Mentionsearch
     */
    static get instance() {
        return searchInstance;
    }

    /**
     * @param {Mentionsearch} instance
     */
    static set instance(instance) {
        searchInstance = instance;
    }

    constructor(mentionNode, type, target_type, target_id, api) {
        this.mentionNode = mentionNode;
        this.type = type;
        this.target_type = target_type;
        this.target_id = target_id;
        this.list_items = [];
        this.selected = 0;
        this.value = '';
        this.api = api;

        this.$popup = this.createSearchPopup();

        this.loadMentionables();
        this.setupListeners();
    }

    loadMentionables() {
        this.selected = 0;
        let url = `?say=get_mentionables&target_type=${this.target_type}&target_id=${this.target_id}&type=${this.type}`;
        if (this.value) {
            url = `${url}&value=${this.value}`;
        }

        this.$popup.find('.list-mode').html(`<div class="list-item"><span class="icon">${UI.fa_image_tag('spinner', { classes: 'fa-spin' })}</span>`);
        Pachno.fetch(Pachno.data_url + url, {
            method: 'GET'
        }).then((json) => {
            searchInstance.list_items = json.mentionables;
            searchInstance.updateList();
        })
    }

    updateList() {
        let html = '';
        for (const index in this.list_items) {
            if (!this.list_items.hasOwnProperty(index))
                continue;

            const item = this.list_items[index];
            const selected_class = (this.selected == index) ? 'selected' : '';
            const icon = (item.image) ? `<span class="icon"><img src="${item.image}" class="avatar small"></span>` : UI.fa_image_tag(item.icon.name, { classes: 'icon' }, item.icon.style);

            html += `<div class="list-item ${selected_class}" data-index="${index}">${icon}<span class="name">${item.name}</span></div>`;
        }

        if (html !== '') {
            this.$popup.find('.list-mode').html(html);
        }
    }

    destroy() {
        this.removeListeners();
        this.$popup.remove();
        searchInstance = undefined;
    }

    createSearchPopup() {
        const rect = this.mentionNode.getBoundingClientRect();
        const mentions_class_name = (this.type === 'user') ? 'user-search' : 'article-search';
        const element = Pachno.htmlToElement(`
    <div class="dropper-container mentions-container ${mentions_class_name}">
        <div class="dropdown-container">
            <div class="list-mode">
                <div class="list-item header">
                    ${Pachno.T.common.mentions.user_search_placeholder}
                </div>
            </div>
        </div>
    </div>
`);
        element.style.left = `${rect.x}px`;
        element.style.top = `calc(${rect.y + rect.height}px + .5em)`;
        document.body.appendChild(element);

        return $(element);
    }

    selectPrevious() {
        if (this.selected === 0) {
            this.selected = this.list_items.length - 1;
        } else {
            this.selected -= 1;
        }
        this.updateList();
    }

    selectNext() {
        if (this.selected === this.list_items.length - 1) {
            this.selected = 0;
        } else {
            this.selected += 1;
        }
        this.updateList();
    }

    select() {
        const selected_item = this.list_items[this.selected];
        this.mentionNode.dataset[`${this.type}Id`] = selected_item.id;
        this.mentionNode.classList.add('completed');
        this.mentionNode.innerText = selected_item.name;

        const selection = window.getSelection();
        selection.setPosition(this.mentionNode, 0);
        for (let counter = 1; counter <= this.mentionNode.innerText.length + 1; counter += 1) {
            selection.modify('move', 'forward', 'character');
        }
        this.destroy();
    }

    /**
     * @param {Event} event
     */
    static keyListener(event) {
        switch (event.key) {
            case 'ArrowUp':
                searchInstance.selectPrevious();
                event.preventDefault();
                event.stopPropagation();
                break;
            case 'ArrowDown':
                searchInstance.selectNext();
                event.preventDefault();
                event.stopPropagation();
                break;
            case 'Enter':
                searchInstance.select();
                event.preventDefault();
                event.stopPropagation();
                break;
        }
    }

    /**
     * @param {Event} event
     */
    static keyEnterListener(event) {
        debugger;
        switch (event.key) {
            case 'Enter':
                event.preventDefault();
                event.stopPropagation();
                searchInstance.select();
                break;
        }
    }

    /**
     * @param {Event} event
     */
    static keyUpListener() {
        if (searchInstance === undefined) {
            return;
        }

        if (searchInstance.mentionNode.innerText != searchInstance.value) {
            searchInstance.value = searchInstance.mentionNode.textContent;
            debounce(searchInstance.loadMentionables(), 250);
        }
    }

    setupListeners() {
        if (this.api === undefined) {
            $('body').off('keypress', '.inline-mention', Mentionsearch.keyListener);
            $('body').on('keypress', '.inline-mention', Mentionsearch.keyListener);
            $('body').off('keyup', '.inline-mention', Mentionsearch.keyUpListener);
            $('body').on('keyup', '.inline-mention', Mentionsearch.keyUpListener);
            $('body').off('keypress', '.inline-mention', Mentionsearch.keyUpListener);
            $('body').on('keypress', '.inline-mention', Mentionsearch.keyUpListener);
        } else {
            this.api.listeners.off($('.wysiwyg-editor')[0], 'keydown', Mentionsearch.keyListener);
            this.api.listeners.on($('.wysiwyg-editor')[0], 'keydown', Mentionsearch.keyListener);
            this.api.listeners.off($('.wysiwyg-editor')[0], 'keypress', Mentionsearch.keyListener);
            this.api.listeners.on($('.wysiwyg-editor')[0], 'keypress', Mentionsearch.keyListener);
            this.api.listeners.off($('.wysiwyg-editor')[0], 'keyup', Mentionsearch.keyUpListener);
            this.api.listeners.on($('.wysiwyg-editor')[0], 'keyup', Mentionsearch.keyUpListener);
            $('body').on('click', '.dropper-container.mentions-container .list-item[data-index]', function (event) {
                if (!searchInstance)
                    return;

                searchInstance.selected = $(this).data('index');
                searchInstance.select();
                event.preventDefault();
                event.stopPropagation();
            });
            // this.api.listeners.on($(this.mentionNode)[0], 'keypress', mentionsearch.keyEnterListener.bind(mentionsearch), false);
            // this.api.listeners.on($(this.mentionNode)[0], 'keyup', mentionsearch.keyEnterListener.bind(mentionsearch), false);
            // this.api.listeners.on($(this.mentionNode)[0], 'keydown', mentionsearch.keyEnterListener.bind(mentionsearch), false);
        }
    }

    removeListeners() {
        const mentionsearch = this;
        if (this.api === undefined) {
            $('body').off('keypress', '.inline-mention');
            $('body').off('keyup', '.inline-mention');
            $('body').off('keypress', '.inline-mention');
            $('body').off('keydown', '.inline-mention');
        } else {
            this.api.listeners.off($('.wysiwyg-editor')[0], 'keydown', Mentionsearch.keyListener);
            this.api.listeners.off($('.wysiwyg-editor')[0], 'keypress', Mentionsearch.keyListener);
            this.api.listeners.off($('.wysiwyg-editor')[0], 'keyup', Mentionsearch.keyUpListener);
            // this.api.listeners.off($(this.mentionNode)[0], 'keypress');
            // this.api.listeners.off($(this.mentionNode)[0], 'keyup');
            // this.api.listeners.off($(this.mentionNode)[0], 'keydown');
        }
    }
}

export default Mentionsearch;
