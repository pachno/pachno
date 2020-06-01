import $ from "jquery";

const Tutorial = {
    Stories: {},

    highlightArea: (top, left, width, height, blocked, seethrough) => {
        let backdrop_class = (seethrough != undefined && seethrough == true) ? 'seethrough' : '';
        let d1 = '<div class="fullpage_backdrop ' + backdrop_class + ' tutorial" style="top: 0; left: 0; width: ' + left + 'px;"></div>';
        let d2_width = Pachno.Core._vp_width - left - width;
        let d2 = '<div class="fullpage_backdrop ' + backdrop_class + ' tutorial" style="top: 0; left: ' + (left + width) + 'px; width: ' + d2_width + 'px;"></div>';
        let d3 = '<div class="fullpage_backdrop ' + backdrop_class + ' tutorial" style="top: 0; left: ' + left + 'px; width: ' + width + 'px; height: ' + top + 'px"></div>';
        let vp_height = document.viewport.getHeight();
        let d4_height = vp_height - top - height;
        let d4 = '<div class="fullpage_backdrop ' + backdrop_class + ' tutorial" style="top: ' + (top + height) + 'px; left: ' + left + 'px; width: ' + width + 'px; height: ' + d4_height + 'px"></div>';
        let mc = $('#main_container');
        if (blocked == true) {
            let d_overlay = '<div class="tutorial block_overlay" style="top: ' + top + 'px; left: ' + left + 'px; width: ' + width + 'px; height: ' + height + 'px;"></div>';
            mc.append(d_overlay);
        }

        [d1, d2, d3, d4].forEach(mc.append)
        this.positionMessage(top, left, width, height);
    },

    highlightElement: (element, blocked, seethrough) => {
        element = $(element);
        let el = element.getLayout();
        let os = element.cumulativeOffset();
        let width = el.get('width') + el.get('padding-left') + el.get('padding-right');
        let height = el.get('height') + el.get('padding-top') + el.get('padding-bottom');
        this.highlightArea(os.top, os.left, width, height, blocked, seethrough);
    },

    positionMessage: (top, left, width, height) => {
        let tm = $('#tutorial-message');
        ['above', 'below', 'left', 'right'].each(function (pos) {
            tm.removeClass(pos);
        });
        if (top + left + width + height == 0) {
            tm.addClass('full');
            tm.css({top: '', left: ''});
        } else {
            tm.removeClass('full');
            let step = parseInt(tm.dataset.tutorialStep);
            let key = tm.dataset.tutorialKey;
            let td = this.Stories[key][step];
            tm.addClass(td.messagePosition);
            let tl = tm.getLayout();
            let th;
            let twidth = tl.get('width') + tl.get('padding-left') + tl.get('padding-right');
            switch (td.messagePosition) {
                case 'right':
                    th = tl.get('height') + tl.get('padding-top') + tl.get('padding-bottom');
                    tm.css({top: (top - parseInt(th / 2)) + 'px', left: (left + width + 15) + 'px'});
                    break;
                case 'left':
                    let width = tl.get('width') + tl.get('padding-left') + tl.get('padding-right');
                    th = tl.get('height') + tl.get('padding-top') + tl.get('padding-bottom');
                    tm.css({top: (top - parseInt(th / 2)) + 'px', left: (left - width - 15) + 'px'});
                    break;
                case 'below':
                    tm.css({top: (top + height + 15) + 'px', left: ((left - parseInt(twidth / 2)) + width / 2) + 'px'});
                    break;
                case 'above':
                    th = tl.get('height') + tl.get('padding-top') + tl.get('padding-bottom');
                    tm.css({top: (top - th - 15) + 'px', left: ((left - parseInt(twidth / 2)) + width / 2) + 'px'});
                    break;
                case 'center':
                    th = tl.get('height') + tl.get('padding-top') + tl.get('padding-bottom');
                    tm.css({top: (top + (height / 2) - (th / 2)) + 'px', left: ((left - parseInt(twidth / 2)) + width / 2) + 'px'});
                    break;
            }
        }
        tm.show();
    },

    resetHighlight: () => {
        $('.tutorial').each(Element.remove);
    },

    disable: () => {
        let tm = $('#tutorial-message');
        let key = tm.dataset.tutorialKey;
        let url = tm.dataset.disableUrl;
        Pachno.Helpers.fetch(url, {
            params: '&key=' + key
        });
        $('#tutorial-next-button').stopObserving('click');
        this.resetHighlight();
        $('#tutorial-message').hide();
    },

    playNextStep: () => {
        this.resetHighlight();
        let tm = $('#tutorial-message');
        tm.hide();
        let step = parseInt(tm.dataset.tutorialStep);
        let key = tm.dataset.tutorialKey;
        step++;
        $('#tutorial-current-step').html(step);
        tm.dataset.tutorialStep = step;
        let tutorialData = this.Stories[key][step];
        if (tutorialData != undefined) {
            if (tutorialData.cb) {
                tutorialData.cb(tutorialData);
            }
            $('#tutorial-message-container').html(tutorialData.message);
            let tbn = tm.down('.tutorial-buttons').down('.button-next');
            let tb = tm.down('.tutorial-buttons').down('.button-disable');
            if (tutorialData.button != undefined) {
                tbn.html(tutorialData.button);
                tbn.show();
                if (step > 1) {
                    tb.hide();
                } else {
                    tb.show();
                }
            } else {
                tbn.hide();
                tb.hide();
            }
            ['small', 'medium', 'large'].each(function (cn) {
                tm.removeClass(cn);
            });
            tm.addClass(tutorialData.messageSize);
            if (tutorialData.highlight != undefined) {
                let tdh = tutorialData.highlight;
                let timeout = (tdh.delay) ? tdh.delay : 50;
                window.setTimeout(function () {
                    tm.show();
                    if (tdh.element != undefined) {
                        let seethrough = (tdh.seethrough != undefined) ? tdh.seethrough : false;
                        this.highlightElement(tdh.element, tdh.blocked, seethrough);
                    } else {
                        this.highlightArea(tdh.top, tdh.left, tdh.width, tdh.height, tdh.blocked);
                    }
                }, timeout);
            } else {
                this.highlightArea(0, 0, 0, 0, true);
            }
        } else {
            this.prop('disabled', true);
        }
    },

    start: (key, initial_step) => {
        let tutorial = this.Stories[key];
        let ts = 0;
        let is = (initial_step != undefined) ? (initial_step - 1) : 0;
        for (let d in tutorial) {
            ts++;
        }
        let tm = $('#tutorial-message');
        tm.dataset.tutorialKey = key;
        tm.dataset.tutorialStep = is;
        tm.dataset.tutorialSteps = ts;
        $('#tutorial-total-steps').html(ts);
        $('#tutorial-next-button').stopObserving('click');
        $('#tutorial-next-button').on('click', this.playNextStep);
        this.playNextStep();
    }
};

export default Tutorial;
