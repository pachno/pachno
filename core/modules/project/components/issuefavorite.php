<?php

    use pachno\core\entities\Issue;
    use pachno\core\entities\User;

    /** @var User $pachno_user */
    /** @var Issue $issue */

?>
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
