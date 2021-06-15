<?php

    use pachno\core\entities\User;
    use pachno\core\framework\Response;
    use pachno\core\entities\Issue;

    /**
     * @var User $pachno_user
     * @var Response $pachno_response
     * @var ?Issue $issue
     */

?>
<?php if ($issue instanceof Issue): ?>
    <?php

        $pachno_response->setTitle('['.(($issue->isClosed()) ? mb_strtoupper(__('Closed')) : mb_strtoupper(__('Open'))) .'] ' . $issue->getFormattedIssueNo(true) . ' - ' . \pachno\core\framework\Context::getI18n()->decodeUTF8($issue->getTitle()));
        $json = $issue->toJSON(true);

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
    <script type="text/javascript">
        Pachno.on(Pachno.EVENTS.ready, function () {
            const issue = Pachno.addIssue(<?= json_encode($json, JSON_THROW_ON_ERROR); ?>, undefined, false);
            issue.allowShortcuts(<?= json_encode($json['fields'], JSON_THROW_ON_ERROR); ?>);
            issue.updateVisibleValues();
        });
    </script>
<?php else: ?>
    <div class="message-box type-error" id="notfound_error">
        <div class="message">
            <div class="title"><?php echo __("This issue can not be displayed"); ?></div>
            <div class="message"><?php echo __("This issue either does not exist, has been deleted or you do not have permission to view it."); ?></div>
        </div>
    </div>
<?php endif; ?>
