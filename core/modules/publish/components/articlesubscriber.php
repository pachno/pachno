<li style="vertical-align: middle; clear: both; height: 20px;">
    <span class="icon">
        <?php include_component('main/favouritetoggle', ['url' => make_url('publish_toggle_favourite_article', array('article_id' => $article->getID(), 'user_id' => $user->getID())), 'starred' => true]); ?>
    </span>
    <?php include_component('main/userdropdown', compact('user')); ?>
</li>