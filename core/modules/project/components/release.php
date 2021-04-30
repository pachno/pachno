<?php

    use pachno\core\entities\Build;
    use pachno\core\framework\Context;

    /**
     * @var Build $build
     */

?>
<div class="release-row row <?php if ($build->isActive()) echo 'active'; ?>">
    <div class="column info-icons"><?= fa_image_tag('boxes', ['class' => 'icon']); ?></div>
    <div class="column name-container"><?php echo $build->getName(); ?></div>
    <div class="column"><?php echo $build->getVersion(); ?></div>
    <div class="column">
        <?= ($build->hasReleaseDate()) ? Context::getI18n()->formatTime($build->getReleaseDate(), 14, true, true) : '-'; ?>
    </div>
    <div class="column actions">
        <?php if ($build->hasDownload()): ?>
            <?php echo ($build->hasFile()) ? link_tag(make_url('downloadfile', ['id' => $build->getFile()->getID()]), fa_image_tag('download', ['class' => 'icon']), ['class' => 'button secondary icon']) : link_tag($build->getFileURL(), fa_image_tag('download', ['class' => 'icon']), ['class' => 'button secondary icon']); ?>
        <?php endif; ?>
        <div class="dropper-container">
            <button class="dropper button secondary icon">
                <?= fa_image_tag('ellipsis-v', ['class' => 'icon']); ?>
            </button>
            <div class="dropdown-container">
                <div class="list-mode">
                    <?php if ($build->isActive()): ?>
                        <?php echo link_tag(make_url('project_issues', array('project_key' => Context::getCurrentProject()->getKey(), 'search' => true, 'fs[state]' => array('o' => '=', 'v' => \pachno\core\entities\Issue::STATE_OPEN), 'fs[build]' => array('o' => '=', 'v' => $build->getID())))."?sortfields=issues.posted=desc", __('Issues'), array('class' => 'button', 'title' => __('Show all issues for this release'))); ?>
                        <?php echo javascript_link_tag(__('Report an issue'), array('onclick' => "Pachno.UI.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'reportissue', 'project_id' => $build->getProject()->getId(), 'build_id' => $build->getID()))."');", 'class' => 'button')); ?>
                    <?php else: ?>
                        <?php if ($build->hasDownload()): ?>
                            <?php if (!$build->isReleased()): ?>
                                <div class="button disabled" title="<?php echo __('This release is no longer available for download'); ?>"><?php echo __('Download'); ?></div>
                            <?php else: ?>
                                <?php echo ($build->hasFile()) ? link_tag(make_url('downloadfile', array('id' => $build->getFile()->getID())), image_tag('icon_download.png').__('Download'), array('class' => 'button button-orange')) : link_tag($build->getFileURL(), image_tag('icon_download.png').__('Download'), array('class' => 'button button-orange')); ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
