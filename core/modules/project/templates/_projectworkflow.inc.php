<div class="backdrop_box large">
    <div id="change_workflow_box">
        <div class="backdrop_detail_header">
            <span><?php echo __('Change workflow'); ?></span>
            <a href="javascript:void(0);" class="closer" onclick="Pachno.Main.Helpers.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
        </div>
        <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" onsubmit="Pachno.Project.workflowtable('<?php echo make_url('configure_projects_workflow_table', array('project_id' => $project->getID())); ?>', <?php echo $project->getID(); ?>);return false;" action="<?php echo make_url('configure_projects_workflow_table', array('project_id' => $project->getID())); ?>" method="post" id="workflow_form" enctype="multipart/form-data">
            <div id="backdrop_detail_content" class="backdrop_detail_content">
                <h3><?php echo __('New workflow scheme:'); ?><select name="new_workflow">
                    <?php foreach (\pachno\core\entities\WorkflowScheme::getAll() as $scheme): ?>
                        <?php if ($scheme == $project->getWorkflowScheme()): continue; endif; ?>
                        <option value="<?php echo $scheme->getID(); ?>"><?php echo $scheme->getName(); ?></option>
                    <?php endforeach; ?>
                    <?php if (count(\pachno\core\entities\WorkflowScheme::getAll()) < 2): ?>
                        <option disabled="disabled" value="0"><?php echo __('No other workflows'); ?></option>
                    <?php endif; ?>
                </select></h3>
            </div>
            <div class="backdrop_details_submit">
                <div class="submit_container">
                    <button class="button" type="submit"><?php echo image_tag('spinning_16.gif', ['id' => 'change_workflow_indicator', 'style' => 'display: none;']) . __('Continue'); ?></button>
                </div>
            </div>
        </form>
    </div>
    <div id="change_workflow_table" style="display: none;"></div>
</div>
