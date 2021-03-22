<?php

    /**
     * @var \pachno\core\entities\Issue $issue
     * @var \pachno\core\entities\User[] $users
     */

?>
<div class="backdrop_box medium" id="viewissue_add_item_div">
    <div class="backdrop_detail_header">
        <span><?= __('Manage issue subscribers'); ?></span>
        <?= javascript_link_tag(fa_image_tag('user-plus'), array('onclick' => "$('#popup_find_subscriber_{$issue->getID()}').toggleClass('force-active');", 'class' => 'add_link')); ?>
        <a href="javascript:void(0);" class="closer" onclick="Pachno.UI.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <?php /* include_component('main/identifiableselector', [
            'header'        => __('Subscribe someone to this issue'),
            'callback'      => "Pachno.Issues.toggleFavourite('".make_url('toggle_favourite_issue', ['issue_id' => $issue->getID(), 'user_id' => '%identifiable_value'])."', '".$issue->getID()."_%identifiable_value');",
            'base_id'       => "popup_find_subscriber_{$issue->getID()}",
            'include_teams' => false,
            'allow_clear'   => false,
            'style'         => ['right' => '8px'],
            'absolute'      => true]); */ ?>
        <p>
            <?= __('The list below shows all users manually subscribed to notifications about this issue. To toggle whether they receive notifications, click the star next to their name.'); ?>
        </p>
        <ul id="subscribers_list" class="list-mode" style="margin-top: 15px;">
            <?php foreach ($users as $user): ?>
                <?php include_component('main/issuesubscriber', compact('user', 'issue')); ?>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
