<div id="livelink_getcommit_backdrop_box" class="backdrop_box large">
    <div class="backdrop_detail_header">
        <span><?php echo __('Show commit details'); ?></span>
        <a href="javascript:void(0)" onclick="Pachno.UI.Backdrop.reset()" class="closer"><?php echo fa_image_tag('times'); ?></a>
    </div>
    <div class="backdrop_detail_content">
        <div class="comment" id="commit_<?php echo $commit->getID(); ?>">
            <div id="commit_view_<?php echo $commit->getID(); ?>" class="comment_main">
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
            </div>
        </div>
    </div>
</div>
