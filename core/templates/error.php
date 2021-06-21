<!DOCTYPE html>
<html>
    <head>
        <style>
            @import url('https://fonts.googleapis.com/css?family=DM+Sans:700|Fira+Sans:300,300i,400,400i,600,600i|Fira+Mono:400,500,700&subset=cyrillic,cyrillic-ext,latin-ext&display=swap');

            body, td, th {
                padding: 0;
                margin: 0;
                background-color: #FFF;
                font-family: 'Fira Sans', 'Open Sans', sans-serif;
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
                font-family: 'DM Sans', sans-serif;
                margin: 0;
                padding: 10px;
                background: #00adc7;
                color: #FFF;
                font-weight: 700;
                font-size: 1.7rem;
                letter-spacing: -1px;
                border-radius: 2px 2px 0 0;
            }
            h1 span, h2 span, h3 span {
                flex: 1 1 auto;
                line-height: 1em;
            }
            .image-container {
                flex: 0 0 36px;
                padding: 0;
                margin-right: 7px;
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
                font-size: 1.2rem;
                margin-top: .8rem;
            }
            a { color: #00adc7; font-weight: 400; display: inline-block; padding: 1px; text-decoration: underline; }
            input[type="text"], input[type="password"] { float: left; margin-right: 15px; }
            label { float: left; font-weight: 600; margin-right: 5px; display: block; width: 150px; }
            label span { font-weight: normal; color: #888; }
            .rounded_box {
                background: transparent;
                margin: 0;
                border-radius: 2px;
                border: 1px solid rgba(0, 0, 0, 0.1);
                box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
                padding: 0;
            }
            .rounded_box h4 { margin-bottom: 0; margin-top: 7px; font-size: 14px; }
            .error_content { padding: 10px; }
            .description { padding: 3px 3px 3px 0;}
            pre{white-space:pre-wrap;padding:0.85em;background:#f8f8f8;font-size: 0.9em; font-family: 'Fira Mono', monospace;}
            .command_box { border: 1px dashed #DDD; background-color: #F5F5F5; padding: 4px; font-family: 'Fira Mono', monospace; font-size: 0.9em; display: inline-block; margin: 0 5px; }
            .stacktrace { font-family: 'Fira Mono', monospace; font-size: 0.9em; }
            .filename { color: #55F; font-size: 0.9em; font-family: 'Fira Mono', monospace; }
            .exception-message { font-size: 1em; font-family: 'Fira Mono', monospace; }
        </style>
    </head>
    <body>
        <div class="rounded_box" style="margin: 30px auto 0 auto; width: 700px;">
            <h1 class="logo"><span class="image-container"><img src="/logo_white_192.png"></span><span>Pachno</span></h1>
            <div class="error_content">
                <h2><span>The following error occurred:</span></h2>
                <?php if (isset($exception)): ?>
                    <h3><?= nl2br($exception->getMessage()); ?></h3>
                    <?php if ($exception instanceof \pachno\core\framework\exceptions\ActionNotFoundException): ?>
                        <h4>Could not find the specified action</h4>
                    <?php elseif ($exception instanceof \pachno\core\framework\exceptions\TemplateNotFoundException): ?>
                        <h4>Could not find the template file for the specified action</h4>
                    <?php elseif ($exception instanceof \pachno\core\framework\exceptions\ConfigurationException): ?>
                        <?php if ($exception->getCode() == \pachno\core\framework\exceptions\ConfigurationException::NO_VERSION_INFO): ?>
                            The version information file <span class="command_box"><?= PACHNO_PATH; ?>installed</span> is present, but file is empty.<br>
                            This file is generated during installation, so this error should not occur.<br>
                            <br>
                            Please reinstall Pachno or file a bug report if you think this is an error.
                        <?php elseif ($exception->getCode() == \pachno\core\framework\exceptions\ConfigurationException::UPGRADE_FILE_MISSING): ?>
                            To enable the upgrade mode, make sure the file <span class="command_box"><?= PACHNO_PATH; ?>upgrade</span> is present<br>
                            Please see the <a href='https://projects.pach.no/pachno/docs/r/upgrade'>upgrade instructions</a> for more information.
                        <?php elseif ($exception->getCode() == \pachno\core\framework\exceptions\ConfigurationException::UPGRADE_REQUIRED): ?>
                            You need to upgrade to this version of Pachno before you can continue.<br>
                            Please see the <a href='https://projects.pach.no/pachno/docs/r/upgrade'>upgrade instructions</a> for more information.
                        <?php elseif ($exception->getCode() == \pachno\core\framework\exceptions\ConfigurationException::NO_B2DB_CONFIGURATION): ?>
                            The database configuration file <span class="command_box"><?= PACHNO_CONFIGURATION_PATH; ?>b2db.yml</span> could not be read.<br>
                            This file is generated during installation, so this error should not occur.<br>
                            <br>
                            Please reinstall Pachno or file a bug report if you think this is an error.
                        <?php else: ?>
                            There is an issue with the configuration. Please see the message above.
                        <?php endif; ?>
                        <br>
                        <br>
                    <?php elseif ($exception instanceof \pachno\core\framework\exceptions\CacheException): ?>
                        <p>
                            <?php if ($exception->getCode() == \pachno\core\framework\exceptions\CacheException::NO_FOLDER): ?>
                                The cache folder <span class="command_box"><?= PACHNO_CACHE_PATH; ?></span> does not exist.<br>
                                Make sure the folder exists and is writable by your web server, then try again.<br>
                                <br>
                                Running the following command may resolve this issue:<div class="command_box">mkdir -p <?= PACHNO_CACHE_PATH; ?></div>
                            <?php elseif ($exception->getCode() == \pachno\core\framework\exceptions\CacheException::NOT_WRITABLE): ?>
                                Trying to write to the cache folder <span class="command_box"><?= PACHNO_CACHE_PATH; ?></span> failed.<br>
                                Make sure the folder is writable by your web server, then try again.<br>
                                <br>
                                Running the following command may resolve this issue:<br>
                                <div class="command_box">chown &lt;web_server_user&gt;:&lt;web_server_user&gt; <?= PACHNO_CACHE_PATH; ?></div> (where <span class="command_box">&lt;web_server_user&gt;</span> is the user and group your web server is running at).
                            <?php else: ?>
                                A caching error occured.
                            <?php endif; ?>
                        </p>
                    <?php elseif ($exception instanceof \b2db\Exception): ?>
                        <h4>An exception was thrown in the B2DB framework</h4>
                    <?php else: ?>
                        <h4>An unhandled exception occurred:</h4>
                    <?php endif; ?>
                    <?php if (class_exists('\pachno\core\framework\Context') && \pachno\core\framework\Context::isDebugMode()): ?>
                        <span class="filename"><?= $exception->getFile(); ?></span>, line <b><?= $exception->getLine(); ?></b>:<br>
                        <span class="exception-message"><?= $exception->getMessage(); ?></span>
                    <?php endif; ?>
                <?php else: ?>
                    <h3><?= nl2br($error); ?></h3>
                    <?php if ($code == 8): ?>
                        <h4>The following notice has stopped further execution:</h4>
                    <?php else: ?>
                        <h4>The following error occured:</h4>
                    <?php endif; ?>
                    <span class="exception-message"><?= $error; ?></span> in <span class="filename"><?= $file; ?></span>, line <?= $line; ?>
                <?php endif; ?>
                <br>
                <?php if (isset($exception) && $exception instanceof \b2db\Exception): ?>
                    <h4>SQL:</h4>
                    <?= $exception->getSQL(); ?>
                <?php endif; ?>
                <?php if (class_exists('\pachno\core\framework\Context') && \pachno\core\framework\Context::isDebugMode() && (!isset($exception) || (!$exception instanceof \pachno\core\framework\exceptions\ComposerException && !$exception instanceof \pachno\core\framework\exceptions\CacheException))): ?>
                    <h4>Stack trace:</h4>
                    <ul class="stacktrace">
                        <?php $trace = (isset($exception)) ? $exception->getTrace() : debug_backtrace(); ?>
                        <?php foreach ($trace as $trace_element): ?>
                            <?php if (array_key_exists('class', $trace_element) && $trace_element['class'] == 'pachno\core\framework\Context' && array_key_exists('function', $trace_element) && $trace_element['function'] == 'errorHandler') continue; ?>
                            <li>
                            <?php if (array_key_exists('class', $trace_element)): ?>
                                <strong><?= $trace_element['class'].$trace_element['type'].$trace_element['function']; ?>()</strong>
                            <?php elseif (array_key_exists('function', $trace_element)): ?>
                                <strong><?= $trace_element['function']; ?>()</strong>
                            <?php else: ?>
                                <strong>unknown function</strong>
                            <?php endif; ?>
                            <br>
                            <?php if (array_key_exists('file', $trace_element)): ?>
                                <span class="filename"><?= $trace_element['file']; ?></span>, line <?= $trace_element['line']; ?>
                            <?php else: ?>
                                <span style="color: #C95;">unknown file</span>
                            <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <h4>Watched variables</h4>
                    <?php foreach (\pachno\core\framework\Context::getDebugger()->getWatchedVariables() as $key => $value): ?>
                        <strong>$<?= $key; ?>:</strong>
                        <pre><?= print_r($value, true); ?></pre>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if (class_exists('\pachno\core\framework\Context') && class_exists('\pachno\core\framework\Logging') && \pachno\core\framework\Context::isDebugMode() && (!isset($exception) || (!$exception instanceof \pachno\core\framework\exceptions\ComposerException && !$exception instanceof \pachno\core\framework\exceptions\CacheException))): ?>
                    <h4>Log messages:</h4>
                    <?php foreach (\pachno\core\framework\Logging::getEntries() as $entry): ?>
                        <?php $color = \pachno\core\framework\Logging::getCategoryColor($entry['category']); ?>
                        <?php $lname = \pachno\core\framework\Logging::getLevelName($entry['level']); ?>
                        <div class="log_<?= $entry['category']; ?>"><strong style="font-size: 0.9em; font-family: 'Fira Mono', monospace;"><?= $lname; ?></strong> <strong style="color: #<?= $color; ?>; font-size: 0.9em; font-family: 'Fira Mono', monospace;">[<?= $entry['category']; ?>]</strong> <span style="color: #555; font-size: 0.8em; font-family: 'Fira Mono', monospace;"><?= $entry['time']; ?></span>&nbsp;&nbsp;<?= $entry['message']; ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if (class_exists('\b2db\Core') && \pachno\core\framework\Context::isDebugMode() && (!isset($exception) || !$exception instanceof \pachno\core\framework\exceptions\ComposerException)): ?>
                    <?php if (count(\b2db\Core::getSQLHits())): ?>
                        <h4>SQL queries:</h4>
                        <ol>
                        <?php foreach (\b2db\Core::getSQLHits() as $details): ?>
                            <li>
                                <span class="faded_out dark small"><b>[<?= ($details['time'] >= 1) ? round($details['time'], 2) . ' seconds' : round($details['time'] * 1000, 1) . 'ms'; ?>]</b></span>
                                from <b><?= $details['filename']; ?>, line <?= $details['line']; ?></b>:<br>
                                <span style="font-size: 12px;"><?= $details['sql']; ?></span>
                            </li>
                        <?php endforeach; ?>
                        </ol>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </body>
</html>
