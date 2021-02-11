<?php

    use pachno\core\entities\Issue;
    use pachno\core\entities\User;

    /** @var User $pachno_user */
    /** @var Issue $issue */

?>
<div class="toggle-favourite-container tooltip-container">
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
        <?php include_component('main/favouritetoggle', ['url' => make_url('toggle_favourite_issue', ['issue_id' => $issue->getID(), 'user_id' => $pachno_user->getID()]), 'include_user' => false, 'starred' => $pachno_user->isIssueStarred($issue->getID())]); ?>
    <?php endif; ?>
</div>
