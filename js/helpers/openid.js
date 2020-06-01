const OpenID = {
    version: '1.3', // version constant
    demo: false,
    demo_text: null,
    cookie_expires: 6 * 30, // 6 months.
    cookie_name: 'openid_provider',
    cookie_path: '/',
    img_path: 'images/',
    locale: 'en', // is set in openid-<locale>.js
    sprite: 'en', // usually equals to locale, is set in
    // openid-<locale>.js
    signin_text: null, // text on submit button on the form
    all_small: false, // output large providers w/ small icons
    image_title: '%openid_provider_name', // for image title

    input_id: 'openid_identifier',
    provider_url: null,
    provider_id: null,
    providers_small: null,
    providers_large: null,
    /**
     * Class constructor
     *
     * @return {Void}
     */
    init: function () {
        var openid_btns = $('#openid_btns');
        if ($('#openid_choice')) {
            $('#openid_choice').css({
                display: 'block'
            });
        }
        if ($('#openid_input_area')) {
            $('#openid_input_area').innerHTML = "";
        }
        var i = 0;
        // add box for each provider
        for (id in this.providers_large) {
            box = this.getBoxHTML(id, this.providers_large[id], (this.all_small ? 'small' : 'large'), i++);
            openid_btns.append(box);
        }
        if (this.providers_small) {
            openid_btns.append('<br/>');
            for (id in this.providers_small) {
                box = this.getBoxHTML(id, this.providers_small[id], 'small', i++);
                openid_btns.append(box);
            }
        }
//		$('#openid_form').submit = this.submit;
//		var box_id = this.readCookie();
//		if (box_id) {
//			this.signin(box_id, true);
//		}
    },
    /**
     * @return {String}
     */
    getBoxHTML: function (box_id, provider, box_size, index) {
        var image_ext = box_size == 'small' ? '.ico.png' : '.png';
        return '<a title="' + this.image_title.replace('%openid_provider_name', provider["name"]) + '" href="javascript:Pachno.OpenID.signin(\'' + box_id + '\');"'
            + 'class="' + box_id + ' openid_' + box_size + '_btn button"><img src="' + Pachno.basepath + 'images/openid_providers.' + box_size + '/' + box_id + image_ext + '"></a>';
    },
    /**
     * Provider image click
     *
     * @return {Void}
     */
    signin: function (box_id) {
        var provider = (this.providers_large[box_id]) ? this.providers_large[box_id] : this.providers_small[box_id];
        if (!provider) {
            return;
        }
        this.highlight(box_id);
        this.provider_id = box_id;
        this.provider_url = provider['url'];
        // prompt user for input?
        if (provider['label']) {
            this.useInputBox(provider);
        } else {
            $('#openid_input_area').innerHTML = '';
            this.submit();
            $('#openid_form').submit();
        }
    },
    /**
     * Sign-in button click
     *
     * @return {Boolean}
     */
    submit: function () {
        var url = this.provider_url;
        var username_field = $('#openid_username');
        var username = username_field ? $('#openid_username').val() : '';
        if (url) {
            url = url.replace('{username}', username);
            this.setOpenIdUrl(url);
        }
        return true;
    },
    /**
     * @return {Void}
     */
    setOpenIdUrl: function (url) {
        var hidden = document.getElementById(this.input_id);
        if (hidden != null) {
            hidden.val(url);
        } else {
            $('#openid_form').append('<input type="hidden" id="' + this.input_id + '" name="' + this.input_id + '" value="' + url + '"/>');
        }
    },
    /**
     * @return {Void}
     */
    highlight: function (box_id) {
        // remove previous highlight.
        var highlight = $('.openid_highlight');
        if (highlight[0]) {
            highlight[0].removeClass('button-pressed');
            highlight[0].removeClass('openid_highlight');
        }
        // add new highlight.
        var box = $('.' + box_id)[0];
        box.addClass('openid_highlight');
        box.addClass('button-pressed');
    },
    setCookie: function (value) {
        var date = new Date();
        date.setTime(date.getTime() + (this.cookie_expires * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();
        document.cookie = this.cookie_name + "=" + value + expires + "; path=" + this.cookie_path;
    },
    readCookie: function () {
        var nameEQ = this.cookie_name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ')
                c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0)
                return c.substring(nameEQ.length, c.length);
        }
        return null;
    },
    /**
     * @return {Void}
     */
    useInputBox: function (provider) {
        var input_area = $('#openid_input_area');
        var html = '';
        var id = 'openid_username';
        var value = '';
        var label = provider['label'];
        var style = '';
        if (provider['name'] == 'OpenID') {
            id = this.input_id;
            value = 'http://';
            style = 'background: #FFF url(' + Pachno.basepath + 'images/openid-inputicon.gif) no-repeat scroll 0 50%; padding-left:18px;';
        }
        html = '<input id="' + id + '" type="text" style="' + style + '" name="' + id + '" value="' + value + '" />';
        if (label) {
            html += '<label for="' + id + '">' + label + '</label>';
        }
        input_area.innerHTML = html;
        $('#openid_submit_button').show();

//		$('#openid_submit').onclick = this.submit;
        $(id).focus();
    },
    setDemoMode: function (demoMode) {
        this.demo = demoMode;
    }
};

export default OpenID;
