import {
  make,
  CSS,
  INLINE_BLOCK_TAG,
  restoreDefaultInlineTools,
} from '@groupher/editor-utils'

/**
 * Mention Tool for the Editor.js
 *
 * Allows to wrap inline fragment and style it somehow.
 */
export default class Mention {
  /**
   * Specifies Tool as Inline Toolbar Tool
   *
   * @return {boolean}
   */
  static get isInline() {
    return false
  }

  /**
   * @param {{api: object}}  - Editor.js API
   */
  constructor({ api }) {
    this.api = api
    /**
     * Tag represented the term
     *
     * @type {string}
     */

    this.CSS = {
      // mention
      mention: CSS.mention,
      hiddenToolbar: 'cdx-hidden-toolbar-block',
    }

    this.api = api;

    const $element = $('.wysiwyg-editor');
    const mention = this;
    api.listeners.on($element[0], 'keyup', (event) => {
      if (event.key === '@' || event.key === '[') {
        const selection = window.getSelection();

        if (this.isInsideMentionElement(selection)) {
          return;
        }

        const mention_type = (event.key === '@') ? 'user' : 'article';
        const mentionNode = mention.insertMentionElement(selection, mention_type);
        const $popup = mention.createSearchPopup(mentionNode, mention_type);
        const $body = $('body');
        $body.on('keypress', mention.verifyStillInMentionElement.bind(mention));
        $body.on('keyup', mention.verifyStillInMentionElement.bind(mention));
      }
    });
  }

  createSearchPopup(mentionNode, type) {
    const rect = mentionNode.getBoundingClientRect();
    const mentions_class_name = (type === 'user') ? 'user-search' : 'article-search';
    const element = this.htmlToElement(`<div class="dropper-container mentions-container ${mentions_class_name}"><div class="dropdown-container"><div class="list-mode"><div class="list-item">bob</div></div></div></div>`);
    element.style.left = `${rect.x}px`;
    element.style.top = `calc(${rect.y + rect.height}px + .5em)`;
    document.body.appendChild(element);

    return element;
  }

  verifyStillInMentionElement() {
    const selection = window.getSelection();
    if (!this.isInsideMentionElement(selection)) {
      $('body').off('keypress', this.verifyStillInMentionElement.bind(this));
      $('body').off('keyup', this.verifyStillInMentionElement.bind(this));
      $('.dropper-container.mentions-container').remove();
      const $incompleteElements = $('.inline-mention:not(.completed)');
      for (const incompleteElement of $incompleteElements) {
        const $incompleteElement = $(incompleteElement);
        const prefix = ($incompleteElement.hasClass('user-link')) ? '@' : '[';
        $incompleteElement.replaceWith(prefix + $incompleteElement.html());
      }
    }
  }

  insertMentionElement(selection, type) {
    selection.extend(selection.anchorNode, selection.anchorOffset - 1);
    const range = selection.getRangeAt(0);
    const mentionNode = document.createElement('span');

    if (type === 'user') {
      mentionNode.textContent = '';
      mentionNode.classList.add('inline-mention');
      mentionNode.classList.add('user-link');
    } else if (type === 'article') {
      mentionNode.textContent = '';
      mentionNode.classList.add('inline-mention');
      mentionNode.classList.add('article-link');
    }

    range.deleteContents();
    range.insertNode(document.createTextNode(' '));
    range.insertNode(mentionNode);
    range.insertNode(document.createTextNode(' '));

    selection.setPosition(mentionNode, 0);
    selection.extend(mentionNode, 0);

    return mentionNode;
  }

  isInsideMentionElement(selection) {
    if (selection.anchorNode) {
      let parentElement = (selection.anchorNode.classList !== undefined) ? selection.anchorNode : selection.anchorNode.parentElement;
      if (parentElement.tagName.toLowerCase() === 'span' && parentElement.classList.contains('inline-mention')) {
        return true;
      }
    }

    return false;
  }

  /**
   * @param {String} HTML representing a single element
   * @return {Element}
   */
  htmlToElement(html) {
    var template = document.createElement('template');
    html = html.trim(); // Never return a text node of whitespace as the result
    template.innerHTML = html;
    return template.content.firstChild;
  }

  /**
   * Create button element for Toolbar
   * @ should not visible in toolbar, so return an empty div
   * @return {HTMLElement}
   */
  render() {
    const emptyEl = make('div', [this.CSS.hiddenToolbar], {})

    return emptyEl
  }

  /**
   * editor.js render actions
   *
   * @returns {HTMLElement}
   * @memberof Mention
   */
  renderActions() {
    // return this.nodes.mention
    return this.ui.renderActions()
  }

  /**
   * NOTE:  inline tool must have this method
   *
   * @param {Range} range - selected fragment
   */
  surround(range) {}

  /**
   * Check and change Term's state for current selection
   */
  checkState(termTag) {
    // console.log('# checkState termTag anchorNode: ', termTag.anchorNode)
    // NOTE: if emoji is init after mention, then the restoreDefaultInlineTools should be called
    // otherwise restoreDefaultInlineTools should not be called, because the mention plugin
    // called first
    //
    // restoreDefaultInlineTools 是否调用和 mention / emoji 的初始化循序有关系，
    // 如果 mention 在 emoji 之前初始化了，那么 emoji 这里就不需要调用 restoreDefaultInlineTools,
    // 否则会导致 mention  无法正常显示。反之亦然。
    if (!termTag || termTag.anchorNode.id !== CSS.mention) {
      return restoreDefaultInlineTools()
    }

    if (termTag.anchorNode.id === CSS.mention) {
      return this.ui.handleMentionActions()
    }

    // normal inline tools
    console.log('restoreDefaultInlineTools 2')
    return restoreDefaultInlineTools()
  }

  /**
   * Sanitizer rule
   * @return {{span: {class: string}}}
   */
  static get sanitize() {
    return {
      'span': {
        class: 'inline-mention,user-link,article-link',
      },
    }
  }

  /**
   * see @link https://editorjs.io/inline-tools-api-1#clear
   * @memberof Mention
   */
  clear() {
    this.ui.clear()
  }
}
