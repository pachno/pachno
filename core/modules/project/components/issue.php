<?php

    /**
     * @var \pachno\core\entities\Issue $issue
     * @var \pachno\core\entities\User $pachno_user
     */

?>
<div id="issue_<?php echo $issue->getID(); ?>" class="viewissue-container <?php if ($issue->isBlocking()) echo ' blocking'; ?>">
    <?php include_component('project/viewissueheader', ['issue' => $issue]); ?>
    <div id="issue-container" class="issue-card">
        <div id="issue-main-container" class="issue-card-main">
            <?php include_component('project/viewissuemessages', compact('issue')); ?>
            <div class="card-header">
                <?php include_component('project/viewissueworkflowbuttons', ['issue' => $issue]); ?>
                <div class="dropper-container">
                    <button class="dropper button secondary icon" id="more_actions_<?php echo $issue->getID(); ?>_button"><?= fa_image_tag('ellipsis-v'); ?></button>
                    <?php include_component('main/issuemoreactions', array('issue' => $issue, 'times' => false, 'show_workflow_transitions' => false)); ?>
                </div>
            </div>
            <?php \pachno\core\framework\Event::createNew('core', 'viewissue::afterWorkflowButtons', $issue)->trigger(); ?>
            <?php include_component('project/issuedetails', ['issue' => $issue]); ?>
            <?php \pachno\core\framework\Event::createNew('core', 'viewissue::afterMainDetails', $issue)->trigger(); ?>
            <?php /*
                    <div class="fancy-tabs" id="viewissue_activity">
                        <?php \pachno\core\framework\Event::createNew('core', 'viewissue_before_tabs', $issue)->trigger(); ?>
                        <a id="tab_viewissue_history" class="tab" href="javascript:void(0);" onclick="Pachno.UI.tabSwitcher('tab_viewissue_history', 'viewissue_activity');">
                            <?= fa_image_tag('history', ['class' => 'icon']); ?>
                            <span class="name"><?= __('History'); ?></span>
                        </a>
                    </div>
                    <div id="viewissue_activity_panes" class="fancypanes">
                        <?php \pachno\core\framework\Event::createNew('core', 'viewissue_after_tabs', $issue)->trigger(); ?>
                        <div id="tab_viewissue_history_pane" style="display:none;">
                            <div class="viewissue_history">
                                <div id="viewissue_log_items">
                                    <ul>
                                        <?php $previous_time = null; ?>
                                        <?php $include_user = true; ?>
                                        <?php foreach (array_reverse($issue->getLogEntries()) as $item): ?>
                                            <?php if (!$item instanceof \pachno\core\entities\LogItem) continue; ?>
                                            <?php include_component('main/issuelogitem', compact('item', 'previous_time', 'include_user')); ?>
                                            <?php $previous_time = $item->getTime(); ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div> */ ?>
            <?php include_component('project/issuecomments', ['issue' => $issue]); ?>
        </div>
        <div class="issue-fields">
            <?php include_component('main/viewissuefields', ['issue' => $issue]); ?>
        </div>
    </div>
</div>