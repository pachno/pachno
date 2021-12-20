/**
 * Mention Tool for the Editor.js
 *
 * Allows to wrap inline fragment and style it somehow.
 */
import Mentionsearch from "../../helpers/mentionsearch";

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
    const $element = $('.wysiwyg-editor');

    this.api = api;
    this.article_id = $element.data('article-id');

    api.listeners.on($element[0], 'keyup', this.listenTriggerPopup.bind(this));
  }

  listenTriggerPopup(event) {
    if (event.key === '@' || event.key === '[') {
      const selection = window.getSelection();

      if (this.isInsideMentionElement(selection)) {
        return;
      }

      const mention_type = (event.key === '@') ? 'user' : 'article';
      const mentionNode = this.insertMentionElement(selection, mention_type);
      Mentionsearch.instance = new Mentionsearch(mentionNode, mention_type, 'article', this.article_id, this.api);
      const $body = $('body');
      $body.on('keypress', this.verifyStillInMentionElement.bind(this));
      $body.on('keyup', this.verifyStillInMentionElement.bind(this));
    }
  }

  verifyStillInMentionElement() {
    const selection = window.getSelection();
    if (!this.isInsideMentionElement(selection)) {
      $('body').off('keypress', this.verifyStillInMentionElement.bind(this));
      $('body').off('keyup', this.verifyStillInMentionElement.bind(this));
      if (Mentionsearch.instance) {
        Mentionsearch.instance.destroy();
      }

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
   * Create button element for Toolbar
   * @ should not visible in toolbar, so return an empty div
   * @return {HTMLElement}
   */
  render() {
    const emptyEl = document.createElement('div');

    return emptyEl
  }

  /**
   * editor.js render actions
   *
   * @returns {HTMLElement}
   * @memberof Mention
   */
  renderActions() {
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
  checkState() {
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
  }
}
