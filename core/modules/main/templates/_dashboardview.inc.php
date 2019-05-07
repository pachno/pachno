<?php

    use pachno\core\entities\DashboardView;
    /** @var DashboardView $view */

?>
<li id="dashboard_container_<?php echo $view->getID(); ?>" data-view-id="<?php echo $view->getID(); ?>" data-preloaded="<?php echo (int) $view->shouldBePreloaded(); ?>" class="dashboard_view_container">
    <div class="dashboard_indicator" style="display: none;"><?php echo image_tag('spinning_16.gif'); ?></div>
    <div class="container_div <?php if ($view->isTransparent()) echo 'transparent'; ?>">
        <?php if ($view->hasTitle()): ?>
            <div class="header">
                <?php echo image_tag('icon_delete.png', array('class' => 'remover', 'onclick' => "Pachno.Main.Dashboard.removeView('click', this);")); ?>
                <?php echo image_tag('icon_arrows_move.png', array('class' => 'mover dashboardhandle')); ?>
                <?php if ($view->hasHeader()): ?>
                    <?php echo $view->getHeader(); ?>
                <?php else: ?>
                    <span><?= $view->getTitle(); ?></span>
                <?php endif; ?>
                <?php if ($view->hasRSS()): ?>
                    <?php echo link_tag($view->getRSSUrl(), fa_image_tag('rss-square', ['class' => 'rss-icon']), array('title' => __('Download feed'), 'class' => 'image')); ?>
                    <?php $pachno_response->addFeed($view->getRSSUrl(), $view->getTitle()); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div id="dashboard_view_<?php echo $view->getID(); ?>" class="<?php if ($view->getTargetType() == DashboardView::TYPE_PROJECT): ?>dashboard_view_content<?php endif; ?>">
            <?php if ($view->shouldBePreloaded()): ?>
                <?php include_component($view->getTemplate(), array('view' => $view)); ?>
            <?php endif; ?>
        </div>
        <?php if (!$view->shouldBePreloaded()): ?>
            <div style="text-align: center; padding: 20px 0;" id="dashboard_view_<?php echo $view->getID(); ?>_indicator">
                <?php echo image_tag('spinning_26.gif'); ?>
            </div>
        <?php endif; ?>
    </div>
</li>
