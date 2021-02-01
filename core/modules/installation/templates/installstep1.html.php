<?php include_component('installation/header'); ?>
<div class="installation_box">
    <h2 style="margin-top: 0px;">Pre-installation checks</h2>
    <p style="margin-bottom: 10px;">
    <?php if ($all_well): ?>
        We've just done a requirements check. All prerequisite requirements are met, so you're good to continue.
    <?php else: ?>
        Before we can start the installation the requirements below need to be satisfied.<br>
        Please look through the list, and take the necessary steps to correct any errors that may have been highlighted.</p>
    <?php endif; ?>
    <div id="installation_main_box">
        <div class="prereq type-ok"><?= fa_image_tag('check'); ?><span class="message">Mozilla Public License 2.0 accepted</span></div>
        <?php if ($php_ok): ?>
            <div class="prereq type-ok"><?= fa_image_tag('check'); ?><span class="message">PHP version (<?php echo $php_ver; ?>) meets requirements</span></div>
        <?php else: ?>
            <div class="prereq type-warn">
                <?= fa_image_tag('exclamation-triangle'); ?>
                <span class="message">
                    <b>PHP interpreter version is too old</b><br>
                    Pachno requires PHP 7.1.0 or later. You have version <?php echo $php_ver; ?>. Grab the latest release from your usual sources or from <a href="http://php.net/downloads.php" target="_blank">php.net</a>
                </span>
            </div>
        <?php endif; ?>
        <?php if ($pcre_ok): ?>
            <div class="prereq type-ok"><?= fa_image_tag('check'); ?><span class="message">PCRE libraries version (<?php echo $pcre_ver; ?>) meets requirements</span></div>
        <?php else: ?>
            <div class="prereq type-warn">
                <?= fa_image_tag('exclamation-triangle'); ?>
                <span class="message">
                    <b>PCRE libraries version is too old</b><br>
                    Pachno requires PCRE libraries 8.0 or later. You have version <?php echo $pcre_ver; ?>. Update your system to the latest release from your usual sources.
                </span>
            </div>
        <?php endif; ?>
        <?php if ($docblock_ok): ?>
            <div class="prereq type-ok"><?= fa_image_tag('check'); ?><span class="message">PHP docblocks are readable</span></div>
        <?php else: ?>
            <div class="prereq type-fail">
                <?= fa_image_tag('times'); ?>
                <span class="message">
                    <b>PHP docblocks are not readable</b><br>
                    Pachno requires that PHP docblocks are readable. You may be running a PHP accellerator that removes docblocks from PHP code files as an optimization technique. Please refer to the accelerator documentation for how to disable this feature, or disable the accellerator.</a>
                </span>
            </div>
        <?php endif; ?>
        <?php if ($base_folder_perm_ok): ?>
            <div class="prereq type-ok"><?= fa_image_tag('check'); ?><span class="message">Can write to Pachno directory</span></div>
        <?php else: ?>
            <div class="prereq type-fail with-solution">
                <?= fa_image_tag('times'); ?>
                <span class="message">
                    <b>Could not write to Pachno directory</b><br>
                    The main folder for Pachno should be writable during installation, since we need to store some information in it
                </span>
            </div>
            <div class="message-box type-solution">
                <b>If you're installing this on a Linux server,</b> running this command should fix it:
                <div class="command_box">
                    chmod a+w <?php echo PACHNO_PATH; ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($pachno_folder_perm_ok): ?>
            <div class="prereq type-ok"><?= fa_image_tag('check'); ?><span class="message">Can write to Pachno public directory</span></div>
        <?php else: ?>
            <div class="prereq type-fail with-solution">
                <?= fa_image_tag('times'); ?>
                <span class="message">
                    <b>Could not write to Pachno public directory</b><br>
                    The public folder for Pachno should be writable during installation, since we need to store some information in it
                </span>
            </div>
            <div class="message-box type-solution">
                <b>If you're installing this on a Linux server,</b> running this command should fix it:
                <div class="command_box">
                    chmod a+w <?php echo PACHNO_PATH . PACHNO_PUBLIC_FOLDER_NAME; ?>/
                </div>
            </div>
        <?php endif; ?>

        <?php if ($mb_ok): ?>
            <div class="prereq type-ok"><?= fa_image_tag('check'); ?><span class="message">PHP "mbstring" extension is loaded</span></div>
        <?php else: ?>
            <div class="prereq type-warn">
                <?= fa_image_tag('exclamation-triangle'); ?>
                <span class="message">
                    <b>PHP extension "mbstring" is not loaded</b><br>
                    Pachno requires the PHP extension "mbstring". Please install and / or enable this extension to continue.
                </span>
            </div>
        <?php endif; ?>

        <?php if ($dom_ok): ?>
            <div class="prereq type-ok"><?= fa_image_tag('check'); ?><span class="message">PHP "xml" extension is loaded</span></div>
        <?php else: ?>
            <div class="prereq type-fail">
                <?= fa_image_tag('times'); ?>
                <span class="message">
                    <b>PHP extension "xml" is not loaded</b><br>
                    Pachno requires the PHP extension "xml". Please install and / or enable this extension to continue.
                </span>
            </div>
        <?php endif; ?>

        <?php if ($gd_ok): ?>
            <div class="prereq type-ok"><?= fa_image_tag('check'); ?><span class="message">PHP "gd" extension is loaded</span></div>
        <?php else: ?>
            <div class="prereq type-warn">
                <?= fa_image_tag('exclamation-triangle'); ?>
                <span class="message">
                    <b>PHP extension "gd" is not loaded</b><br>
                    You won't be able to display graphs statistics and some other images.
                </span>
            </div>
        <?php endif; ?>
        <?php if ($pdo_ok): ?>
            <div class="prereq type-ok"><?= fa_image_tag('check'); ?><span class="message">PHP "pdo" extension is loaded</span></div>
        <?php endif; ?>
        <?php if (!$mysql_ok && !$pgsql_ok): ?>
            <div class="prereq type-fail">
                <?= fa_image_tag('times'); ?>
                <span class="message">
                    <b>No PDO driver enabled</b><br>
                    To install Pachno, a PDO database driver must be installed and enabled. Please install and / or enable a supported pdo extension to continue.
                </span>
            </div>
        <?php else: ?>
            <?php if ($mysql_ok): ?>
                <div class="prereq type-ok"><?= fa_image_tag('check'); ?><span class="message">PHP "pdo-mysql" extension is loaded</span></div>
            <?php elseif (!$mysql_ok && $pgsql_ok): ?>
                <div class="prereq type-warn">
                    <?= fa_image_tag('info-circle'); ?>
                    <span class="message">
                        <b>PDO MySQL driver not enabled</b><br>
                        You can continue the installation, but you won't be able to install Pachno on a MySQL database.
                    </span>
                </div>
            <?php endif; ?>
            <?php if ($pgsql_ok): ?>
                <div class="prereq type-ok"><?= fa_image_tag('check'); ?><span class="message">PHP "pdo-pgsql" extension is loaded</span></div>
            <?php elseif ($mysql_ok && !$pgsql_ok): ?>
                <div class="prereq type-warn">
                    <?= fa_image_tag('info-circle'); ?>
                    <span class="message">
                        <b>PDO PostgreSQL driver not enabled</b><br>
                        You can continue the installation, but you won't be able to install Pachno on a PostgreSQL database.
                    </span>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($b2db_param_file_ok && $b2db_param_folder_ok): ?>
            <div class="prereq type-ok"><?= fa_image_tag('check'); ?><span class="message">Can save database connection details</span></div>
        <?php elseif (!$b2db_param_file_ok): ?>
            <div class="prereq type-fail">
                <?= fa_image_tag('times'); ?>
                <span class="message">
                    <b>Could not write the SQL settings file</b><br>
                    The folder that contains the SQL settings is not writable
                </span>
            </div>
            <div class="message-box type-solution">
                <b>If you're installing this on a Linux server,</b> running those commands should fix it:<br>
                <div class="command_box">
                    touch <?php echo realpath(PACHNO_CONFIGURATION_PATH) . DS; ?>b2db.yml<br>
                    chmod a+w <?php echo realpath(PACHNO_CONFIGURATION_PATH) . DS; ?>b2db.yml
                </div>
            </div>
        <?php else: ?>
            <div class="prereq type-fail">
                <?= fa_image_tag('times'); ?>
                <span class="message">
                    <b>Could not write the SQL settings file</b><br>
                    The file that contains the SQL settings already exists, but is not writable
                </span>
            </div>
            <div class="message-box type-solution">
                <b>If you're installing this on a Linux server,</b> running this command should fix it:<br>
                <div class="command_box">
                    chmod a+w <?php echo realpath(PACHNO_CONFIGURATION_PATH) . DS; ?>b2db.yml
                </div>
            </div>
        <?php endif; ?>
        <?php if ($all_well): ?>
            <form accept-charset="utf-8" action="index.php" method="post" id="installation_form" style="display: flex; width: 100%; align-items: center; justify-content: center; flex-direction: row; margin: 30px 0 20px;">
                <input type="hidden" name="step" value="2">
                <button type="submit" onclick="document.getElementById('start_install').classList.add('disabled');document.getElementById('installation_form').classList.add('submitting');" id="start_install" style="margin-left: auto;">
                    <span class="name"><?= __('Continue'); ?></span>
                    <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                </button>
            </form>
        <?php else: ?>
            <div style="clear: both; padding-top: 20px; text-align: right; ">
                <form accept-charset="utf-8" action="index.php" method="post">
                    <input type="hidden" name="step" value="1">
                    <input type="hidden" name="agree_license" value="1">
                    <label for="retry_button" style="font-size: 13px; margin-right: 10px;">You need to correct the above error(s) before the installation can continue.</label>
                    <input type="submit" id="retry_button" value="Retry">
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include_component('installation/footer'); ?>
