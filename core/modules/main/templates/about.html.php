<?php

    $pachno_response->setTitle(__('About %sitename', array('%sitename' => \pachno\core\framework\Settings::getSiteHeaderName())));
    $pachno_response->addBreadcrumb(__('About %sitename', array('%sitename' => \pachno\core\framework\Settings::getSiteHeaderName())), make_url('about'));

?>
<div class="rounded_box borderless mediumgrey" style="margin: 10px auto 0 auto; width: 500px; padding: 5px 5px 15px 5px; font-size: 13px; text-align: center;">
    <div style="text-align: left; padding: 10px;">
        <h1 style="font-size: 25px; margin-bottom: 0px; padding-bottom: 3px;">
            Pachno
            <span style="font-size: 14px; font-weight: normal; color: #888;">
                <?php echo __('Version %pachno_version', array('%pachno_version' => \pachno\core\framework\Settings::getVersion(true))); ?>
            </span>
        </h1>
        <h3 style="margin-top: 0; padding-top: 0;">Beautiful issue tracking and project management</h3>
        <?php echo __('Pachno is an issue tracking system with a strong focus on being friendly &ndash; both for regular users and power users'); ?>.<br>
        <br>
        <?php echo __('Pachno follows an open development model, and is released under an open source software license called the MPL (Mozilla Public License). This license gives you the freedom to pick up the sourcecode for Pachno and work with it any way you need.'); ?><br>
        <br>
        <?php echo __('Extend, develop and change Pachno in any way you want, and do whatever you want with the new piece of software (The only thing you cannot do is call your software Pachno). Please do send us your modifications for inclusion in Pachno.'); ?><br>
        <br>
        <b><?php echo __('Enjoy using Pachno!'); ?></b>
    </div>
    <br>
    <a href="https://pachno.com" target="_blank">Pachno</a>, Copyright &copy; 2002 - <?php echo date('Y'); ?> <b>Pachno team</b><br>
    <?php echo __('Licensed under the MPL 2.0, read it at %link_to_MPL', array('%link_to_MPL' => '<a href="http://opensource.org/licenses/MPL-2.0">opensource.org</a>')); ?>.<br>
    <br>
    <span class="faded_out">
        <?php echo __('Pachno uses icons from the %link_to_iconset', array('%link_to_iconset' => '<a href="https://sourceforge.net/projects/openiconlibrary">Oxygen icon set</a>')); ?>.<br>
        <?php echo __('These icons may be freely distributed under the %link_to_license', array('%link_to_license' => '<a href="http://creativecommons.org/licenses/by-sa/3.0/">CC BY-SA 3.0 License</a>')); ?>.
    </span>
</div>
