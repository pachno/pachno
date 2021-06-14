<?php

    use pachno\core\entities\Build;
    use pachno\core\entities\Component;
    use pachno\core\entities\Edition;
    use pachno\core\entities\Issue;

    /**
     * @var Issue $issue
     * @var Build[] $builds
     * @var Component[] $components
     * @var Edition[] $editions
     */

?>
<div class="backdrop_box medium" id="viewissue_add_item_div">
    <div class="backdrop_detail_header">
        <span><?= __('Add affected item'); ?></span>
        <a href="javascript:void(0);" class="closer"><?= fa_image_tag('times'); ?></a>
    </div>
    <div class="backdrop_detail_content">
        <div class="form-container">
            <form id="viewissue_add_item_form" method="post" action="<?= make_url('add_affected', ['project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID()]); ?>" method="post" accept-charset="<?= \pachno\core\framework\Settings::getCharset(); ?>" data-simple-submit data-auto-close data-update-container="#affected_list" data-update-insert>
                <div class="form-row">
                    <div class="helper-text">
                        <?= __('Please select the type and item you wish to add as affected by this issue.'); ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="fancy-dropdown-container">
                        <div class="fancy-dropdown">
                            <label><?= __('Item type'); ?></label>
                            <span class="value"></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container list-mode">
                                <?php if ($issue->getProject()->isEditionsEnabled() && $issue->canEditAffectedEditions() && isset($editions) && count($editions)): ?>
                                    <input class="fancy-checkbox" type="radio" name="item_type" id="item_type_edition" value="edition" onclick="$('#which_item_edition').removeClass('hidden'); $('#which_item_component').addClass('hidden'); $('#which_item_build').addClass('hidden');" />
                                    <label for="item_type_edition" class="list-item">
                                        <span class="name value"><?= __('Edition'); ?></span>
                                    </label>
                                <?php endif; ?>
                                <?php if ($issue->getProject()->isComponentsEnabled() && $issue->canEditAffectedComponents() && isset($components) && count($components)): ?>
                                    <input class="fancy-checkbox" type="radio" name="item_type" id="item_type_component" value="component" onclick="$('#which_item_edition').addClass('hidden'); $('#which_item_component').removeClass('hidden'); $('#which_item_build').addClass('hidden');" />
                                    <label for="item_type_component" class="list-item">
                                        <span class="name value"><?= __('Component'); ?></span>
                                    </label>
                                <?php endif; ?>
                                <?php if ($issue->getProject()->isBuildsEnabled() && $issue->canEditAffectedBuilds() && isset($builds) && count($builds)): ?>
                                    <input class="fancy-checkbox" type="radio" name="item_type" id="item_type_build" value="build" onclick="$('#which_item_edition').addClass('hidden'); $('#which_item_component').addClass('hidden'); $('#which_item_build').removeClass('hidden');" />
                                    <label for="item_type_build" class="list-item">
                                        <span class="name value"><?= __('Release'); ?></span>
                                    </label>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ($issue->getProject()->isEditionsEnabled() && $issue->canEditAffectedEditions()): ?>
                    <div class="form-row hidden" id="which_item_edition">
                        <div class="fancy-dropdown-container">
                            <div class="fancy-dropdown">
                                <label><?= __('Edition'); ?></label>
                                <span class="value"></span>
                                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                <div class="dropdown-container list-mode">
                                    <?php foreach ($editions as $edition): ?>
                                        <input type="radio" id="which_item_edition_<?= $edition->getID(); ?>" name="which_item_edition" class="fancy-checkbox" value="<?= $edition->getID(); ?>">
                                        <label for="which_item_edition_<?= $edition->getID(); ?>" class="list-item">
                                            <span class="name value"><?= $edition->getName(); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($issue->getProject()->isComponentsEnabled() && $issue->canEditAffectedComponents()): ?>
                    <div class="form-row hidden" id="which_item_component">
                        <div class="fancy-dropdown-container">
                            <div class="fancy-dropdown">
                                <label><?= __('Component'); ?></label>
                                <span class="value"></span>
                                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                <div class="dropdown-container list-mode">
                                    <?php foreach ($components as $component): ?>
                                        <input type="radio" id="which_item_component_<?= $component->getID(); ?>" name="which_item_component" class="fancy-checkbox" value="<?= $component->getID(); ?>">
                                        <label for="which_item_component_<?= $component->getID(); ?>" class="list-item">
                                            <span class="name value"><?= $component->getName(); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($issue->getProject()->isBuildsEnabled() && $issue->canEditAffectedBuilds()): ?>
                    <div class="form-row hidden" id="which_item_build">
                        <div class="fancy-dropdown-container">
                            <div class="fancy-dropdown">
                                <label><?= __('Release'); ?></label>
                                <span class="value"></span>
                                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                <div class="dropdown-container list-mode">
                                    <?php foreach ($builds as $build): ?>
                                        <input type="radio" id="which_item_build_<?= $build->getID(); ?>" name="which_item_build" class="fancy-checkbox" value="<?= $build->getID(); ?>">
                                        <label for="which_item_build_<?= $build->getID(); ?>" class="list-item">
                                            <span class="name value"><?= $build->getName(); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($issue->getProject()->isBuildsEnabled() || $issue->getProject()->isComponentsEnabled() || $issue->getProject()->isEditionsEnabled()): ?>
                    <div class="form-row submit-container">
                        <button type="submit" class="button primary">
                            <?= fa_image_tag('plus-square', ['class' => 'icon']); ?>
                            <span><?php echo __('Add affected item'); ?></span>
                            <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                        </button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>
