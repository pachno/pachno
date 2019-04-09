<?php
    /*
     * Generate link for browser
     */
     
    $link_repo = \pachno\core\framework\Context::getModule('vcs_integration')->getSetting('browser_url_' . \pachno\core\framework\Context::getCurrentProject()->getID());

?>
<?php if (\pachno\core\framework\Context::getModule('vcs_integration')->getSetting('vcs_mode_' . \pachno\core\framework\Context::getCurrentProject()->getID()) != \pachno\modules\vcs_integration\Vcs_integration::MODE_DISABLED): ?>
    <a href="<?= make_url('vcs_commitspage', ['project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey()]); ?>" class="list-item <?php if ($pachno_response->getPage() == 'vcs_commitspage') echo 'selected expanded'; ?>">
        <span class="name"><?= __('Commits'); ?></span>
        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
    </a>
    <?php if (!($submenu) && $pachno_response->getPage() == 'vcs_commitspage'): ?>
        <div class="list-mode submenu">
            <a href="<?php echo $link_repo; ?>" target="_blank" class="list-item"><?php echo __('Browse source code'); ?></a>
        </div>
    <?php endif; ?>
<?php endif; ?>
