<!DOCTYPE html>
<html>
    <head>
        <style>
            @import url('https://fonts.googleapis.com/css?family=Fira+Mono:400,500,700|Source+Sans+Pro:400,400i,600,600i|Lilita+One:400&subset=cyrillic,cyrillic-ext,latin-ext&display=swap');

            body, td, th {
                padding: 0px;
                margin: 0px;
                background-color: #FFF;
                font-family: 'Source Sans Pro', 'Open Sans', sans-serif;
                font-style: normal;
                font-weight: normal;
                text-align: left;
                font-size: 16px;
                line-height: 1.2em;
                color: #141823;
            }
            h1, h2, h3 {
                font-weight: 600;
                display: flex;
                align-items: center;
                border: none;
                margin: 5px 0;
                padding: 0;
            }
            h1.logo {
                font-family: 'Lilita One', 'Open Sans', sans-serif;
                margin: 0;
                padding: 10px;
                background: #0C8990;
                color: #FFF;
                font-weight: 400;
                font-size: 1.8rem;
                border-radius: 2px 2px 0 0;
            }
            h1 span, h2 span, h3 span {
                flex: 1 1 auto;
                line-height: 1em;
            }
            .image-container {
                flex: 0 0 36px;
                padding: 0;
                margin-right: 2px;
            }
            .image-container img {
                width: 100%;
                display: inline-block;
                vertical-align: middle;
            }
            b, strong { font-weight: 600; }
            h2 {
                font-size: 1.5rem;
            }
            h3 {
                font-size: 1.3rem;
            }
            a { color: #0C8990; font-weight: 400; display: inline-block; padding: 1px; text-decoration: underline; }
            input[type="text"], input[type="password"] { float: left; margin-right: 15px; }
            label { float: left; font-weight: 600; margin-right: 5px; display: block; width: 150px; }
            label span { font-weight: normal; color: #888; }
            .rounded_box {
                background: transparent;
                margin: 0;
                border-radius: 2px;
                border: none;
                box-shadow: 1px 2px 8px rgba(0, 0, 0, 0.15), 0 3px 4px rgba(0, 0, 0, 0.15);
                padding: 0;
            }
            .rounded_box h4 { margin-bottom: 0px; margin-top: 7px; font-size: 14px; }
            .error_content { padding: 10px; }
            .description { padding: 3px 3px 3px 0;}
            pre { overflow: scroll; padding: 5px; }
            .command_box { border: 1px dashed #DDD; background-color: #F5F5F5; padding: 4px; font-family: 'Fira Mono', monospace; display: inline-block; margin-top: 5px; margin-bottom: 15px; }
        </style>
        <link rel="shortcut icon" href="favicon_inverted.png">
    </head>
    <body>
        <div class="rounded_box" style="margin: 30px auto 0 auto; width: 700px;">
            <h1 class="logo"><span class="image-container"><img src="logo_128.png"></span><span>Pachno</span></h1>
            <div class="error_content">
                <h2>External php dependencies not installed</h2>
                <p>
                    Pachno uses the <a href="http://getcomposer.org">composer</a> dependency management tool to install and update required libraries.<br>
                    Before you can use or install Pachno, you must install these required libraries using composer.<br>
                    <br>
                    If you have already downloaded and installed composer, you can perform this installation by running the following command from the directory containing Pachno:<br>
                    <div class="command_box">php /path/to/composer.phar install</div><br>
                    When the command completes, refresh this page and continue.<br>
                    <br>
                    You can read more about composer &ndash; including how to install it &ndash; on <a href="http://getcomposer.org">http://getcomposer.org</a>
                </p>
            </div>
        </div>
    </body>
</html>
