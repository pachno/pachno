<?php

/** @var \pachno\core\entities\User $pachno_user */
/** @var \pachno\core\entities\Issue $issue */
/** @var \pachno\core\framework\Response $pachno_response */

?>
<div class="header-container">
    <div class="toggle-favourite">
        <?php if ($pachno_user->isGuest()): ?>
            <?php echo fa_image_tag('star', ['id' => 'issue_favourite_faded_'.$issue->getId(), 'class' => 'unsubscribed']); ?>
            <div class="tooltip from-above leftie">
                <?php echo __('Please log in to bookmark issues'); ?>
            </div>
        <?php else: ?>
            <div class="tooltip from-above leftie">
                <?php echo __('Click the star to toggle whether you want to be notified whenever this issue updates or changes'); ?><br>
                <br>
                <?php echo __('If you have the proper permissions, you can manage issue subscribers via the "%more_actions" button to the right.', array('%more_actions' => __('More actions'))); ?>
            </div>
            <?php echo fa_image_tag('spinner', array('id' => 'issue_favourite_indicator_'.$issue->getId(), 'style' => 'display: none;', 'class' => 'fa-spin')); ?>
            <?php echo fa_image_tag('star', array('id' => 'issue_favourite_faded_'.$issue->getId(), 'class' => 'unsubscribed', 'style' => ($pachno_user->isIssueStarred($issue->getID())) ? 'display: none;' : '', 'onclick' => "Pachno.Issues.toggleFavourite('".make_url('toggle_favourite_issue', array('issue_id' => $issue->getID(), 'user_id' => $pachno_user->getID()))."', ".$issue->getID().");")); ?>
            <?php echo fa_image_tag('star', array('id' => 'issue_favourite_normal_'.$issue->getId(), 'class' => 'subscribed', 'style' => (!$pachno_user->isIssueStarred($issue->getID())) ? 'display: none;' : '', 'onclick' => "Pachno.Issues.toggleFavourite('".make_url('toggle_favourite_issue', array('issue_id' => $issue->getID(), 'user_id' => $pachno_user->getID()))."', ".$issue->getID().");")); ?>
        <?php endif; ?>
    </div>
    <div id="title-field" class="title-container">
        <span class="title-crumbs">
            <?php include_component('issueparent_crumbs', array('issue' => $issue)); ?>
        </span>
        <div id="title_content">
            <span class="faded_out" id="no_title" <?php if ($issue->getTitle() != ''):?> style="display: none;" <?php endif; ?>><?php echo __('Nothing entered.'); ?></span>
            <span id="title-name" class="title-name" title="<?php echo \pachno\core\framework\Context::getI18n()->decodeUTF8($issue->getTitle()); ?>">
                <?php echo \pachno\core\framework\Context::getI18n()->decodeUTF8($issue->getTitle()); ?>
            </span>
        </div>
        <?php if ($issue->isEditable() && $issue->canEditTitle()): ?>
            <span id="title_change" style="display: none;">
                <form id="title_form" action="<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'title')); ?>" method="post" onSubmit="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'title')) ?>', 'title'); return false;">
                    <input type="text" name="value" value="<?php echo $issue->getTitle(); ?>"><span class="title_form_save_container"><?php echo __('%cancel or %save', array('%save' => '<input type="submit" class="button" value="'.__('Save').'">', '%cancel' => '<a href="#" onclick="$(\'title-field\').toggleClassName(\'editing\');$(\'title_change\').hide(); $(\'title_name\').show(); return false;">'.__('cancel').'</a>')); ?></span>
                </form>
                <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'title_spinning')); ?>
                <span id="title_change_error" class="error_message" style="display: none;"></span>
            </span>
        <?php endif; ?>
        <div id="posted_by_field" class="posted-by-container">
            <label><?php echo __('Posted by'); ?></label>
            <div id="posted_by_content" class="dropper-container">
                <div id="posted_by_name" class="value">
                    <?php echo include_component('main/userdropdown', ['user' => $issue->getPostedBy(), 'size' => 'medium']); ?>
                    <?php if ($issue->isEditable() && $issue->canEditPostedBy()): ?>
                        <a href="javascript:void(0);" class="button secondary dropper" title="<?php echo __('Click to change owner'); ?>"><?= fa_image_tag('angle-down'); ?></a>
                        <div class="dropdown-container from-left">
                            <?php include_component('main/identifiableselector', [
                                'html_id'             => 'posted_by_change',
                                'header'             => __('Change poster'),
                                'allow_clear'        => false,
                                'clear_link_text'    => '',
                                'callback'             => "Pachno.Issues.Field.set('" . make_url('issue_setfield', ['project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'posted_by', 'value' => '%identifiable_value']) . "', 'posted_by');",
                                'base_id'            => 'posted_by',
                                'absolute'            => true,
                                'classes'            => '']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
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
    <div style="<?php if (!$issue->isVotesVisible()): ?> display: none;<?php endif; ?>" id="votes_additional"<?php if ($issue->isVotesVisible()): ?> class="visible"<?php endif; ?>>
        <div id="viewissue_votes">
            <div id="vote_down">
                <?php $vote_down_options = ($issue->getProject()->isArchived() || $issue->hasUserVoted($pachno_user, false)) ? 'display: none;' : ''; ?>
                <?php $vote_down_faded_options = ($vote_down_options == '') ? 'display: none;' : ''; ?>
                <?php echo javascript_link_tag(fa_image_tag('minus'), array('onclick' => "Pachno.Issues.voteDown('".make_url('issue_vote', array('issue_id' => $issue->getID(), 'vote' => 'down'))."');", 'id' => 'vote_down_link', 'class' => 'image', 'style' => $vote_down_options)); ?>
                <?php echo image_tag('spinning_16.gif', array('id' => 'vote_down_indicator', 'style' => 'display: none;')); ?>
                <?php echo image_tag('action_vote_minus_faded.png', array('id' => 'vote_down_faded', 'style' => $vote_down_faded_options)); ?>
            </div>
            <div class="votes">
                <div id="issue_votes"><?php echo $issue->getVotes(); ?></div>
                <div class="votes_header"><?php echo __('Votes'); ?></div>
            </div>
            <div id="vote_up">
                <?php $vote_up_options = ($issue->getProject()->isArchived() || $issue->hasUserVoted($pachno_user, true)) ? 'display: none;' : ''; ?>
                <?php $vote_up_faded_options = ($vote_up_options == '') ? 'display: none;' : ''; ?>
                <?php echo javascript_link_tag(fa_image_tag('plus'), array('onclick' => "Pachno.Issues.voteUp('".make_url('issue_vote', array('issue_id' => $issue->getID(), 'vote' => 'up'))."');", 'id' => 'vote_up_link', 'class' => 'image', 'style' => $vote_up_options)); ?>
                <?php echo image_tag('spinning_16.gif', array('id' => 'vote_up_indicator', 'style' => 'display: none;')); ?>
                <?php echo image_tag('action_vote_plus_faded.png', array('id' => 'vote_up_faded', 'style' => $vote_up_faded_options)); ?>
            </div>
        </div>
    </div>
    <div style="<?php if (!$issue->isUserPainVisible()): ?> display: none;<?php endif; ?>" id="user_pain_additional"<?php if ($issue->isVotesVisible()): ?> class="visible"<?php endif; ?>>
        <div title="<?php echo __('This is the user pain value for this issue'); ?>" id="viewissue_triaging">
            <div class="user_pain" id="issue_user_pain"><?php echo $issue->getUserPain(); ?></div>
            <div class="user_pain_calculated" id="issue_user_pain_calculated"><?php echo $issue->getUserPainDiffText(); ?></div>
        </div>
    </div>
</div>
