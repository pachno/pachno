import {
  make,
  CSS,
  debounce,
  moveCaretToEnd,
  keepCustomInlineToolOnly,
  removeElementByClass,
  convertElementToText,
} from '@groupher/editor-utils'

import { TAB } from './constant'

/**
 * Mention Tool for the Editor.js
 *
 * Allows to wrap inline fragment and style it somehow.
 */
export default class UI {
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

    // current active tab
    this.activeTab = TAB.USER
    this.tabConfig = [
      {
        title: '用户',
        raw: TAB.USER,
      },
      {
        title: '帖子',
        raw: TAB.POST,
      },
    ]

    this.CSS = {
      //
      mention: CSS.mention,
      mentionContainer: 'cdx-mention__container',
      mentionInput: 'cdx-mention__input',
      mentionIntro: 'cdx-mention-suggestion__intro',
      mentionAvatar: 'cdx-mention-suggestion__avatar',
      mentionTitle: 'cdx-mention-suggestion__title',
      mentionDesc: 'cdx-mention-suggestion__desc',
      // tab
      tabWrapper: 'cdx-mention__tab',
      tabItem: 'cdx-mention__tab_item',
      tabItemActive: 'cdx-mention__tab_item_active',

      // suggestion
      suggestionContainer: 'cdx-mention-suggestion-wrapper',
      suggestion: 'cdx-mention-suggestion',
      // inline toolbar
      inlineToolBar: 'ce-inline-toolbar',
      inlineToolBarOpen: 'ce-inline-toolbar--showed',
      inlineToolbarButtons: 'ce-inline-toolbar__buttons',
    }

    this.nodes = {
      mention: make('div', this.CSS.mentionContainer),
      suggestions: make('div', this.CSS.suggestionContainer),
      tab: this._drawTab(),
      mentionInput: make('input', this.CSS.mentionInput, {
        autofocus: true,
        placeholder: '你想 @ 谁？',
      }),
    }

    this._initMentionInput()

    this.nodes.mention.appendChild(this.nodes.tab)
    this.nodes.mention.appendChild(this.nodes.mentionInput)
    this.nodes.mention.appendChild(this.nodes.suggestions)

