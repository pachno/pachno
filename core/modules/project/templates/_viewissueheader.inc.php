<?php

/** @var \pachno\core\entities\User $pachno_user */
/** @var \pachno\core\entities\Issue $issue */
/** @var \pachno\core\framework\Response $pachno_response */

?>
<div id="viewissue_header_container" class="cf">
    <div class="viewissue-header-container">
        <div class="toggle-favourite">
            <?php if ($pachno_user->isGuest()): ?>
                <?php echo image_tag('star_faded.png', array('id' => 'issue_favourite_faded_'.$issue->getId())); ?>
                <div class="tooltip from-above leftie">
                    <?php echo __('Please log in to bookmark issues'); ?>
                </div>
            <?php else: ?>
                <div class="tooltip from-above leftie">
                    <?php echo __('Click the star to toggle whether you want to be notified whenever this issue updates or changes'); ?><br>
                    <br>
                    <?php echo __('If you have the proper permissions, you can manage issue subscribers via the "%more_actions" button to the right.', array('%more_actions' => __('More actions'))); ?>
                </div>
                <?php echo image_tag('spinning_20.gif', array('id' => 'issue_favourite_indicator_'.$issue->getId(), 'style' => 'display: none;')); ?>
                <?php echo fa_image_tag('star', array('id' => 'issue_favourite_faded_'.$issue->getId(), 'class' => 'unsubscribed', 'style' => ($pachno_user->isIssueStarred($issue->getID())) ? 'display: none;' : '', 'onclick' => "Pachno.Issues.toggleFavourite('".make_url('toggle_favourite_issue', array('issue_id' => $issue->getID(), 'user_id' => $pachno_user->getID()))."', ".$issue->getID().");")); ?>
                <?php echo fa_image_tag('star', array('id' => 'issue_favourite_normal_'.$issue->getId(), 'class' => 'subscribed', 'style' => (!$pachno_user->isIssueStarred($issue->getID())) ? 'display: none;' : '', 'onclick' => "Pachno.Issues.toggleFavourite('".make_url('toggle_favourite_issue', array('issue_id' => $issue->getID(), 'user_id' => $pachno_user->getID()))."', ".$issue->getID().");")); ?>
            <?php endif; ?>
        </div>
        <div id="title_field" class="<?php if ($issue->isTitleChanged()): ?>issue_detail_changed<?php endif; ?><?php if (!$issue->isTitleMerged()): ?> issue_detail_unmerged<?php endif; ?> hoverable">
            <div class="viewissue_title">
                <span class="faded_out" id="title_header">
                    <?php include_component('issueparent_crumbs', array('issue' => $issue)); ?>
                </span>
                <span id="issue_title">
                    <?php if ($issue->isEditable() && $issue->canEditTitle()): ?>
                        <?php echo fa_image_tag('edit', array('class' => 'dropdown', 'id' => 'title_edit', 'onclick' => "$('title_field').toggleClassName('editing');$('title_change').show(); $('title_name').hide(); $('no_title').hide();")); ?>
                        <a class="undo" href="javascript:void(0);" onclick="Pachno.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'title')); ?>', 'title');" title="<?php echo __('Undo this change'); ?>"><?php echo fa_image_tag('undo-alt', ['class' => 'undo'], 'fas'); ?></a>
                        <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'title_undo_spinning')); ?>
                    <?php endif; ?>
                    <span id="title_content">
                        <span class="faded_out" id="no_title" <?php if ($issue->getTitle() != ''):?> style="display: none;" <?php endif; ?>><?php echo __('Nothing entered.'); ?></span>
                        <span id="title_name" title="<?php echo \pachno\core\framework\Context::getI18n()->decodeUTF8($issue->getTitle()); ?>">
                            <?php echo \pachno\core\framework\Context::getI18n()->decodeUTF8($issue->getTitle()); ?>
                        </span>
                    </span>
                </span>
                <?php if ($issue->isEditable() && $issue->canEditTitle()): ?>
                    <span id="title_change" style="display: none;">
                        <form id="title_form" action="<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'title')); ?>" method="post" onSubmit="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'title')) ?>', 'title'); return false;">
                            <input type="text" name="value" value="<?php echo $issue->getTitle(); ?>"><span class="title_form_save_container"><?php echo __('%cancel or %save', array('%save' => '<input type="submit" class="button" value="'.__('Save').'">', '%cancel' => '<a href="#" onclick="$(\'title_field\').toggleClassName(\'editing\');$(\'title_change\').hide(); $(\'title_name\').show(); return false;">'.__('cancel').'</a>')); ?></span>
                        </form>
                        <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'title_spinning')); ?>
                        <span id="title_change_error" class="error_message" style="display: none;"></span>
                    </span>
                <?php endif; ?>
            </div>
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
