<header>
    <div id="logo_container">
        <?php \pachno\core\framework\Event::createNew('core', 'header_before_logo')->trigger(); ?>
        <span id="mobile_menuanchor" class="mobile_menuanchor" onclick="$('body').toggleClass('mobile_leftmenu_visible');"><?= fa_image_tag('bars'); ?></span>
        <?php $link = (\pachno\core\framework\Settings::getHeaderLink() == '') ? \pachno\core\framework\Context::getWebroot() : \pachno\core\framework\Settings::getHeaderLink(); ?>
        <a class="logo" href="<?php print $link; ?>"><?php echo image_tag(\pachno\core\framework\Settings::getHeaderIconUrl(), [], true); ?></a>
        <?php if (\pachno\core\framework\Settings::getSiteHeaderName() != '' || \pachno\core\framework\Context::isProjectContext()): ?>
            <div id="logo_name" class="logo_name"><?php echo \pachno\core\framework\Settings::getSiteHeaderName(); ?></div>
            <?php if (\pachno\core\framework\Context::isProjectContext()): ?>
                <a id="logo_project_name" href="<?= make_url('project_dashboard', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey())); ?>" class="logo_name">Pachno<?php //echo \pachno\core\framework\Context::getCurrentProject()->getName(); ?></a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php if (!\pachno\core\framework\Settings::isMaintenanceModeEnabled()): ?>
        <div id="topmenu-container">
            <?php require PACHNO_CORE_PATH . 'templates/headermainmenu.inc.php'; ?>
            <?php if ($pachno_user->canSearchForIssues()): ?>
                <div id="quicksearch-container">
                    <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo (\pachno\core\framework\Context::isProjectContext()) ? make_url('search', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey())) : make_url('search'); ?>" method="get" name="quicksearchform" id="quicksearchform">
                        <input type="hidden" name="fs[text][o]" value="=">
                        <i class="fa fa-circle-o-notch fa-spin fa-fw" id="quicksearch_indicator" style="display: none;"></i>
                        <input type="search" name="fs[text][v]" accesskey="f" id="searchfor" placeholder="<?php echo __('Search'); ?>"><div id="searchfor_autocomplete_choices" class="autocomplete rounded_box"></div>
                        <button type="submit" id="quicksearch_submit"><?= fa_image_tag('search'); ?></button>
                    </form>
                </div>
            <?php endif; ?>
            <?php require PACHNO_CORE_PATH . 'templates/headerusermenu.inc.php'; ?>
        </div>
        <script type="text/javascript">
            $(document).ready(() => {
                var mm = $('#main_menu');
                if (mm.hasClass('project_context')) {
                    mm.select('div.menuitem_container').each(function(elm) {
                        elm.observe('click', function(e) { elm.toggleClass('selected');e.preventDefault(); });
                    });
                }
            });
        </script>
        <?php \pachno\core\framework\Event::createNew('core', 'header_menu_end')->trigger(); ?>
    <?php endif; ?>
</header>
