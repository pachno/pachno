<footer>
    <?php if (!\pachno\core\framework\Settings::isStable()): ?>
        <div class="message-box type-warning">
            <span class="message"><?= fa_image_tag('code-branch'); ?>This version is still in development. Do not use in production.</span>
            <span class="actions"><a href="https://projects.pachno.com/pachno" target="_blank" class="button"><?= __('Report an issue'); ?></a></span>
        </div>
    <?php endif; ?>
    <?php echo image_tag('/logo_128.png', [], true); ?>
    <?php echo link_tag(make_url('about'), 'Pachno').'&nbsp;'.\pachno\core\framework\Settings::getVersion(); ?>
    <?php if ($pachno_user->canAccessConfigurationPage()): ?>
        | <b><?php echo link_tag(make_url('configure'), __('Configure %pachno_name', array('%pachno_name' => \pachno\core\framework\Settings::getSiteHeaderName()))); ?></b>
    <?php endif; ?>
    | <a href="https://pachno.com/support">Support</a>
    | <a href="https://pachno.com/feedback">Feedback</a>
    <?php if (\pachno\core\framework\Context::isDebugMode() && \pachno\core\framework\Logging::isEnabled()): ?>
        <script>
            function pachno_debug_show_menu_tab(tab, clicked) {
                $('debug-bar').childElements().each(function (unclicked) {
                    unclicked.removeClassName('selected');
                });
                clicked.addClassName('selected');
                $('debug-frames-container').childElements().each(function (container) {
                    (container.id == tab) ? container.addClassName('selected') : container.removeClassName('selected');
                });
            }
        </script>
        <div id="___PACHNO_DEBUG_INFO___" style="position: fixed; bottom: 0; left: 0; z-index: 1100; display: none; width: 100%;">
        </div>
        <?php echo image_tag('spinning_16.gif', array('style' => 'position: fixed; bottom: 5px; right: 23px;', 'id' => '___PACHNO_DEBUG_INFO___indicator')); ?>
    <?php endif; ?>
</footer>
