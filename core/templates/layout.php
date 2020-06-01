<?php

    use pachno\core\framework\Context;

    /**
     * @var \pachno\core\framework\Response $pachno_response
     * @var string $webroot
     */

    $header_name = \pachno\core\framework\Settings::getSiteHeaderName() ?? 'Pachno';

?>
<!DOCTYPE html>
<html lang="<?= \pachno\core\framework\Settings::getHTMLLanguage(); ?>" style="cursor: progress;">
    <head>
        <meta charset="<?= Context::getI18n()->getCharset(); ?>">
        <?php \pachno\core\framework\Event::createNew('core', 'layout.php::header-begins')->trigger(); ?>
        <meta name="description" content="Pachno, friendly issue tracking">
        <meta name="keywords" content="pachno friendly issue tracking">
        <meta name="author" content="pachno.com">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0"/>
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <base href="<?= rtrim(Context::getWebroot(), '/'); ?>">
        <title><?= ($pachno_response->hasTitle()) ? strip_tags($header_name . ' ~ ' . $pachno_response->getTitle()) : strip_tags(\pachno\core\framework\Settings::getSiteHeaderName()); ?></title>
        <style>
            @import url('https://fonts.googleapis.com/css?family=Fira+Mono:400,500,700|Source+Sans+Pro:300,300i,400,400i,600,600i|Catamaran:300,300i,400,400i,600,600i&subset=cyrillic,cyrillic-ext,latin-ext');
            @font-face {
                font-family: 'Simplifica';
                src: url('/fonts/simplifica_typeface-webfont.woff2') format('woff2'),
                url('/fonts/simplifica_typeface-webfont.woff') format('woff');
                font-weight: normal;
                font-style: normal;
            }
        </style>
        <?php $pachno_version = \pachno\core\framework\Settings::getVersion(); ?>
        <link rel="shortcut icon" href="<?= (Context::isProjectContext()) ? Context::getCurrentProject()->getIconName() : (\pachno\core\framework\Settings::isUsingCustomFavicon() ? \pachno\core\framework\Settings::getFaviconURL() : '/favicon.png?bust=' . $pachno_version); ?>">
        <link title="<?= (Context::isProjectContext()) ? __('%project_name search', array('%project_name' => Context::getCurrentProject()->getName())) : __('%site_name search', array('%site_name' => \pachno\core\framework\Settings::getSiteHeaderName())); ?>" href="<?= (Context::isProjectContext()) ? make_url('project_opensearch', array('project_key' => Context::getCurrentProject()->getKey())) : make_url('opensearch'); ?>" type="application/opensearchdescription+xml" rel="search">
        <?php foreach ($pachno_response->getFeeds() as $feed_url => $feed_title): ?>
            <link rel="alternate" type="application/rss+xml" title="<?= str_replace('"', '\'', $feed_title); ?>" href="<?= $feed_url; ?>">
        <?php endforeach; ?>
        <?php $rand = \Ramsey\Uuid\Uuid::uuid4()->toString(); ?>
        <?php $minified = ! Context::isDebugMode() && Context::isMinifiedAssets() ? '.min' :''; ?>
        <?php include PACHNO_PATH . 'themes' . DS . \pachno\core\framework\Settings::getThemeName() . DS . 'theme.php'; ?>

        <?php [$localcss, $externalcss] = $pachno_response->getStylesheets(); ?>
        <?php foreach ($localcss as $css): ?>
            <?php if ( ! empty($minified)) : $pathinfo = pathinfo($css); $css = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . $minified . '.' . $pathinfo['extension']; endif; ?>
            <link rel="stylesheet" href="<?php print $css; ?>">
        <?php endforeach; ?>
        <?php foreach ($externalcss as $css): ?>
            <link rel="stylesheet" href="<?= $css; ?>">
        <?php endforeach; ?>
        <!-- Editor's Dependecy Style -->
        <link
                rel="stylesheet"
                href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.48.4/codemirror.min.css"
        />
        <!-- Editor's Style -->
        <link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.min.css" />
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/v4-shims.css">

        <link
                rel="stylesheet"
                href="https://unpkg.com/simplebar@latest/dist/simplebar.css"
        />
        <?php \pachno\core\framework\Event::createNew('core', 'layout.php::header-ends')->trigger(); ?>
    </head>
    <body id="pachno-body"
      <?php if (Context::isDebugMode()) echo 'data-debug-mode="1" data-debug-url="' . make_url('debugger', array('debug_id' => '___debugid___')) . '"'; ?>
      data-webroot="<?= $webroot; ?>"
      data-data-url="<?= make_url('userdata'); ?>"
      data-autocompleter-url="<?= (Context::isProjectContext()) ? make_url('project_quicksearch', array('project_key' => Context::getCurrentProject()->getKey())) : make_url('quicksearch'); ?>"
    >
    <?php foreach ($localjs as $js): ?>
        <script type="text/javascript" src="<?= make_url('home'); ?>js/dist/<?= $js; ?>.js?bust=<?= (Context::isDebugMode()) ? $rand : $pachno_version; ?>"></script>
    <?php endforeach; ?>
    <script src="https://unpkg.com/simplebar@latest/dist/simplebar.min.js"></script>
    <script type="text/javascript" src="<?= make_url('home'); ?>js/HackTimer<?= $minified; ?>.js"></script>
    <script type="text/javascript" src="<?= make_url('home'); ?>js/HackTimerWorker<?= $minified; ?>.js"></script>
    <?php foreach ($externaljs as $js): ?>
        <script type="text/javascript" src="<?= $js; ?>"></script>
    <?php endforeach; ?>
        <div id="main_container" class="<?php if (\pachno\core\framework\Context::isProjectContext()) echo 'project-context'; ?> page-<?= \pachno\core\framework\Context::getRouting()->getCurrentRoute()->getName(); ?> cf <?php if ($pachno_response->isFullscreen()) echo ' fullscreen'; ?>" data-url="<?= make_url('userdata'); ?>">
            <?php if (!Context::getRouting()->getCurrentRoute()->isAnonymous()): ?>
                <?php \pachno\core\framework\Logging::log('Rendering header'); ?>
                <?php require PACHNO_CORE_PATH . 'templates/headertop.inc.php'; ?>
                <?php \pachno\core\framework\Logging::log('done (rendering header)'); ?>
            <?php endif; ?>
            <div id="content_container" class="cf">
                <?php \pachno\core\framework\Logging::log('Rendering content'); ?>
                <?= $content; ?>
                <?php \pachno\core\framework\Logging::log('done (rendering content)'); ?>
            </div>
            <?php if (!Context::getRouting()->getCurrentRoute()->isAnonymous()): ?>
                <?php \pachno\core\framework\Event::createNew('core', 'layout.php::footer-begins')->trigger(); ?>
                <?php require PACHNO_CORE_PATH . 'templates/footer.inc.php'; ?>
                <?php \pachno\core\framework\Event::createNew('core', 'layout.php::footer-ends')->trigger(); ?>
            <?php endif; ?>
        </div>
        <?php require PACHNO_CORE_PATH . 'templates/backdrops.inc.php'; ?>
        <?php if (Context::isDebugMode()): ?>
            <script type="text/javascript">
                Pachno.on(Pachno.EVENTS.ready, () => {
                    <?php
                        $session_time = Context::getSessionLoadTime();
                        $session_time = ($session_time >= 1) ? round($session_time, 2) . 's' : round($session_time * 1000, 1) . 'ms';
                        $load_time = Context::getLoadTime();
                        $calculated_load_time = $load_time - Context::getSessionLoadTime();
                        $load_time = ($load_time >= 1) ? round($load_time, 2) . 's' : round($load_time * 1000, 1) . 'ms';
                        $calculated_load_time = ($calculated_load_time >= 1) ? round($calculated_load_time, 2) . 's' : round($calculated_load_time * 1000, 1) . 'ms';
                    ?>
                    Pachno.debugger.updateDebugInfo({location: 'Page loaded', time: new Date(), debug_id: '<?= Context::getDebugID(); ?>', loadtime: '<?= $load_time; ?>', session_loadtime: '<?= $session_time; ?>', calculated_loadtime: '<?= $calculated_load_time; ?>'});
                    Pachno.debugger.loadDebugInfo('<?= Context::getDebugID(); ?>');
                });
            </script>
        <?php endif; ?>
    </body>
</html>
