<?php

    include_component('installation/header', array('mode' => 'upgrade'));

?>
<div class="installation_box">
    <?php if ($upgrade_available): ?>
        <?php if (version_compare($current_version, '2.0', '>')): ?>
            <div class="message-box type-error">
                <?= fa_image_tag('exclamation-circle', ['type' => 'icon']); ?>
                <div class="message">
                    Upgrading from The Bug Genie is not supported. To migrate from The Bug Genie to Pachno, run the included migration:
                    <div class="command_box">./bin/pachno migrate</div>
                    from the command line. Migrating via the web interface is not possible.
                </div>
            </div>
        <?php elseif (isset($permissions_ok) && $permissions_ok): ?>
            <div class="fullpage_backdrop" id="upgrading_popup" style="display: none;">
                <div class="backdrop_box">
                    <div class="backdrop_detail_content">
                        <div class="form-container">
                            <div class="form submitting">
                                <div class="form-row header">
                                    <h1>Upgrading, please wait ...</h1>
                                </div>
                                <div class="form-row">
                                    <div class="helper-text">This can take a little while</div>
                                </div>
                                <div class="form-row submit-container">
                                    <?= fa_image_tag('spinner', ['class' => 'icon fa-spin indicator']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <form accept-charset="utf-8" action="<?php echo make_url('upgrade'); ?>" method="post" onsubmit="Pachno.$('#upgrading_popup').show();">
                <?php if (isset($error)): ?>
                    <div class="padded_box installpage backup" id="install_page_error">
                        <div class="rounded_box shadowed padded_box installation_prerequisites prereq_fail" style="padding: 10px; margin-bottom: 10px;">
                            <b>An error occurred during the upgrade:</b><br>
                            <?= $error; ?>
                        </div>
                        <div class="progress_buttons">
                            <a href="javascript:void(0);" class="button button-next">Okay</a>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="installpage" id="install_page_1">
                    <div class="installation_progress">
                        <h5>Upgrade progress</h5>
                        <div class="progress_bar"><div class="filler" style="width: 25%;"></div></div>
                    </div>
                    Pachno is open source software provided <span class="highlight">free of charge</span> by a community of volunteers.<br>
                    If you or your company use Pachno on a regular basis, please consider one of the following ways to contribute:
                    <ul>
                        <li>committing patches, fixes and features <a href="https://github.com/pachno/pachno">via github</a></li>
                        <li>writing and improving the <a href="https://projects.pach.no/pachno/docs/r/home">documentation</a></li>
                        <li>help out other users in our <a href="https://forum.pach.no/">user forums</a></li>
                        <li>improve or add translations <a href="https://github.com/pachno/pachno/tree/master/i18n">via github</a></li>
                        <li>author public blog posts or news articles about Pachno</li>
                    </ul>
                    If you would like to support us but are unable to contribute in any of the ways listed above, please send us an email and we'll work something out: <a href="mailto:contribute@pach.no">contribute@pach.no</a><br>
                    <br>
                    <h5>How to get involved:</h5>
                    <a target="_blank" href="https://forum.pach.no">Pachno forums</a> <i>(opens in a new window)</i><br>
                    <a target="_blank" href="https://pachno.zulipchat.com">Pachno live chat</a> <i>(opens in a new window)</i>
                    <div class="progress_buttons">
                        <a href="javascript:void(0);" class="button button-next">Next</a>
                    </div>
                </div>
                <div class="installpage backup" id="install_page_2">
                    <div class="installation_progress">
                        <h5>Upgrade progress</h5>
                        <div class="progress_bar"><div class="filler" style="width: 50%;"></div></div>
                    </div>
                    <h4 style="margin-bottom: 15px; padding-bottom: 0;">
                        <span style="font-weight: normal;">You are performing the following upgrade: </span><span class="count-badge" style="flex: 0 0 auto;"><?php echo $current_version; ?>.x</span>&nbsp;<?= fa_image_tag('long-arrow-alt-right'); ?><span class="count-badge" style="flex: 0 0 auto;"><?php echo \pachno\core\framework\Settings::getVersion(false, true); ?></span><br>
                    </h4>
                    Although this upgrade process has been thoroughly tested before the release, errors may still occur.<br>
                    Before continuing, you are strongly encouraged to <strong>make sure you have backed up</strong> the following:
                    <ul class="backuplist">
                        <li style="background-image: url('images/backup_database.png');">
                            Pachno database<br>
                            Currently connected to <?php echo b2db\Core::getDriver(); ?> database <span class="command_box"><?php echo b2db\Core::getDatabaseName(); ?></span> running on <span class="command_box"><?php echo b2db\Core::getHostname(); ?></span>, table prefix <span class="command_box"><?php echo b2db\Core::getTablePrefix(); ?></span>
                        </li>
                        <li style="background-image: url('images/backup_uploads.png');" class="<?php if (\pachno\core\framework\Settings::getUploadStorage() != 'files') echo 'faded'; ?>">
                            Uploaded files<br>
                            <?php if (\pachno\core\framework\Settings::getUploadStorage() != 'files'): ?>
                                <span class="smaller">When using database file upload storage, this is included in the database backup</span>
                            <?php else: ?>
                                Remember to keep a copy of all files in <span class="command_box"><?php echo \pachno\core\framework\Settings::getUploadsLocalpath(); ?></span>
                            <?php endif; ?>
                        </li>
                        <li style="background-image: url('images/backup_specialfiles.png');">
                            Pachno special files<br>
                            There are a number of configuration files used by Pachno for its initialization and configuration. You should keep a copy of these files:
                            <ul>
                                <li class="command_box" style="display: block; margin: 0;"><?php echo PACHNO_PATH . 'installed'; ?></li>
                                <li class="command_box" style="display: block; margin: 0;"><?php echo PACHNO_CORE_PATH . 'config/b2db.yml'; ?></li>
                                <li class="command_box" style="display: block; margin: 0;"><?php echo PACHNO_PATH . PACHNO_PUBLIC_FOLDER_NAME . '/.htaccess'; ?></li>
                            </ul>
                        </li>
                    </ul>
                    <div class="progress_buttons">
                        <a href="javascript:void(0);" class="button button-previous">Previous</a>
                        <a href="javascript:void(0);" class="button button-next">Next</a>
                    </div>
                </div>
                <div class="installpage" id="install_page_5">
                    <div class="installation_progress">
                        <h5>Upgrade progress</h5>
                        <div class="progress_bar"><div class="filler" style="width: 75%;"></div></div>
                    </div>
                    <h4>Ready to upgrade</h4>
                    <div class="message-box type-warning">
                        <?= fa_image_tag('exclamation-triangle'); ?>
                        <span class="message">
                            Pressing <b>Perform upgrade</b> on this page will start the upgrade process.
                        </span>
                    </div>
                    Please read the <a target="_blank" href="https://pach.no/releases/latest">release notes</a> and <a target="_blank" href="https://projects.pach.no/pachno/docs/r/upgrade">upgrade notes</a> before you press "Perform upgrade" to continue.<br>
                    <div class="progress_buttons">
                        <input type="hidden" name="perform_upgrade" value="1">
                        <input type="hidden" name="confirm_backup" value="1" id="confirm_backup">
                        <input type="submit" value="Perform upgrade" id="start_upgrade">
                    </div>
                </div>
            </form>
        <?php else: ?>
            <div class="message-box type-warning with-solution">
                <?= fa_image_tag('exclamation-triangle'); ?>
                <span class="message">
                    The upgrade routine needs the following file to be writable: <span class="command_box"><?php echo PACHNO_PATH . 'installed'; ?></span>
                </span>
            </div>
            <div class="message-box type-solution">
                <div class="message">
                    On Linux or Unix systems, you can fix this by running the following command in a console: <br>
                    <div class="command_box" style="font-size: 1em;">chmod a+w <?php echo PACHNO_PATH . 'installed'; ?></div>
                </div>
            </div>
        <?php endif; ?>
    <?php elseif ($upgrade_complete): ?>
        <div class="installation_progress">
            <h5>Upgrade progress</h5>
            <div class="progress_bar"><div class="filler" style="width: 100%;"></div></div>
        </div>
        <h4>Upgrade successfully completed!</h4>
        <p>
            Remember to remove the file <span class="command_box"><?php echo PACHNO_PATH . 'upgrade'; ?></span> before you click the "Finish" button below.
        </p>
        <div class="progress_buttons">
            <a href="<?php echo make_url('auth_logout'); ?>" class="button">Finish</a>
        </div>
    <?php else: ?>
        <h4>No upgrade necessary!</h4>
        <p>
            Make sure that the file <span class="command_box"><?php echo PACHNO_PATH . 'upgrade'; ?></span> is removed before you click the "Finish" button below.
        </p>
        <div class="progress_buttons">
            <a href="<?php echo make_url('home'); ?>" class="button button-next">Finish</a>
        </div>
    <?php endif; ?>
</div>
<script>
    function pachno_upgrade_next(container) {
        container.toggle();
        container.next().toggle();
    }
    function pachno_upgrade_previous(container) {
        container.toggle();
        container.previous().toggle();
    }
    Pachno.on(Pachno.EVENTS.ready, () => {
        Pachno.$('.installpage').hide();
        Pachno.$('.installpage').first().show();

        Pachno.$('body').on('click', '.button-previous', function (event) {
            event.preventDefault();
            event.stopPropagation();
            const $container = Pachno.$(this).parents('.installpage');
            $container.toggle();
            $container.prev().toggle();

            return false;
        });

        Pachno.$('body').on('click', '.button-next', function (event) {
            event.preventDefault();
            event.stopPropagation();
            const $container = Pachno.$(this).parents('.installpage');
            $container.toggle();
            $container.next().toggle();

            return false;
        });
    });

</script>
<?php include_component('installation/footer'); ?>