    this.nodes.mentionInput.addEventListener(
      'keyup',
      debounce(this._handleInput.bind(this), 200),
    )
  }

  /**
   * handle tab change
   * @param {string} tab - tab raw
   * @memberof UI
   */
  _handleTabChange(tab) {
    if (this.activeTab === tab) return

    this.activeTab = tab
    const TabEl = this._drawTab()

    this.nodes.tab.replaceWith(TabEl)
    this.nodes.tab = TabEl

    setTimeout(() => this._applyInputStyle())
  }

  /**
   * different input style for each tab
   *
   * @memberof UI
   */
  _applyInputStyle() {
    switch (this.activeTab) {
      case TAB.POST: {
        this.nodes.mentionInput.style.width = '280px'
        this.nodes.mentionInput.placeholder = '文章标题'
        break
      }

      default: {
        this.nodes.mentionInput.style.width = '180px'
        this.nodes.mentionInput.placeholder = '你想 @ 谁?'
        break
      }
    }
    this._initMentionInput()
    this.nodes.mentionInput.focus()
  }

  _initMentionInput() {
    this.api.listeners.off(this.nodes.mentionInput, 'focus')

    this.api.listeners.on(this.nodes.mentionInput, 'focus', () => {
      const mentionEl = document.querySelector('#' + this.CSS.mention)

      if (mentionEl) {
        const mentionCursorHolder = make('span', CSS.focusHolder)
        mentionEl.parentNode.insertBefore(
          mentionCursorHolder,
          mentionEl.nextSibling,
        )
      }
    })
    // this.nodes.mentionInput.addEventListener('focus', )
  }

  /**
   * draw tab
   * @return {HTMLElement}
   * @memberof UI
   */
  _drawTab() {
    const TabEl = make('div', this.CSS.tabWrapper)
    this.tabConfig.forEach((tabItem) => {
      const classList = [this.CSS.tabItem]
      if (tabItem.raw === this.activeTab) classList.push(this.CSS.tabItemActive)

      const TabItemEl = make('div', classList, {
        innerHTML: tabItem.title,
      })

      TabItemEl.addEventListener('click', () => {
        this._handleTabChange(tabItem.raw)
      })

      TabEl.appendChild(TabItemEl)
    })

    return TabEl
  }

  /**
   * editor.js render actions
   *
   * @returns {HTMLElement}
   * @memberof Mention
   */
  renderActions() {
    return this.nodes.mention
  }

  /**
   * show mention suggestions, hide normal actions like bold, italic etc...inline-toolbar buttons
   * 隐藏正常的 粗体，斜体等等 inline-toolbar 按钮，这里是借用了自带 popover 的一个 hack
   */
  handleMentionActions() {
    keepCustomInlineToolOnly('mention')

    this.clearSuggestions()
    this.nodes.mentionInput.value = ''

    setTimeout(() => this.nodes.mentionInput.focus(), 100)
  }

  // clear suggestions list
  clearSuggestions() {
    console.log('clearSuggestions')
    const node = document.querySelector('.' + this.CSS.suggestionContainer)

    while (node.firstChild) {
      node.removeChild(node.firstChild)
    }
  }

  /**
   * handle mention input
   *
   * @return {void}
   */
  _handleInput(ev) {
    if (ev.code === 'Escape') {
      // clear the mention input and close the toolbar
      this.nodes.mentionInput.value = ''
      this._cleanUp()
      return
    }

    if (ev.code === 'Enter') {
      return console.log('select first item')
    }

    const user = {
      id: 1,
      title: 'mydaerxym',
      desc: '摩托旅行爱好者',
      avatar: 'https://cps-oss.oss-cn-shanghai.aliyuncs.com/test.jpg',
    }

    const suggestion = this._drawSuggestion(user)

    this.nodes.suggestions.appendChild(suggestion)
  }

  /**
   * generate suggestion block
   *
   * @return {HTMLElement}
   */
  _drawSuggestion(user) {
    const mentionEl = document.querySelector('#' + this.CSS.mention)
    const suggestionWrapper = make('div', [this.CSS.suggestion], {})

    const avatar = make('img', [this.CSS.mentionAvatar], {
      src: user.avatar,
    })

    const intro = make('div', [this.CSS.mentionIntro], {})
    const title = make('div', [this.CSS.mentionTitle], {
      innerText: user.title,
    })
    const desc = make('div', [this.CSS.mentionDesc], {
      innerText: user.desc,
    })

    suggestionWrapper.appendChild(avatar)
    intro.appendChild(title)
    intro.appendChild(desc)
    suggestionWrapper.appendChild(intro)

    suggestionWrapper.addEventListener('click', () => {
      this.nodes.mentionInput.value = user.title
      mentionEl.innerHTML = `${user.title} `

      console.log('<<<< mentionEl: ', mentionEl)
      mentionEl.setAttribute('data-sign', '#')

      this._cleanUp()
    })

    // https://avatars0.githubusercontent.com/u/6184465?s=40&v=4

    return suggestionWrapper
  }

  /**
   * close the mention popover, then focus to mention holder
   *
   * @return {void}
   */
  _cleanUp() {
    const mentionEl = document.querySelector('#' + this.CSS.mention)
    if (!mentionEl) return

    // clear input
    this.nodes.mentionInput.value = ''

    // empty the mention input
    this._clearSuggestions()

    // closePopover
    const inlineToolBar = document.querySelector('.' + this.CSS.inlineToolBar)
    // this.api.toolbar.close is not work
    // so close the toolbar by remove the open class manually
    // this.api.toolbar.close()
    inlineToolBar.classList.remove(this.CSS.inlineToolBarOpen)

    // move caret to end of the current mention
    if (mentionEl.nextElementSibling) {
      moveCaretToEnd(mentionEl.nextElementSibling)
    }

    // mention holder id should be uniq
    // 在 moveCaret 定位以后才可以删除，否则定位会失败
    setTimeout(() => {
      this._removeAllHolderIds()
      removeElementByClass(CSS.focusHolder)
      if (mentionEl.innerHTML === '&nbsp;') {
        convertElementToText(mentionEl, true)
      }
    })
  }

  // clear suggestions list
  _clearSuggestions() {
    console.log('clearSuggestions')
    const node = document.querySelector('.' + this.CSS.suggestionContainer)

    while (node.firstChild) {
      node.removeChild(node.firstChild)
    }
  }

  // 删除所有 mention-holder 的 id， 因为 closePopover 无法处理失焦后
  // 自动隐藏的情况
  _removeAllHolderIds() {
    const holders = document.querySelectorAll('.' + this.CSS.mention)

    holders.forEach((item) => item.removeAttribute('id'))

    return false
  }

  /**
   * see @link https://editorjs.io/inline-tools-api-1#clear
   * @memberof Mention
   */
  clear() {
    /**
     * should clear anchors after user manually click outside the popover,
     * otherwise will confuse the next insert
     *
     * 用户手动点击其他位置造成失焦以后，如果没有输入的话需要清理 anchors，
     * 否则会造成下次插入 mention 的时候定位异常
     *
     */
    setTimeout(() => {
      const mentionEl = document.querySelector('#' + this.CSS.mention)
      this._cleanUp()
    })
  }
}
