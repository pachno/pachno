<?php if (\pachno\core\framework\Context::isDebugMode() && \pachno\core\framework\Logging::isEnabled()): ?>
    <footer>
        <script>
            function pachno_debug_show_menu_tab(tab, clicked) {
                $('#debug-bar').children().removeClass('selected');
                clicked.addClass('selected');
                $('#debug-frames-container').children().each(function () {
                    let container = $(this);
                    (container.attr('id') == tab) ? container.addClass('selected') : container.removeClass('selected');
                });
            }
        </script>
        <div id="___PACHNO_DEBUG_INFO___" style="position: fixed; bottom: 0; left: 0; z-index: 1100; display: none; width: 100%;">
        </div>
        <?php echo image_tag('spinning_16.gif', array('style' => 'position: fixed; bottom: 5px; right: 23px;', 'id' => '___PACHNO_DEBUG_INFO___indicator')); ?>
    </footer>
<?php endif; ?>
