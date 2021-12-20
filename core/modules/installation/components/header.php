<?php

    header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
    header ("Cache-Control: no-store, must-revalidate"); // HTTP/1.1
    header ("Pragma: no-cache"); // HTTP/1.0

    $step = $pachno_request->getParameter('step', 0);

    $mode = (isset($mode)) ? $mode : 'install';

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title>Pachno <?php if ($mode == 'upgrade'): ?>upgrade<?php else: ?>installation<?php endif; ?></title>
        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="author" content="zegenie">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="shortcut icon" href="images/favicon_inverted.png">
        <?php if ($mode != 'upgrade'): ?>
            <script type="text/javascript" src="js/install.js"></script>
        <?php endif; ?>
        <style type="text/css">
            <?php include PACHNO_PATH . 'themes' . DS . 'oxygen' . DS . 'css' . DS . 'theme.css'; ?>
        </style>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/v4-shims.css">
        <style type="text/css">
            @import url('https://fonts.googleapis.com/css?family=DM+Sans:700|Fira+Sans:300,300i,400,400i,600,600i|Fira+Mono:400,500,700&subset=cyrillic,cyrillic-ext,latin-ext&display=swap');

            body { overflow: hidden; }

            h1, h2, h3, h4, h5 { text-shadow: none; border-bottom: 1px dotted #CCC; text-transform: uppercase; font-weight: 400; color: #141823; }
            h1 { font-size: 1.6em; }
            h2 { font-size: 1.4em; margin-bottom: 8px; }
            h3 { font-size: 1.2em; }
            h4 { font-size: 1.1em; }
            h5 { font-size: 1.05em; }
            b, strong { font-weight: 600; }
            h2 .smaller { font-size: 0.9em; text-shadow: none; }
            label { vertical-align: middle; font-weight: 300; font-size: 1em; }
            label[for=agree_license] { font-size: 1.05em; display: inline-block; vertical-align: middle; font-weight: 400; }
            .installation_progress { display: block; margin: 0; }
            .installation_progress h4,
            .installation_progress h5 {
                border: none;
                text-align: center;
                margin: 5px 0 0 15px;
            }
            .installpage .installation_progress {
                margin-bottom: 15px;
            }
            .progress_bar { display: block; width: calc(100% - 30px); margin: 5px 15px; box-sizing: border-box; position: relative; height: 6px; background-color: rgba(12, 216, 182, .3); border-radius: 3px; }
            .progress_bar .filler { background-color: rgb(12, 216, 182); position: absolute; left: 0; top: 0; height: 6px; min-width: 5px; border-radius: 3px; }
            .installation_box { padding: 15px; position: relative; font-size: 1em; line-height: 1.6; }
            .installation_box dl { font-size: 1em; }
            .installation_box dl dd, .installation_box dl dt { vertical-align: middle; font-weight: 300; margin-left: 0; }
            .donate { border: 1px solid #aaC6aa; background-color: #CFE8CF; margin: 0; }
            .grey_box { border: 1px solid #DDD; background-color: #F5F5F5; }
            .command_box { border: 1px dashed #DDD; background-color: #F5F5F5; padding: 4px; font-family: 'Fira Mono', monospace; margin-top: 5px; margin-bottom: 15px; font-size: 0.9em; }
            span.command_box { margin-top: 0; margin-bottom: 0; }
            .features { width: 400px; float: right; margin-left: 10px; }
            .feature { border: 1px solid #DDD; background-color: #F5F5F5; padding: 10px; margin-bottom: 5px; }
            .feature .description { background-color: #FFF; padding: 10px; }
            .feature .content { background-color: transparent; padding: 10px; border-top: 1px solid #EEE; }
            .install_list { display: flex; flex-direction: column; align-items: flex-start; justify-content: center; }
            .install_list dd { padding: 2px 0 5px 0; }
            .helptext { color: #AAA; vertical-align: middle; display: inline-block; margin-left: 5px; }
            .install_list dt { width: 200px; padding: 7px 0; }
            .install_list dt .faded_out { font-weight: 300; }
            .install_list select { padding: 5px; font-weight: 1.1em; height: auto; vertical-align: middle; border: 1px solid #BEBEBE; border-radius: 4px; }
            .main_header_print
            {
                background: #00adc7; /* Old browsers */
                color: white;
                border-radius: 0;
                margin-top: 0;
                display: block;
                -moz-border-radius-bottomleft: 7px;
                -moz-border-radius-bottomright: 7px;
                -webkit-border-bottom-left-radius: 7px;
                -webkit-border-bottom-right-radius: 7px;
                height: 80px !important;
            }

            input[type=text] { padding: 4px; border: 1px solid #BEBEBE; border-radius: 4px;}
            input[type=text].small { width: 100px; margin-top: -5px; }
            input[type=text].dsn { width: 400px; margin-top: -5px; }
            input[type=text].smallest { width: 50px; }

            input[type=submit].primary, input[type=submit].primary:hover,
            input[type=button].primary, input[type=button].primary:hover,
            button.primary, button.primary:hover,
            .button, .button:hover {
                font-weight: 400;
            }


            .padded_box { padding: 3px 10px 10px 10px; }
            .error { padding: 4px; border: 1px solid #B77; background-color: #FEE; color: #955; margin: 10px 0 10px 0; }
            .ok { padding: 4px; border: 1px solid #aaC6aa; background-color: #CFE8CF; margin: 10px 0 10px 0; }
            .error:first-line, .ok:first-line { font-weight: 600; }

            fieldset { border: 1px solid #DDD; margin: 10px 0 10px 0; background-color: #F5F5F5; padding: 0 0 0 8px; }
            legend { font-weight: 300; font-size: 1.1em; color: #141823; text-transform: uppercase; padding: 5px 10px; }

            ul.outlined { margin-top: 5px; }
            ul.outlined li { font-weight: 600; }

            .scope_upgrade { margin: 5px; padding: 0; font-size: 0.9em; }
            .scope_upgrade li { margin: 0; padding: 2px 0; list-style: none; display: inline-block; width: 450px; }
            .scope_upgrade li:hover { background-color: rgba(200, 230, 200, 0.3); }
            .scope_upgrade li label { display: inline-block; width: 180px; vertical-align: middle; text-align: right; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
            .scope_upgrade li select { width: 250px; }

            .progress_buttons { padding: 30px 15px 15px 15px; text-align: right; display: block; min-height: 30px; }
            .progress_buttons .button-next, .progress_buttons .button-finish { float: right; }
            .progress_buttons .button-previous { float: left; }

            ul.backuplist { margin: 15px 0; padding: 0; }
            ul.backuplist > li { background-position: 0 13px; background-repeat: no-repeat; list-style: none; padding: 10px 0 10px 40px; }
            ul.backuplist > li:first-line { font-weight: 600; font-size: 1.1em; }
            ul.backuplist > li.faded { opacity: 0.3; }
            ul.backuplist > li label, ul.backuplist > li input, ul.passwordlist li label, ul.passwordlist li input { vertical-align: middle; }
            ul.backuplist > li > ul { margin: 10px 0; padding: 0; }
            ul.backuplist > li > ul li { margin: 2px 0; display: block; clear: both; float: none; max-width: 800px; }

            ul.passwordlist { list-style: none; margin: 0; padding: 0; }
            ul.passwordlist li { margin: 5px 0 15px; }
            ul.passwordlist li .explanation { padding: 5px; font-size: 1em; }
            .installpage ul li input[type=text], input.username {
                background-image: url('images/user_mono.png');
            }
            input[type=email], input.email {
                background-image: url('images/icon-mono-email.png');
            }
            input.password, input.adminpassword {
                background-image: url('images/password_mono.png');
            }
            .installpage ul li input[type=text], input.username, input.email, input.password, input.adminpassword {
                background-position: 7px 7px;
                background-repeat: no-repeat;
                padding: 5px 5px 5px 28px;
                font-size: 1.1em;
                border-radius: 4px;
                width: 300px;
                margin-top: -5px;
                border: 1px solid #BEBEBE;
            }

            .message-box,
            .prereq {
                display: flex;
                width: calc(100%);
                flex-direction: row;
                box-sizing: border-box;
                margin: 0 0 10px 0;
                padding: 10px;
                border: 1px solid rgba(200, 200, 200, 0.7);
                align-items: center;
                border-radius: 3px;
            }
            .message-box.with-solution {
                border-bottom-left-radius: 0;
                border-bottom-right-radius: 0;
                margin-bottom: 0;
                border-collapse: collapse;
            }
            .prereq {
                margin: 2px;
                padding: 5px 10px;
            }
            .prereq.type-fail {
                background-color: #c72b00;
                color: #FFF;
                margin-bottom: 0;
                border-bottom-left-radius: 0;
                border-bottom-right-radius: 0;
            }
            .prereq.type-warn {
                background-color: rgba(249, 245, 178, 0.6);
            }
            .prereq.type-ok {
                display: none;
                background-color: transparent;
                color: #232323;
                border: none;
            }
            .prereq + .message-box.type-solution {
                margin: 0 2px 15px 2px;
            }
            .message-box.with-solution + .message-box.type-solution,
            .prereq.with-solution + .message-box.type-solution {
                border-top: 0;
                margin-top: 0;
                border-top-left-radius: 0;
                border-top-right-radius: 0;
            }
            .message-box.type-solution {
                background-color: #FFF;
                display: block;
                text-align: left;
                padding: 10px;
                vertical-align: middle;
            }
            .message-box ul {
                margin: 0;
                padding: 0;
                display: block;
            }
            .message-box li {
                margin: 0 0 0 15px;
                padding: 0;
                display: list-item;
            }
            .message-box .fas,
            .message-box .fab,
            .message-box .far {
                margin-right: .5em;
                font-size: 1.5em;
            }
            .prereq .fas,
            .prereq .fab,
            .prereq .far {
                margin-right: .5em;
                font-size: 1em;
            }
            .message-box .message,
            .prereq .message {
                flex: 1 1 auto;
                font-size: 1.1em;
                line-height: 1.3em;
            }
            .message-box .actions {
                flex: 0 0 auto;
                align-items: flex-end;
            }
            .message-box .message + .actions {
                margin-left: 10px;
            }

            .message-box .icon.large {
                font-size: 2.5em;
                margin: 0 .5em 0 .25em;
            }

            .message-box .actions .button {
                /*padding: 4px 7px;*/
            }
            .message-box .actions .button + .button {
                /*padding: 4px 7px;*/
                margin-top: .5em;
            }

            .contact-container {
                flex: 1 1 100%;
                display: flex;
                align-items: center;
                flex-direction: row;
            }

            .button-container {
                flex: 1 1 100%;
                display: flex;
                align-items: flex-start;
                flex-direction: row;
            }

            .installation_container {
                border-radius: 4px;
                overflow: hidden;
                margin: 50px auto;
                box-sizing: border-box;
                width: 50%;
                max-width: 1024px;
                display: flex;
                flex-direction: column;
                border: none;
                box-shadow: 0 1px 4px 0 rgba(0, 0, 0, 0.16), 0 0 1px 0 rgba(0, 0, 0, 0.16);
            }
            .header_container {
                flex: 0 0 auto;
                padding: 15px;
                display: flex;
                flex-direction: row;
                align-items: center;
                background: linear-gradient(to right, #0cd8b6 0%, #00adc7 20%);
                color: #FFF;
            }
            .logo_image_container {
                flex: 0 0 36px;
                padding-right: 15px;
            }
            .logo_image_container img {
                width: 100%;
            }
            .logo_name_container {
                flex: 1 1 auto;
                margin-left: 3px;
                line-height: 1.4em;
            }
            .header_container .logo_name_container {
                color: #FFF;
                font-size: 1.8rem;
                font-family: "DM Sans";
                letter-spacing: -1px;
                font-weight: 700;
            }
            .installation_main_container {
                flex: 1 1 auto;
                max-height: calc(100vh - 200px);
                overflow: auto;
            }
            .footer_container {
                box-shadow: inset 0 5px 5px -5px rgba(0,0,0,0.25);
                flex: 0 0 auto;
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-items: center;
                text-align: left;
                background-color: #F5F5F5;
                border-top: 1px solid #DDD;
                padding: 15px;
            }
            .highlight {
                border-bottom: 2px solid #00adc7;
                display: inline-flex;
                align-items: center;
            }

            .highlight .icon {
                margin: 0 .5em;
            }


        </style>
    </head>
    <body>
        <?php if ($mode == 'upgrade'): ?>
            <script type="text/javascript" src="js/dist/pachno/main.js?bust=<?= \pachno\core\framework\Settings::getVersion(); ?>"></script>
        <?php endif; ?>
        <div class="installation_container">
            <div class="header_container">
                <div class="logo_image_container"><img src="/logo_white_192.png" alt="Pachno - Installation"></div>
                <div class="logo_name_container">
                    <div class="logo_name">Pachno</div>
                </div>
            </div>
            <div class="installation_main_container">
                <?php if ($mode == 'install' && $step > 0): ?>
                    <div class="installation_progress">
                        <h4>Installation progress</h4>
                        <div class="progress_bar"><div class="filler" style="width: <?php echo ($step == 6) ? 100 : $step * 15; ?>%;"></div></div>
                    </div>
                <?php endif; ?>
