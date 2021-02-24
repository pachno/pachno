<nav class="sidebar <?php if (isset($collapsed) && $collapsed) echo 'collapsed'; ?>">
    <div class="list-mode">
    <?php foreach ($config_sections as $category => $config_info): ?>
        <div class="header">
            <span class="name"><?= \pachno\core\framework\Settings::getConfigSectionHeader(pachno\core\framework\Context::getI18n(), $category); ?></span>
            <?php if ($category == \pachno\core\framework\Settings::CONFIGURATION_SECTION_MODULES): ?>
                <a href="<?= make_url('configure_modules'); ?>" class="button icon secondary"><?= fa_image_tag('cog', ['class' => 'icon']); ?></a>
            <?php endif; ?>
        </div>
        <?php foreach ($config_info as $section => $info): ?>
            <?php $is_selected = (bool) (($selected_section == \pachno\core\framework\Settings::CONFIGURATION_SECTION_MODULES && isset($selected_subsection) && array_key_exists('module', $info) && $selected_subsection == $info['module']) || ($selected_section != \pachno\core\framework\Settings::CONFIGURATION_SECTION_MODULES && !isset($selected_subsection) && !array_key_exists('module', $info) && $selected_section == $section)); ?>
            <?php if (is_array($info['route'])): ?>
                <?php $url = make_url($info['route'][0], $info['route'][1]); ?>
            <?php else: ?>
                <?php $url = make_url($info['route']); ?>
            <?php endif;?>
            <?php if (isset($info['disabled']) && $info['disabled']): ?>
                <div class="list-item disabled">
                    <?php if (isset($info['fa_icon'])): ?>
                        <?php $style = (isset($info['fa_color'])) ? 'color: ' . $info['fa_color'] : ''; ?>
                        <?= fa_image_tag($info['fa_icon'], ['style' => $style, 'class' => 'icon'], $info['fa_style']); ?>
                    <?php elseif (isset($info['module']) && $info['module'] != 'core'): ?>
                        <?= image_tag('cfg_icon_'.$info['icon'].'.png', ['class' => 'icon'], false, $info['module']); ?>
                    <?php else: ?>
                        <?= image_tag('cfg_icon_'.$info['icon'].'.png', ['class' => 'icon']); ?>
                    <?php endif; ?>
                    <span class="name"><?= $info['description']; ?></span>
                </div>
            <?php else: ?>
                <a href="<?= $url; ?>" class="list-item<?php if ($is_selected): ?> selected<?php endif; ?>">
                    <?php if (isset($info['fa_icon'])): ?>
                        <?php $style = (isset($info['fa_color'])) ? 'color: ' . $info['fa_color'] : ''; ?>
                        <?= fa_image_tag($info['fa_icon'], ['style' => $style, 'class' => 'icon'], $info['fa_style']); ?>
                    <?php elseif (isset($info['module']) && $info['module'] != 'core'): ?>
                        <?= image_tag('cfg_icon_'.$info['icon'].'.png', ['class' => 'icon'], false, $info['module']); ?>
                    <?php else: ?>
                        <?= image_tag('cfg_icon_'.$info['icon'].'.png', ['class' => 'icon']); ?>
                    <?php endif; ?>
                    <span class="name"><?= $info['description']; ?></span>
                </a>
            <?php endif;?>
        <?php endforeach;?>
    <?php endforeach;?>
    </div>
    <div class="collapser list-mode">
        <a class="list-item" href="javascript:void(0);">
            <span class="icon"><?= fa_image_tag('angle-double-left'); ?></span>
            <span class="name"><?= __('Toggle sidebar'); ?></span>
        </a>
    </div>
</nav>
