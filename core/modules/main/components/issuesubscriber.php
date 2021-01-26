<li class="list-item">
    <span class="icon">
        <?php include_component('main/favouritetoggle', ['url' => make_url('toggle_favourite_issue', array('issue_id' => $issue->getID(), 'user_id' => $user->getID())), 'starred' => true]); ?>
    </span>
    <span class="name"><?php include_component('main/userdropdown', ['user' => $user]); ?></span>
</li>