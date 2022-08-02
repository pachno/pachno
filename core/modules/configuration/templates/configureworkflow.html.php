<?php

    use \pachno\core\framework\Settings;

    /**
     * @var \pachno\core\entities\Workflow $workflow
     */

    $pachno_response->setTitle(__('Configure issue type scheme %scheme_name', ['%scheme_name' => $workflow->getName()]));

?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => Settings::CONFIGURATION_SECTION_WORKFLOW]); ?>
    <div class="configuration-container">
        <div class="configuration-content centered">
            <div class="form-container">
                <form action="<?= make_url('configure_workflow_post', ['workflow_id' => $workflow->getId()]); ?>" onsubmit="Pachno.Config.Workflows.Workflow.save(this);return false;" data-interactive-form data-interactive-form-method="Pachno.Config.IssuetypeScheme.save">
                    <div class="form-row">
                        <input type="text" name="name" value="<?= $workflow->getName(); ?>" class="invisible title" id="workflow_<?= $workflow->getID(); ?>_name_input">
                        <label for="workflow_<?= $workflow->getID(); ?>_name_input"><?= __('Workflow name'); ?><?= fa_image_tag('spinner', ['class' => 'fa-spin submit-indicator icon']); ?></label>
                    </div>
                    <div class="form-row">
                        <input type="text" name="description" value="<?= $workflow->getDescription(); ?>" class="invisible" id="workflow_<?= $workflow->getID(); ?>_description_input" placeholder="<?= __('Enter an optional workflow description'); ?>">
                        <label for="workflow_<?= $workflow->getID(); ?>_description_input"><?= __('Workflow description'); ?><?= fa_image_tag('spinner', ['class' => 'fa-spin submit-indicator icon']); ?></label>
                    </div>
                    <div class="form-row error-container">
                        <div class="error"></div>
                    </div>
                </form>
            </div>
            <div class="configurable-components-container" id="workflow-steps-container">
                <div class="configurable-components-list-container">
                    <h3><?php echo __('Workflow steps'); ?></h3>
                    <div class="configurable-components-list" id="workflow-steps-list">
                        <?php foreach ($workflow->getSteps() as $step): ?>
                            <?php include_component('configuration/workflowstep', ['step' => $step]); ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="configurable-components-list">
                        <button class="configurable-component trigger-backdrop" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'create_workflow_step', 'workflow_id' => $workflow->getId()]); ?>">
                            <span class="row">
                                <span class="icon"><?= fa_image_tag('plus'); ?></span>
                                <span class="name">
                                    <span class="title"><?= __('Add workflow step'); ?></span>
                                </span>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="configurable-component-options" id="selected-workflow-step-options"></div>
            </div>
        </div>
    </div>
</div>
<script>
  Pachno.on(Pachno.EVENTS.ready, function () {
    Pachno.on(Pachno.EVENTS.configuration.deleteComponent, (_, data) => {
      if (data.type == 'workflow-transition') {
          $('#add-transition-list .list-item[data-add-workflow-transition][data-id=' + data.id + ']').removeClass('disabled');
      }
      if (data.type == 'workflow-transition-action') {
          $('#add-transition-list .list-item[data-add-workflow-transition][data-id=' + data.id + ']').removeClass('disabled');
      }
      if (data.type == 'workflow-validation-rule') {
          $('#add-transition-list .list-item[data-add-workflow-validation-rule][data-id=' + data.id + ']').removeClass('disabled');
      }
    });
    
    $('body').on('click', '#add-transition-list .list-item[data-add-workflow-transition]:not(.disabled)', function(event) {
      const key = $(this).data('id'),
        url = $(this).data('url');

      const $container = $('#outgoing-transitions-list');

      fetch(url, {
        method: 'POST'
      })
        .then(function (response) {
          response.json().then(function (json) {
            if (response.ok) {
              $container.append(json.content);
              $('#add-transition-list .list-item[data-add-workflow-transition][data-id=' + key + ']').addClass('disabled');
            } else {
              Pachno.UI.Message.error(json.error);
            }
          });
        });
    });

    $('body').on('click', '#add-transition-action .list-item[data-add-workflow-transition-action]:not(.disabled)', function(event) {
      const key = $(this).data('id'),
        url = $(this).data('url');

      const $container = $('#workflow-transition-actions-list');

      fetch(url, {
        method: 'POST'
      })
        .then(function (response) {
          response.json().then(function (json) {
            if (response.ok) {
              $container.append(json.content);
              $('#add-transition-list .list-item[data-add-workflow-transition-action][data-id=' + key + ']').addClass('disabled');
            } else {
              Pachno.UI.Message.error(json.error);
            }
          });
        });
    });

    $('body').on('click', '.add-validation-rule-list .list-item[data-add-workflow-validation-rule]:not(.disabled)', function(event) {
      const key = $(this).data('id'),
        url = $(this).data('url');

      const $container = ($(this).data('rule-type') == 'post') ? $('#workflowtransitionpostvalidationrules_list') : $('#workflowtransitionprevalidationrules_list');

      fetch(url, {
        method: 'POST'
      })
        .then(function (response) {
          response.json().then(function (json) {
            if (response.ok) {
              $container.append(json.content);
              $('.add-validation-rule-list .list-item[data-add-workflow-validation-rule][data-id=' + key + ']').addClass('disabled');
            } else {
              Pachno.UI.Message.error(json.error);
            }
          });
        });
    });

    Pachno.on(Pachno.EVENTS.formSubmitResponse, function (PachnoApplication, data) {
      const json = data.json;
      switch (data.form) {
        case 'edit-workflow-transition-0-form':
          const $menu_container = $('#add-transition-list');
          const $container = $('#outgoing-transitions-list');
          
          if ($menu_container.length > 0) {
            $menu_container.append(json.menu_item);
          }
          if ($container.length > 0) {
            $container.append(json.content);
          }
          $('#add-transition-list .list-item[data-add-workflow-transition-action][data-id=' + json.transition.id + ']').addClass('disabled');
          break;
      }
    });
  });
</script>
