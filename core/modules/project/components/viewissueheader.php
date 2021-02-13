<?php

    use pachno\core\entities\Issue;
    use pachno\core\entities\User;
    use pachno\core\framework\Context;
    use pachno\core\framework\Response;

    /** @var User $pachno_user */
    /** @var Issue $issue */
    /** @var Response $pachno_response */

?>
<div class="top-search-filters-container">
    <div class="header-container">
        <?php include_component('project/issuefavorite', array('issue' => $issue)); ?>
        <div id="title-field" class="title-container">
            <span class="title-crumbs">
                <?php include_component('project/issueparent_crumbs', array('issue' => $issue)); ?>
            </span>
            <div id="title_content">
                <span id="title-name" class="title-name" title="<?php echo \pachno\core\framework\Context::getI18n()->decodeUTF8($issue->getTitle()); ?>">
                    <?php echo \pachno\core\framework\Context::getI18n()->decodeUTF8($issue->getTitle()); ?>
                </span>
            </div>
        </div>
        <div class="status-header">
            <div id="issue-update-indicator" class="indicator issue-update-indicator" data-issue-id="<?= $issue->getID(); ?>">
                <?= fa_image_tag('spinner', ['class' => 'fa-spin']); ?>
            </div>
            <div class="created-times">
                <div id="posted_at_field">
                    <label><?= __('Posted at'); ?></label>
                    <time datetime="<?= Context::getI18n()->formatTime($issue->getPosted(), 24); ?>" title="<?= Context::getI18n()->formatTime($issue->getPosted(), 21); ?>" class="value-container"><?= Context::getI18n()->formatTime($issue->getPosted(), 20); ?></time>
                </div>
                <div id="updated_at_field">
                    <label><?= __('Last updated'); ?></label>
                    <time datetime="<?= Context::getI18n()->formatTime($issue->getLastUpdatedTime(), 24); ?>" title="<?= Context::getI18n()->formatTime($issue->getLastUpdatedTime(), 21); ?>" class="value-container" data-dynamic-field-value data-field="updated_at" data-issue-id="<?= $issue->getId(); ?>"><?= Context::getI18n()->formatTime($issue->getLastUpdatedTime(), 20); ?></time>
                </div>
            </div>
            <div id="status-field" class="dropper-container status-field">
                <div class="status-badge dropper" style="
                        background-color: <?php echo ($issue->getStatus() instanceof \pachno\core\entities\Datatype) ? $issue->getStatus()->getColor() : '#FFF'; ?>;
                        color: <?php echo ($issue->getStatus() instanceof \pachno\core\entities\Datatype) ? $issue->getStatus()->getTextColor() : '#333'; ?>;
                <?php if (!$issue->getStatus() instanceof \pachno\core\entities\Datatype): ?> display: none;<?php endif; ?>
                        " id="status_<?php echo $issue->getID(); ?>_color">
                    <span id="status_content"><?php if ($issue->getStatus() instanceof \pachno\core\entities\Datatype) echo __($issue->getStatus()->getName()); ?></span>
                </div>
                <?php if ($issue->canEditStatus()): ?>
                    <div class="dropdown-container">
                        <div class="list-mode" id="status_change">
                            <div class="header">
                                <span class="name"><?= __('Change status'); ?></span>
                            </div>
                            <?php foreach ($statuses as $status): ?>
                                <?php if (!$status->canUserSet($pachno_user)) continue; ?>
                                <div class="list-item">
                                    <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'status', 'status_id' => $status->getID())); ?>', 'status');">
                                        <div class="status-badge" style="background-color: <?php echo $status->getColor(); ?>;color: <?php echo $status->getTextColor(); ?>;">
                                            <span><?php echo __($status->getName()); ?></span>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="fields-header">
            <?php if ($issue->isClosed()): ?>
                <div class="not-editable">
                    <?= fa_image_tag('check'); ?>
                    <span class="name"><?= __('Done'); ?></span>
                    <span class="tooltip from-above"><?= __('This issue is marked as done / closed'); ?></span>
                </div>
            <?php elseif ((!isset($showLockedStatus) || $showLockedStatus) && !$issue->isEditable()): ?>
                <div class="not-editable">
                    <?= fa_image_tag('lock'); ?>
                    <span class="name"><?= __('Locked'); ?></span>
                    <span class="tooltip from-above"><?= __('Most details of this issue cannot be edited because the workflow defines this step as "locked"'); ?></span>
                </div>
            <?php endif; ?>
            <?php include_component('project/issuefieldissuetype', ['issue' => $issue]); ?>
            <div style="<?php if (true || !$issue->isVotesVisible()): ?> display: none;<?php endif; ?>" id="votes_additional" class="vote-container <?php if ($issue->isVotesVisible()): ?>visible<?php endif; ?> tooltip-container">
                <a id="vote_down_link" href="javascript:void(0);" onclick="Pachno.Issues.voteDown('<?= make_url('issue_vote', array('issue_id' => $issue->getID(), 'vote' => 'down')); ?>');" style="<?= ($issue->getProject()->isArchived() || !$issue->hasUserVoted($pachno_user, true)) ? 'display: none;' : ''; ?>">
                    <?php echo fa_image_tag('spinner', ['id' => 'vote_down_indicator', 'style' => 'display: none;', 'class' => 'fa-spin']); ?>
                    <?php echo fa_image_tag('vote-yea'); ?>
                </a>
                <a id="vote_up_link" href="javascript:void(0);" onclick="Pachno.Issues.voteUp('<?= make_url('issue_vote', array('issue_id' => $issue->getID(), 'vote' => 'up')); ?>');" style="<?= ($issue->getProject()->isArchived() || $issue->hasUserVoted($pachno_user, true)) ? 'display: none;' : ''; ?>">
                    <?php echo fa_image_tag('spinner', ['id' => 'vote_up_indicator', 'style' => 'display: none;', 'class' => 'fa-spin']); ?>
                    <?php echo fa_image_tag('vote-yea'); ?>
                </a>
                <div class="vote-count"><?php echo $issue->getVotes(); ?></div>
                <div class="tooltip from-right">
                    <?= __('Click to toggle a vote for this issue'); ?>
                </div>
            </div>
            <div style="<?php if (!$issue->isUserPainVisible()): ?> display: none;<?php endif; ?>" id="user_pain_additional"<?php if ($issue->isVotesVisible()): ?> class="visible"<?php endif; ?>>
                <div title="<?php echo __('This is the user pain value for this issue'); ?>" id="viewissue_triaging">
                    <div class="user_pain" id="issue_user_pain"><?php echo $issue->getUserPain(); ?></div>
                    <div class="user_pain_calculated" id="issue_user_pain_calculated"><?php echo $issue->getUserPainDiffText(); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
