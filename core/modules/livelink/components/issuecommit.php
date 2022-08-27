<?php

    use pachno\core\entities\Commit;
    
    /**
     * @var Commit $commit
     */

?>
<a class="configurable-component trigger-backdrop" data-commit data-commit-id="<?= $commit->getID(); ?>" data-url="<?php echo make_url('get_partial_for_backdrop', array('key' => 'livelink-getcommit', 'commit_id' => $commit->getID())); ?>">
    <div class="row">
        <div class="icon">
            <?php echo fa_image_tag('code'); ?>
        </div>
        <div class="information">
            <span class="count-badge"><?= $commit->getRevisionString(); ?></span>
        </div>
        <div class="name">
            <div class="title">
                <?php echo $commit->getTitle(); ?>
            </div>
        </div>
    </div>
    
    <?php /*<div id="commit_view_<?php echo $commit->getID(); ?>" class="comment_main">
        <div id="commit_<?php echo $commit->getID(); ?>_header" class="commentheader">
            <div class="commenttitle">
                <?php include_component('main/userdropdown', array('user' => $commit->getAuthor(), 'size' => 'large')); ?>
            </div>
            <div class="comment_hash">
                <a href="javascript:void(0)" onclick="Pachno.UI.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'livelink_getcommit', 'commit_id' => $commit->getID())); ?>');"><?php echo $commit->getRevisionString(); ?></a>
            </div>
            <div class="commentdate" id="commit_<?php echo $commit->getID(); ?>_date">
                <?php echo \pachno\core\framework\Context::getI18n()->formatTime($commit->getDate(), 9); ?>
            </div>
        </div>

        <div class="commentbody article commit_main" id="commit_<?php echo $commit->getID(); ?>_body">
            <?php echo \pachno\core\helpers\TextParser::parseText(trim($commit->getLog()), false, null, array('target' => $commit)); ?>
        </div>
    </div> */ ?>
</a>
