<?php foreach (\pachno\core\entities\Comment::getComments($target_id, $target_type, $pachno_user->getCommentSortOrder()) as $comment): ?>
    <?php

    if ($comment->isReply()) continue;

    $options = compact('comment', 'comment_count_div', 'mentionable_target_type');
    if (isset($issue))
        $options['issue'] = $issue;

    if (isset($article))
        $options['article'] = $article;

    if (isset($commit))
        $options['commit'] = $commit;

    include_component('main/commentwrapper', $options);

    ?>
<?php endforeach; ?>

