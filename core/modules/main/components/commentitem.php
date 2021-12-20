<?php if (isset($issue) && $issue instanceof \pachno\core\entities\Issue): ?>
    <tr>
        <td class="imgtd"><?php echo fa_image_tag($issue->getIssueType()->getFontAwesomeIcon()); ?></td>
        <td style="padding-bottom: <?php if (isset($extra_padding) && $extra_padding == true): ?>20<?php else: ?>15<?php endif; ?>px;">
            <?php if (isset($include_time) && $include_time == true): ?><span class="time"><?php echo \pachno\core\framework\Context::getI18n()->formatTime($comment->getPosted(), 19); ?></span>&nbsp;<?php endif; ?>
            <?php if (isset($include_project) && $include_project == true): ?><span class="faded_out smaller"><?php echo image_tag($issue->getProject()->getIconName(), array('class' => 'issuelog-project-logo'), true); ?></span><?php endif; ?>
            <?php
                $issue_title = \pachno\core\framework\Context::getI18n()->decodeUTF8($issue->getFormattedTitle(true));
                if (isset($pad_length))
                {
                    $issue_title = pachno_truncateText($issue_title, $pad_length);
                }
            ?>
            <?php echo link_tag($issue->getUrl(), $issue_title, array('class' => $issue->isClosed() ? 'issue_closed' : 'issue_open')); ?>
            <br>
            <span class="user">
                <?php if (($user = $comment->getPostedBy()) instanceof \pachno\core\entities\User): ?>
                    <?php echo __('%buddy_name (%username) said'.':', array('%username' => $user->getUsername(), '%buddy_name' => $user->getBuddyname())); ?>
                <?php else: ?>
                    <?php echo __('Unknown user said').':'; ?>
                <?php endif; ?>
            </span>
            <?php
                echo '<div class="timeline_inline_details">';
                echo nl2br(pachno_truncateText(\pachno\core\framework\Context::getI18n()->decodeUTF8($comment->getContent())));
                echo '</div>';
            ?>
        </td>
    </tr>
<?php endif; ?>
