<?php

    /**
     * @var \pachno\core\entities\User $pachno_user
     * @var \pachno\core\framework\Response $pachno_response
     * @var \pachno\core\entities\Issue $issue
     */

?>
<?php if ($issue instanceof \pachno\core\entities\Issue): ?>
    <?php

        $pachno_response->setTitle('['.(($issue->isClosed()) ? mb_strtoupper(__('Closed')) : mb_strtoupper(__('Open'))) .'] ' . $issue->getFormattedIssueNo(true) . ' - ' . \pachno\core\framework\Context::getI18n()->decodeUTF8($issue->getTitle()));

    ?>
    <?php \pachno\core\framework\Event::createNew('core', 'viewissue_top', $issue)->trigger(); ?>
    <div id="issuetype_indicator_fullpage" style="display: none;" class="fullpage_backdrop">
        <div style="position: absolute; top: 45%; left: 40%; z-index: 100001; color: #FFF; font-size: 15px; font-weight: bold;">
            <?php echo image_tag('spinning_32.gif'); ?><br>
            <?php echo __('Please wait while updating issue type'); ?>...
        </div>
    </div>
    <div class="content-with-sidebar">
        <?php include_component('project/sidebar', ['collapsed' => true]); ?>
        <?php include_component('project/issue', ['issue' => $issue]); ?>
    </div>
    <?php include_component('main/issue_workflow_transition', compact('issue')); ?>
    <?php if ($pachno_user->isViewissueTutorialEnabled()): ?>
        <?php //include_component('main/tutorial_viewissue', compact('issue')); ?>
    <?php endif; ?>
<?php elseif (isset($issue_deleted)): ?>
    <div class="greenbox" id="issue_deleted_message">
        <div class="header"><?php echo __("This issue has been deleted"); ?></div>
        <div class="content"><?php echo __("This message will disappear when you reload the page."); ?></div>
    </div>
<?php else: ?>
    <div class="redbox" id="notfound_error">
        <div class="header"><?php echo __("This issue can not be displayed"); ?></div>
        <div class="content"><?php echo __("This issue either does not exist, has been deleted or you do not have permission to view it."); ?></div>
    </div>
<?php endif; ?>
