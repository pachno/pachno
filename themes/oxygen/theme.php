<?php

    /**
     * Configuration for theme
     */

    $pachno_response->addStylesheet(make_url('asset_css', array('theme_name' => 'oxygen', 'css' => 'theme.css')));
    $pachno_response->addStylesheet(make_url('asset_css', array('theme_name' => 'oxygen', 'css' => 'widgets.css')));
    $pachno_response->addStylesheet(make_url('asset_css', array('theme_name' => 'oxygen', 'css' => 'mobile.css')));
    \pachno\core\framework\Settings::setIconsetName('oxygen');
?>
<style>
    #header_banner { background-image: url('<?= image_url('mobile_header_banner.png'); ?>'); }
    #openid-signin-button.persona-button span:after{ background-image: url('<?php echo $webroot; ?>images/openid_providers.small/openid.ico.png'); }
    #regular-signin-button.persona-button span:after{ background-image: url('<?php echo $webroot; ?>images/footer_logo.png'); }
    #forgot_password_username { background-image: url('<?php echo $webroot; ?>images/user_mono.png'); }

    .module .rating { background-image:url('<?php echo $webroot; ?>images/star_faded_small.png'); }
    .module .rating .score { background-image:url('<?php echo $webroot; ?>images/star_small.png'); }

    <?php /*
    .markItUp .markItUpButton1 a { background-image:url('<?php echo $webroot; ?>images/markitup/h1.png'); }
    .markItUp .markItUpButton2 a { background-image:url('<?php echo $webroot; ?>images/markitup/h2.png'); }
    .markItUp .markItUpButton3 a { background-image:url('<?php echo $webroot; ?>images/markitup/h3.png'); }
    .markItUp .markItUpButton4 a { background-image:url('<?php echo $webroot; ?>images/markitup/h4.png'); }
    .markItUp .markItUpButton5 a { background-image:url('<?php echo $webroot; ?>images/markitup/h5.png'); }
    .markItUp .markItUpButton6 a { background-image:url('<?php echo $webroot; ?>images/markitup/bold.png'); }
    .markItUp .markItUpButton7 a { background-image:url('<?php echo $webroot; ?>images/markitup/italic.png'); }
    .markItUp .markItUpButton8 a { background-image:url('<?php echo $webroot; ?>images/markitup/stroke.png'); }
    .markItUp .markItUpButton9 a { background-image:url('<?php echo $webroot; ?>images/markitup/list-bullet.png'); }
    .markItUp .markItUpButton10 a { background-image:url('<?php echo $webroot; ?>images/markitup/list-numeric.png'); }
    .markItUp .markItUpButton11 a { background-image:url('<?php echo $webroot; ?>images/markitup/picture.png'); }
    .markItUp .markItUpButton12 a { background-image:url('<?php echo $webroot; ?>images/markitup/link.png'); }
    .markItUp .markItUpButton13 a { background-image:url('<?php echo $webroot; ?>images/markitup/url.png'); }
    .markItUp .markItUpButton14 a { background-image:url('<?php echo $webroot; ?>images/markitup/quotes.png'); }
    .markItUp .markItUpButton15 a { background-image:url('<?php echo $webroot; ?>images/markitup/code.png'); }
    .markItUp .preview a { background-image:url('<?php echo $webroot; ?>images/markitup/preview.png'); }
    .markItUpResizeHandle { background-image:url('<?php echo $webroot; ?>images/markitup/handle.png'); }
    .markItUpHeader ul .markItUpDropMenu { background-image: url('<?php echo $webroot; ?>images/markitup/menu.png'); }
    .markItUpHeader ul ul .markItUpDropMenu { background-image: url('<?php echo $webroot; ?>images/markitup/submenu.png'); }
    */ ?>

    #user_notifications .toggling { background: url('<?php echo $webroot; ?>images/spinning_16.gif') no-repeat 374px 11px; }

</style>
