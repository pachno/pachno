<?php

    use pachno\core\entities\Milestone;

    /** @var Milestone $milestone */

    $savebuttonlabel = (isset($savebuttonlabel)) ? $savebuttonlabel : __('Save milestone');
    $milestonenamelabel = (isset($milestonenamelabel)) ? $milestonenamelabel : __('Milestone name');
    $milestoneplaceholder = (isset($milestoneplaceholder)) ? $milestoneplaceholder : __('Enter a milestone name');
    if (!isset($milestoneheader)) {
        $milestoneheader = ($milestone->getId()) ? __('Edit milestone details') : __('Add milestone');
    }
    $milestone_type = (isset($milestone_type)) ? $milestone_type : $milestone->getType();
    $milestoneincludeissues_text = (isset($milestoneincludeissues_text)) ? $milestoneincludeissues_text : __('The %number selected issue(s) will be automatically assigned to the new milestone', array('%number' => '<span id="milestone_include_num_issues"></span>'));
    $action_url = (isset($action_url)) ? $action_url : make_url('project_milestone', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => (int) $milestone->getID()));

?>
<div class="backdrop_box large" id="edit_milestone_container">
    <div class="backdrop_detail_header">
        <span><?= $milestoneheader; ?></span>
        <a href="javascript:void(0);" class="closer" onclick="Pachno.UI.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content edit_milestone">
        <div class="form-container">
            <?php if (!isset($includeform) || $includeform): ?>
            <form accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= $action_url; ?>" method="post" id="edit_milestone_form" data-simple-submit data-auto-close>
            <?php endif; ?>
                <div class="form-row">
                    <input type="text" class="name-input-enhance" value="<?= $milestone->getName(); ?>" name="name" id="milestone_name_<?= $milestone->getID(); ?>" placeholder="<?= $milestoneplaceholder; ?>">
                    <label for="milestone_name_<?= $milestone->getID(); ?>"><?= $milestonenamelabel; ?></label>
                </div>
                <?php if ($milestone->getId()): ?>
                    <div class="form-row">
                        <input type="text" class="milestone_input_description secondary" value="<?= $milestone->getDescription(); ?>" name="description" id="milestone_description_<?= $milestone->getID(); ?>">
                        <label for="milestone_description_<?= $milestone->getID(); ?>"><?= __('Description'); ?></label>
                    </div>
                    <div class="form-row">
                        <input type="checkbox" class="fancy-checkbox" name="visibility_roadmap" value="1" id="milestone_visibility_roadmap_<?= $milestone->getID(); ?>" <?php if ($milestone->isVisibleRoadmap()) echo 'checked'; ?>>
                        <label for="milestone_visibility_roadmap_<?= $milestone->getID(); ?>">
                            <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                            <span><?= __('Visible in project roadmap'); ?></span>
                        </label>
                    </div>
                    <div class="form-row">
                        <input type="checkbox" class="fancy-checkbox" name="visibility_issues" value="1" id="milestone_visibility_issues_<?= $milestone->getID(); ?>" <?php if ($milestone->isVisibleIssues()) echo 'checked'; ?>>
                        <label for="milestone_visibility_issues_<?= $milestone->getID(); ?>">
                            <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                            <span><?= __('Issues can be assigned to this milestone'); ?></span>
                        </label>
                    </div>
                    <div class="form-row">
                        <div class="fancy-dropdown-container">
                            <div class="fancy-dropdown">
                                <label><?= __('Percentage type'); ?></label>
                                <span class="value"></span>
                                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                <div class="dropdown-container list-mode">
                                    <?php foreach(Milestone::getPercentageTypes() as $percentage_type_key => $percentage_type_text): ?>
                                        <input type="radio" class="fancy-checkbox" name="percentage_type" id="milestone_percentage_type_<?= $milestone->getID(); ?>_<?= $percentage_type_key; ?>" value="<?= $percentage_type_key; ?>" <?php if ($milestone->getPercentageType() == $percentage_type_key) echo 'checked'; ?>>
                                        <label for="milestone_percentage_type_<?= $milestone->getID(); ?>_<?= $percentage_type_key; ?>" class="list-item">
                                            <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                                            <span class="name value"><?= $percentage_type_text; ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="row">
                    <div class="column">
                        <div class="form-row">
                            <input type="checkbox" class="fancy-checkbox milestone-edit-date-toggle" value="1" name="is_starting" id="starting_date_<?= $milestone->getID(); ?>" <?php if ($milestone->isStarting()) echo 'checked'; ?>>
                            <label for="starting_date_<?= $milestone->getID(); ?>"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Planned start date'); ?></label>
                        </div>
                        <div class="form-row">
                            <input type="checkbox" class="fancy-checkbox milestone-edit-date-toggle" value="1" name="is_scheduled" id="end_date_<?= $milestone->getID(); ?>" <?php if ($milestone->isScheduled()) echo 'checked'; ?>>
                            <label for="end_date_<?= $milestone->getID(); ?>"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Planned end date'); ?></label>
                        </div>
                    </div>
                    <div class="column">
                        <div class="form-row">
                            <input type="hidden" id="edit_milestone_date_container" class="auto-calendar">
                        </div>
                    </div>
                </div>
                <div id="milestone_include_issues" class="form-row milestone_include_issues" style="display: none;">
                    <?= $milestoneincludeissues_text; ?>
                    <input id="include_selected_issues" value="0" name="include_selected_issues" type="hidden">
                </div>
                <?php if (isset($milestone_type)): ?>
                    <input id="milestone_type" value="<?= $milestone_type; ?>" name="milestone_type" type="hidden">
                <?php endif; ?>
                <?php if ($milestone->getID()): ?>
                    <input type="hidden" name="milestone_id" value="<?= $milestone->getID(); ?>">
                <?php endif; ?>
                <div class="form-row submit-container">
                    <button class="button primary" type="submit">
                        <?= fa_image_tag('spinner', ['class' => 'indicator fa-spin']); ?>
                        <span><?= $savebuttonlabel; ?></span>
                    </button>
                </div>
                <?php if (!isset($includeform) || $includeform): ?>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('body').off('click', '.milestone-edit-date-toggle');
    $('body').on('click', '.milestone-edit-date-toggle', function () {
        let datepickerInstance = Pachno.UI.calendars['edit_milestone_date_container'];
        if ($('.milestone-edit-date-toggle:checked').length == 2) {
            datepickerInstance.update({ range: true });
        } else {
            datepickerInstance.update({ range: false });
        }
    });

    $form = $('#edit_milestone_form');
    $form.off('submit');
    $form.on('submit', function (event) {
        $form.addClass('submitting');
        event.preventDefault();
        event.stopPropagation();
        let datepickerInstance = Pachno.UI.calendars['edit_milestone_date_container'];
        let dates = datepickerInstance.selectedDates.map(date => date.getTime() / 1000);

        let options = {
            method: 'POST',
            form: 'edit_milestone_form',
            data: {
                dates
            }
        };
        Pachno.fetch($form.attr('action'), options)
            .then(json => {
                $form.removeClass('submitting');
                Pachno.UI.Backdrop.reset();
                Pachno.trigger(Pachno.EVENTS.formSubmitResponse, { form: $form.attr('id'), json });
            });
    })
</script>
