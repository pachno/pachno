<?php

    use pachno\core\entities\DashboardView;
    use pachno\core\entities\User;
    use pachno\core\framework\Response;

    /**
     * @var DashboardView $view
     * @var User $pachno_user
     * @var Response $pachno_response
     */

?>
<li id="dashboard_container_<?php echo $view->getID(); ?>" data-view-id="<?php echo $view->getID(); ?>" data-preloaded="<?php echo (int) $view->shouldBePreloaded(); ?>" class="dashboard_view_container">
    <div class="dashboard_indicator" style="display: none;"><?php echo image_tag('spinning_16.gif'); ?></div>
    <div class="container_div <?php if ($view->isTransparent()) echo 'transparent'; ?>">
        <?php if ($view->hasTitle()): ?>
            <div class="header">
                <?php echo image_tag('icon_delete.png', array('class' => 'remover', 'onclick' => "Pachno.Main.Dashboard.removeView('click', this);")); ?>
                <?php echo image_tag('icon_arrows_move.png', array('class' => 'mover dashboardhandle')); ?>
                <span><?= $view->getTitle(); ?></span>
                <?php if ($view->hasHeaderButton() || $view->hasRSS()): ?>
                    <span class="button-container">
                        <?php if ($view->hasHeaderButton()): ?>
                            <?= $view->getHeaderButton(); ?>
                        <?php endif; ?>
                        <?php if ($view->hasRSS()): ?>
                            <?php echo link_tag($view->getRSSUrl(), fa_image_tag('rss-square', ['class' => 'rss-icon']), array('title' => __('Download feed'), 'class' => 'button secondary icon')); ?>
                            <?php $pachno_response->addFeed($view->getRSSUrl(), $view->getTitle()); ?>
                        <?php endif; ?>
                    </span>
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
