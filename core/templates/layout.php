<?php

    /**
     * @var \pachno\core\framework\Response $pachno_response
     * @var string $webroot
     */

    $header_name = \pachno\core\framework\Settings::getSiteHeaderName() ?? 'Pachno';

?>
<!DOCTYPE html>
<html lang="<?= \pachno\core\framework\Settings::getHTMLLanguage(); ?>" style="cursor: progress;">
    <head>
        <meta charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>">
        <?php \pachno\core\framework\Event::createNew('core', 'layout.php::header-begins')->trigger(); ?>
        <meta name="description" content="Pachno, friendly issue tracking">
        <meta name="keywords" content="pachno friendly issue tracking">
        <meta name="author" content="pachno.com">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0"/>
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <title><?= ($pachno_response->hasTitle()) ? strip_tags($header_name . ' ~ ' . $pachno_response->getTitle()) : strip_tags(\pachno\core\framework\Settings::getSiteHeaderName()); ?></title>
        <style>
            @font-face {
              font-family: 'Open Sans';
              font-style: normal;
              font-weight: normal;
              src: url('<?= $webroot; ?>fonts/open_sans.eot');
              src: local('Open Sans'), local('OpenSans'), url('<?= $webroot; ?>fonts/open_sans.woff') format('woff'), url('<?= $webroot; ?>fonts/open_sans.ttf') format('truetype');
            }
            @font-face {
              font-family: 'Open Sans';
              font-style: italic;
              font-weight: normal;
              src: url('<?= $webroot; ?>fonts/open_sans_italic.eot');
              src: local('Open Sans Italic'), local('OpenSans-Italic'), url('<?= $webroot; ?>fonts/open_sans_italic.woff') format('woff'), url('<?= $webroot; ?>fonts/open_sans_italic.ttf') format('truetype');
            }
            @font-face {
              font-family: 'Open Sans';
              font-style: normal;
              font-weight: bold;
              src: url('<?= $webroot; ?>fonts/open_sans_bold.eot');
              src: local('Open Sans Bold'), local('OpenSans-Bold'), url('<?= $webroot; ?>fonts/open_sans_bold.woff') format('woff'), url('<?= $webroot; ?>fonts/open_sans_bold.ttf') format('truetype');
            }
            @font-face {
              font-family: 'Open Sans';
              font-style: italic;
              font-weight: bold;
              src: url('<?= $webroot; ?>fonts/open_sans_bold_italic.eot');
              src: local('Open Sans Bold Italic'), local('OpenSans-BoldItalic'), url('<?= $webroot; ?>fonts/open_sans_bold_italic.woff') format('woff'), url('<?= $webroot; ?>fonts/open_sans_bold_italic.ttf') format('truetype');
            }
            @font-face {
                font-family: 'Simplifica';
                src: url('/fonts/simplifica_typeface-webfont.woff2') format('woff2'),
                url('/fonts/simplifica_typeface-webfont.woff') format('woff');
                font-weight: normal;
                font-style: normal;
            }
        </style>
        <?php $pachno_version = \pachno\core\framework\Settings::getVersion(); ?>
        <link rel="shortcut icon" href="<?= (\pachno\core\framework\Context::isProjectContext() && \pachno\core\framework\Context::getCurrentProject()->hasSmallIcon()) ? \pachno\core\framework\Context::getCurrentProject()->getSmallIconName() : (\pachno\core\framework\Settings::isUsingCustomFavicon() ? \pachno\core\framework\Settings::getFaviconURL() : '/favicon.png?bust=' . $pachno_version); ?>">
        <link title="<?= (\pachno\core\framework\Context::isProjectContext()) ? __('%project_name search', array('%project_name' => \pachno\core\framework\Context::getCurrentProject()->getName())) : __('%site_name search', array('%site_name' => \pachno\core\framework\Settings::getSiteHeaderName())); ?>" href="<?= (\pachno\core\framework\Context::isProjectContext()) ? make_url('project_opensearch', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey())) : make_url('opensearch'); ?>" type="application/opensearchdescription+xml" rel="search">
        <?php foreach ($pachno_response->getFeeds() as $feed_url => $feed_title): ?>
            <link rel="alternate" type="application/rss+xml" title="<?= str_replace('"', '\'', $feed_title); ?>" href="<?= $feed_url; ?>">
        <?php endforeach; ?>
        <?php $rand = \Ramsey\Uuid\Uuid::uuid4()->toString(); ?>
        <?php $minified = ! \pachno\core\framework\Context::isDebugMode() && \pachno\core\framework\Context::isMinifiedAssets() ? '.min' :''; ?>
        <?php include PACHNO_PATH . 'themes' . DS . \pachno\core\framework\Settings::getThemeName() . DS . 'theme.php'; ?>

        <?php [$localcss, $externalcss] = $pachno_response->getStylesheets(); ?>
        <?php foreach ($localcss as $css): ?>
            <?php if ( ! empty($minified)) : $pathinfo = pathinfo($css); $css = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . $minified . '.' . $pathinfo['extension']; endif; ?>
            <link rel="stylesheet" href="<?php print $css; ?>">
        <?php endforeach; ?>
        <?php foreach ($externalcss as $css): ?>
            <link rel="stylesheet" href="<?= $css; ?>">
        <?php endforeach; ?>

        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/v4-shims.css">

        <script type="text/javascript" src="<?= make_url('home'); ?>js/HackTimer<?= $minified; ?>.js"></script>
        <script type="text/javascript" src="<?= make_url('home'); ?>js/HackTimerWorker<?= $minified; ?>.js"></script>
        <script>
            var bust = function (path) {
                return path + '.js?bust=<?= (\pachno\core\framework\Context::isDebugMode()) ? $rand : $pachno_version; ?>';
            };

            var require = {
                waitSeconds: 0,
                baseUrl: '<?= make_url('home'); ?>js',
                paths: {
                    'jquery': bust('jquery-2.1.3<?= $minified ?>'),
                    <?php foreach([
                        'bootstrap-typeahead',
                        'domReady',
                        'jquery.animate-enhanced',
                        'jquery.ba-resize',
                        'jquery.flot',
                        'jquery.flot.canvas',
                        'jquery.flot.categories',
                        'jquery.flot.crosshair',
                        'jquery.flot.dashes',
                        'jquery.flot.errorbars',
                        'jquery.flot.fillbetween',
                        'jquery.flot.image',
                        'jquery.flot.navigate',
                        'jquery.flot.pie',
                        'jquery.flot.resize',
                        'jquery.flot.selection',
                        'jquery.flot.stack',
                        'jquery.flot.symbol',
                        'jquery.flot.threshold',
                        'jquery.flot.time',
                        'jquery.markitup',
                        'jquery.nanoscroller',
                        'jquery-mousewheel',
                        'jquery-private',
                        'jquery-ui',
                        'lightwindow',
                        'mention',
                        'notify',
                        'spectrum',
                        'prototype',
                        'effects',
                        'controls',
                        'mention',
                        'scriptaculous',
                        'slider',
                        'sound',
                        'tablekit',
                        \pachno\core\framework\Settings::getThemeName() .'/theme',
                        'pachno',
                        'pachno/index',
                        'pachno/tools',
                    ] as $path): ?>
                    '<?= $path ?>': bust('<?= $path.$minified ?>'),
                    <?php endforeach; ?>
                    'TweenMax': bust('greensock/TweenMax<?= $minified ?>'),
                    'TweenLite': bust('greensock/TweenLite<?= $minified ?>'),
                    'GSDraggable': bust('greensock/utils/Draggable<?= $minified ?>')
                },
                map: {
                    '*': { 'jquery': 'jquery-private' },
                    'jquery-private': { 'jquery': 'jquery' }
                },
                shim: {
                    'prototype': {
                        // Don't actually need to use this object as
                        // Prototype affects native objects and creates global ones too
                        // but it's the most sensible object to return
                        exports: 'Prototype'
                    },
                    'jquery.markitup': {
                        deps: ['jquery']
                    },
                    'calendarview': {
                        deps: ['prototype'],
                        exports: 'Calendar'
                    },
                    'effects': {
                        deps: ['prototype']
                    },
                    'controls': {
                        deps: ['effects']
                    },
                    'jquery.flot': {
                        deps: ['jquery']
                    },
                    'jquery.flot.selection': {
                        deps: ['jquery', 'jquery.flot']
                    },
                    'jquery.flot.time': {
                        deps: ['jquery', 'jquery.flot']
                    },
                    'jquery.flot.dashes': {
                        deps: ['jquery', 'jquery.flot']
                    },
                    'scriptaculous': {
                        deps: ['prototype', 'controls'],
                        exports: 'Scriptaculous'
                    },
                    'bootstrap-typeahead': {
                        deps: ['jquery']
                    },
                    'mention': {
                        deps: ['jquery', 'bootstrap-typeahead']
                    },
                    'jquery.nanoscroller': {
                        deps: ['jquery']
                    },
                    'jquery.ba-resize': {
                        deps: ['jquery']
                    },
                    'jquery.ui.touch-punch': {
                        deps: ['jquery-ui']
                    },
                    'jquery.animate-enhanced<?= $minified ?>': {
                        deps: ['jquery']
                    },
                     'jquery-ui': {
                         deps: ['jquery.animate-enhanced<?= $minified ?>']
                     },
                     'dragdrop': {
                         deps: ['effects']
                     },
                     'slider': {
                         deps: ['effects']
                     },
                    deps: [<?= join(', ', array_map(function ($element) { return "\"{$element}\""; }, $localjs)); ?>]
                }
            };
        </script>
        <script data-main="pachno" src="<?= make_url('home'); ?>js/require<?= $minified; ?>.js"></script>
        <script src="<?= make_url('home'); ?>js/promise-7.0.4<?= $minified; ?>.js"></script>
        <?php foreach ($externaljs as $js): ?>
            <script type="text/javascript" src="<?= $js; ?>"></script>
        <?php endforeach; ?>
          <!--[if lt IE 9]>
              <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
          <![endif]-->
        <?php \pachno\core\framework\Event::createNew('core', 'layout.php::header-ends')->trigger(); ?>
    </head>
    <body id="body">
        <div id="main_container" class="<?php if (\pachno\core\framework\Context::isProjectContext()) echo 'project-context'; ?> page-<?= \pachno\core\framework\Context::getRouting()->getCurrentRouteName(); ?> cf" data-url="<?= make_url('userdata'); ?>">
            <?php if (!in_array(\pachno\core\framework\Context::getRouting()->getCurrentRouteName(), array('login_page', 'elevated_login_page', 'reset_password'))): ?>
                <?php \pachno\core\framework\Logging::log('Rendering header'); ?>
                <?php require PACHNO_CORE_PATH . 'templates/headertop.inc.php'; ?>
                <?php \pachno\core\framework\Logging::log('done (rendering header)'); ?>
            <?php endif; ?>
            <div id="content_container" class="cf">
                <?php \pachno\core\framework\Logging::log('Rendering content'); ?>
                <?= $content; ?>
                <?php \pachno\core\framework\Logging::log('done (rendering content)'); ?>
            </div>
            <?php \pachno\core\framework\Event::createNew('core', 'layout.php::footer-begins')->trigger(); ?>
            <?php require PACHNO_CORE_PATH . 'templates/footer.inc.php'; ?>
            <?php \pachno\core\framework\Event::createNew('core', 'layout.php::footer-ends')->trigger(); ?>
        </div>
        <?php require PACHNO_CORE_PATH . 'templates/backdrops.inc.php'; ?>
        <script type="text/javascript">
            var Pachno, jQuery;
            require(['domReady', 'pachno/index', 'jquery', 'jquery.nanoscroller'], function (domReady, pachno_index_js, jquery, nanoscroller) {
                domReady(function () {
                    Pachno = pachno_index_js;
                    jQuery = jquery;
                    require(['scriptaculous']);
                    var f_init = function() {Pachno.initialize({ basepath: '<?= $webroot; ?>', data_url: '<?= make_url('userdata'); ?>', autocompleter_url: '<?= (\pachno\core\framework\Context::isProjectContext()) ? make_url('project_quicksearch', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey())) : make_url('quicksearch'); ?>'})};
                    <?php if (\pachno\core\framework\Context::isDebugMode()): ?>
                        Pachno.debug = true;
                        Pachno.debugUrl = '<?= make_url('debugger', array('debug_id' => '___debugid___')); ?>';
                        <?php
                            $session_time = \pachno\core\framework\Context::getSessionLoadTime();
                            $session_time = ($session_time >= 1) ? round($session_time, 2) . 's' : round($session_time * 1000, 1) . 'ms';
                            $load_time = \pachno\core\framework\Context::getLoadTime();
                            $calculated_load_time = $load_time - \pachno\core\framework\Context::getSessionLoadTime();
                            $load_time = ($load_time >= 1) ? round($load_time, 2) . 's' : round($load_time * 1000, 1) . 'ms';
                            $calculated_load_time = ($calculated_load_time >= 1) ? round($calculated_load_time, 2) . 's' : round($calculated_load_time * 1000, 1) . 'ms';
                        ?>
                        Pachno.Core.AjaxCalls.push({location: 'Page loaded', time: new Date(), debug_id: '<?= \pachno\core\framework\Context::getDebugID(); ?>', loadtime: '<?= $load_time; ?>', session_loadtime: '<?= $session_time; ?>', calculated_loadtime: '<?= $calculated_load_time; ?>'});
                        Pachno.loadDebugInfo('<?= \pachno\core\framework\Context::getDebugID(); ?>', f_init);
                    <?php else: ?>
                        f_init();
                    <?php endif; ?>
                });
            });
        </script>
    </body>
</html>
