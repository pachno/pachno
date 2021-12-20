<?php if (is_array($pachno_summary)): ?>
    <style type="text/css">
        /* logging colors for categories */
        <?php foreach(array("main", "B2DB", "core", "routing", "i18n", "cache", "search", "publish") as $category): ?>
            .cat-<?php echo $category; ?> .badge.catname {
                background-color:#<?php echo \pachno\core\framework\Logging::getCategoryColor($category); ?>;
                color: #FFF;
            }
        <?php endforeach; ?>
        .catname { text-shadow: none; text-transform: uppercase; }
        h1 .log-selectors { float: right; font-size: 0.7em; }
        h1 .log-selectors .badge { opacity: 0.2; cursor: pointer; }
        #debug-frames-container .expander { cursor: pointer; }
        h1 .log-selectors .badge.selected { opacity: 1; }
        #log_timing ul, #log_ajax ul, #log_messages ul, #debug_routes ul, #log_sql ol { list-style: none; padding: 0; margin: 0; }
        #log_timing ul li, #log_ajax ul li, #log_messages ul li { font-size: 1.1em; list-style: none; padding: 4px; margin: 1px 0; clear: both; display: block; border: 1px solid transparent; }
        #log_timing ul li:hover, #log_ajax ul li:hover, #log_messages ul li:hover, #debug_routes ul li:hover { background-color: rgba(230, 230, 230, 0.1); border-color: rgba(100, 100, 100, 0.1); }
        #debug_routes ul li.selected { background-color: rgba(160, 230, 160, 0.2); }
        #debug_routes ul li.selected:hover { background-color: rgba(160, 230, 160, 0.4); }
        #log_sql li .sql { font-family: monospace; font-size: 1em; display: block; margin: 5px 0; padding: 5px; border: 1px dotted rgba(100, 100, 100, 0.1); background-color: rgba(200, 200, 200, 0.2); color: #888; text-shadow: none; }
        #debug-frames-container .partial, #debug-frames-container .logmessage, #debug-frames-container .badge.url, #debug-frames-container .badge.method, #debug-frames-container .expander { display: inline-block; font-weight: normal; font-size: 1.1em; vertical-align: middle; }
        #debug-frames-container .partial.code { font-family: 'Fira Mono', monospace; background: rgba(100, 100, 100, 0.1); border: 1px solid rgba(100, 100, 100, 0.2); padding: 1px 3px; text-shadow: none; vertical-align: middle; }
        #debug-frames-container .partial { display: inline-block; vertical-align: middle; }
        #debug-frames-container .file-icon { font-size: 1.2em; margin-right: 6px; margin-left: 6px; vertical-align: middle; color: rgba(100, 100, 100, 0.6); }
        #debug-frames-container .badge.url { text-align: left; }
        #debug-frames-container .partial.hidden, #debug-frames-container .expander .collapse, #debug-frames-container li.expanded .expander .expand { display: none; }
        #debug-frames-container li:hover > .partial.hidden, #debug-frames-container li.expanded .expander .collapse { display: initial; }
        #debug-frames-container ul.backtrace { display: none; list-style: none; padding: none; margin: none; }
        #debug-frames-container ul.backtrace li { padding: 2px 0; }
        #debug-frames-container ul.backtrace li.b2db-hidden { display: none; }
        #debug-frames-container ul.backtrace li.b2db-hidden-toggler { cursor: pointer; }
        #debug-frames-container ul.backtrace.b2db-hidden-visible li.b2db-hidden-toggler { display: none; }
        #debug-frames-container ul.backtrace.b2db-hidden-visible li.b2db-hidden { display: block; }
        #debug-frames-container li.expanded ul.backtrace { display: block; margin-top: 15px; }
        #debug-frames-container .badge { display: inline-block; font-weight: normal; border-radius: 3px; padding: 3px 5px; text-align: center; min-width: 30px; margin-right: 5px; text-shadow: none; }
        #debug-frames-container .badge .fa, #debug-bar .fa { display: inline-block; width: 16px; text-align: center; vertical-align: middle; margin-right: 3px; }
        #debug-frames-container .badge.timing { background-color: rgba(200, 225, 200, 0.5); min-width: 85px; font-size: 1.05em; text-align: left; }
        #debug-frames-container .badge.timing.session { background-color: rgba(225, 225, 200, 0.5); }
        #debug-frames-container .badge.timing.calculated { background-color: rgba(200, 225, 200, 0.3); }
        #debug-frames-container .badge.csrf { background-color: rgba(200, 225, 200, 0.5); opacity: 0.2; }
        #debug-frames-container .badge.csrf.enabled { opacity: 1; }
        #debug-frames-container .badge.timestamp { background-color: rgba(255, 255, 255, 1); min-width: 90px; }
        #debug-frames-container .badge.count, #debug-frames-container .badge.loglevel { background-color: rgba(225, 225, 225, 0.5); }
        #debug-frames-container .badge.routename { background-color: rgba(225, 225, 225, 0.5); min-width: 200px; }
        #debug-frames-container .badge.classname { background-color: rgba(235, 235, 205, 0.5); min-width: 200px; }
        #debug-frames-container .badge.classcount { background-color: rgba(205, 205, 235, 0.5); min-width: 30px; }
        #debug-frames-container .badge.modulename { background-color: rgba(225, 225, 225, 0.5); margin: 0; }
        #debug-bar { cursor: pointer; text-align: left; border-top: 1px solid rgba(100, 100, 100, 0.2); width: 100%; padding: 0; background-color: #FAFAFA; z-index: 10000; box-shadow: 0 -3px 2px rgba(100, 100, 100, 0.2); font-size: 1.1em; list-style: none; margin: 0; height: 41px; transition: height 0.3s ease-in-out, width 0.3s ease-in-out, top 0.3s ease-in-out; display: flex; }
        #debug-bar.enabled { position: fixed; top: 0; left: 0; border: 0; }
        #debug-bar.minimized { width: 50px; }
        #debug-bar > li { display: flex; padding: 11px 20px; border-right: 1px solid rgba(100, 100, 100, 0.2); border-left: 1px solid rgba(255, 255, 255, 0.8); vertical-align: middle; align-items: center; justify-content: flex-start; }
        #debug-bar > li:first-child { border-left: none; }
        #debug-bar > li:last-child { margin-left: auto; border-left: none; }
        #debug-bar.enabled > li.selected { background-color: #FFF; box-shadow: 0 -4px 4px rgba(100, 100, 100, 0.3); }
        #debug-bar > li .far, #debug-bar > li .fas { margin-right: .35em; flex: 0 0 auto; }
        #debug-bar > li > span { display: inline-block; vertical-align: middle; }
        #debug-bar.enabled + #debug-frames-container { display: block; }
        #debug-bar .minimizer { display: none; }
        #debug-bar.minimized > li:not(.maximizer) { display: none; }
        #debug-bar.enabled .maximizer { display: none; }
        #debug-bar.enabled .minimizer { display: inline-block; cursor: pointer; float:right; }
        #debug-bar .maximizer { display: inline-block; cursor: pointer; float:right; }
        #debug-frames-container { display: none; width: 100%; height: calc(100% - 40px); box-sizing: border-box; padding: 0; margin: 0; position: fixed; left: 0; top: 40px; background: #FFF; }
        #debug-frames-container > li { display: none }
        #debug-frames-container > li.selected { display: block; text-align: left; position: absolute; height: 100%; width: 100%; left: 0; top: 0; right: 0; bottom: 0; box-sizing: border-box; padding: 5px; background: #FFF; margin: 0; overflow: auto; }
        #debug-frames-container > li h1 { font-size: 17px; font-weight: normal; color: #999; border: 1px solid rgba(100, 100, 100, 0.2); background-color: rgba(200, 200, 200, 0.1); box-shadow: inset 0 0 3px rgba(100, 100, 100, 0.1); padding: 5px; text-transform: uppercase; }
    </style>
    <ul class="" id="debug-bar" onclick="$(this).addClass('enabled');">
        <li onclick="pachno_debug_show_menu_tab('debug_routes', $(this));">
            <?php echo fa_image_tag('desktop'); ?>
            <span>
                <?php if ($pachno_summary['routing']): ?>
                    [<i><?php echo $pachno_summary['routing']['name']; ?></i>] <?php echo $pachno_summary['routing']['module']; ?> / <?php echo $pachno_summary['routing']['action'][0]; ?>::run<?php echo $pachno_summary['routing']['action'][1]; ?>()
                <?php else: ?>
                    [<i>Unknown route</i>] - / -
                <?php endif; ?>
            </span>
        </li>
        <li onclick="pachno_debug_show_menu_tab('log_timing', $(this));" title="Click to toggle timing overview">
            <?php echo fa_image_tag('tachometer-alt'); ?>
            <span>
                <?php echo $pachno_summary['load_time']; ?> <span title="Time spent by php loading session data">(<?= $pachno_summary['session_initialization_time']; ?>)</span> /
                <?php echo round($pachno_summary['memory'] / 1000000, 2); ?>MiB
            </span>
        </li>
        <li onclick="pachno_debug_show_menu_tab('log_ajax', $(this));" title="Click to toggle ajax calls list">
            <?php echo fa_image_tag('globe'); ?>
            <span id="debug_ajax_count">1</span>
        </li>
        <li onclick="pachno_debug_show_menu_tab('scope_settings', $(this));"  title="Generated hostname: <?php echo $pachno_summary['scope']['hostnames']; ?>">
            <?php echo fa_image_tag('clone'); ?>
            <span><b>Scope: </b><?php echo $pachno_summary['scope']['id']; ?></span>
        </li>
        <?php if (array_key_exists('db', $pachno_summary)): ?>
            <li onclick="pachno_debug_show_menu_tab('log_sql', $(this));" title="Database queries">
                <?php echo fa_image_tag('database'); ?>
                <span>
                    <b><?php echo count($pachno_summary['db']['queries']); ?></b> (<?php echo ($pachno_summary['db']['timing'] > 1) ? round($pachno_summary['db']['timing'], 2) . 's' : round($pachno_summary['db']['timing'] * 1000, 1) . 'ms'; ?>)
                </span>
            </li>
            <li onclick="pachno_debug_show_menu_tab('log_objectpopulation', $(this));" title="Database object population">
                <?php echo fa_image_tag('cubes'); ?>
                <span>
                    <b><?php echo $pachno_summary['db']['objectcount']; ?></b> (<?php echo ($pachno_summary['db']['objecttiming'] > 1) ? round($pachno_summary['db']['objecttiming'], 2) . 's' : round($pachno_summary['db']['objecttiming'] * 1000, 1) . 'ms'; ?>)
                </span>
            </li>
        <?php else: ?>
            <li title="Database queries">
                <?php echo fa_image_tag('database', ['class' => 'faded_out']); ?>
                <span class="faded_out">No database queries</span>
            </li>
        <?php endif; ?>
        <li onclick="pachno_debug_show_menu_tab('log_messages', $(this));" style="cursor: pointer;">
            <?php echo fa_image_tag('file-alt'); ?>
            <span>Log</span>
        </li>
        <li onclick="setTimeout(function() { $('#debug-bar').removeClass('enabled'); }, 150);" title="Minimize" class="minimizer">
            <?php echo fa_image_tag('arrows-alt-h'); ?>
        </li>
        <li onclick="event.preventDefault(); event.stopPropagation(); setTimeout(function() { $('#debug-bar').toggleClass('minimized');$('#debug-bar').removeClass('enabled'); }, 150);" title="Minimize" class="maximizer">
            <?php echo fa_image_tag('arrows-alt-h'); ?>
        </li>
    </ul>
    <ul id="debug-frames-container">
        <?php include_component('debugger/scope', array('scope_settings' => $pachno_summary['settings'])); ?>
        <?php include_component('debugger/ajaxlogger'); ?>
        <?php include_component('debugger/timings', array('partials' => $pachno_summary['partials'])); ?>
        <?php include_component('debugger/routing', array('routing' => $pachno_summary['routing'])); ?>
        <?php include_component('debugger/db', array('db_summary' => $pachno_summary['db'])); ?>
        <?php include_component('debugger/log', array('log' => $pachno_summary['log'])); ?>
    </ul>
<?php else: ?>
No debug data
<?php endif; ?>
