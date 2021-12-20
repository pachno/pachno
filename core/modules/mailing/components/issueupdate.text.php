* <?php use pachno\core\entities\LogItem;

echo $issue->getFormattedTitle(true); ?> *
<?php echo __('Updated by %name', array('%name' => $updated_by->getNameWithUsername()));?>

<?php echo '(' . __('Created by %name', array('%name' => $issue->getPostedBy()->getNameWithUsername())); ?>

<?php if (isset($comment) && $comment instanceof \pachno\core\entities\Comment): ?>
* <?php echo __('Comment by %name', array('%name' => $comment->getPostedBy()->getNameWithUsername()));?> *
<?php echo $comment->getContent(); ?>
<?php endif; ?>

<?php if (count($log_items)): ?>
* <?php echo __('Changes'); ?> *
<?php foreach ($log_items as $item): ?>
<?php
            switch ($item->getChangeType())
            {
                case LogItem::ACTION_ISSUE_CREATED:
                case LogItem::ACTION_COMMENT_CREATED:
                    break;
                case LogItem::ACTION_ISSUE_CLOSE:
                    echo ' * ' . __('Issue closed');
                    break;
                case LogItem::ACTION_ISSUE_REOPEN:
                    echo ' * ' . __('Issue reopened');
                    break;
                case LogItem::ACTION_ISSUE_UPDATE_FREE_TEXT:
                    echo ' * ' . $item->getText();
                    break;
                case LogItem::ACTION_ISSUE_UPDATE_PAIN_BUG_TYPE:
                    echo ' * ' . __('Triaged bug type: %text', array('%text' => str_replace('&rArr;', '->', $item->getText())));
                    break;
                case LogItem::ACTION_ISSUE_UPDATE_PAIN_LIKELIHOOD:
                    echo ' * ' . __('Triaged likelihood: %text', array('%text' => str_replace('&rArr;', '->', $item->getText())));
                    break;
                case LogItem::ACTION_ISSUE_UPDATE_PAIN_EFFECT:
                    echo ' * ' . __('Triaged effect: %text', array('%text' => str_replace('&rArr;', '->', $item->getText())));
                    break;
                case LogItem::ACTION_ISSUE_UPDATE_PAIN_SCORE:
                    echo ' * ' . __('Calculated user pain: %text', array('%text' => str_replace('&rArr;', '->', $item->getText())));
                    break;
                case LogItem::ACTION_ISSUE_UPDATE_CATEGORY:
                    echo ' * ' . __('Category changed: %text', array('%text' => str_replace("&rArr;", '->', $item->getText())));
                    break;
                case LogItem::ACTION_ISSUE_UPDATE_CUSTOMFIELD:
                    echo ' * ' . __('Custom field changed: %text', array('%text' => str_replace('&rArr;', '->', $item->getText())));
                    break;
                case LogItem::ACTION_ISSUE_UPDATE_STATUS:
                    echo ' * ' . __('Status changed: %text', array('%text' => str_replace('&rArr;', '->', $item->getText())));
                    break;
                case LogItem::ACTION_ISSUE_UPDATE_REPRODUCABILITY:
                    echo ' * ' . __('Reproducability changed: %text', array('%text' => str_replace('&rArr;', '->', $item->getText())));
                    break;
                case LogItem::ACTION_ISSUE_UPDATE_PRIORITY:
                    echo ' * ' . __('Priority changed: %text', array('%text' => str_replace('&rArr;', '->', $item->getText())));
                    break;
                case LogItem::ACTION_ISSUE_UPDATE_SEVERITY:
                    echo ' * ' . __('Severity changed: %text', array('%text' => str_replace('&rArr;', '->', $item->getText())));
                    break;
                case LogItem::ACTION_ISSUE_UPDATE_RESOLUTION:
                    echo ' * ' . __('Resolution changed: %text', array('%text' => str_replace('&rArr;', '->', $item->getText())));
                    break;
                case LogItem::ACTION_ISSUE_UPDATE_PERCENT_COMPLETE:
                    echo ' * ' . __('Percent completed: %text', array('%text' => str_replace('&rArr;', '->', $item->getText())));
                    break;
                case LogItem::ACTION_ISSUE_UPDATE_MILESTONE:
                    echo ' * ' . __('Target milestone changed: %text', array('%text' => str_replace('&rArr;', '->', $item->getText())));
                    break;
                case LogItem::ACTION_ISSUE_UPDATE_ISSUETYPE:
                    echo ' * ' . __('Issue type changed: %text', array('%text' => str_replace('&rArr;', '->', $item->getText())));
                    break;
                case LogItem::ACTION_ISSUE_UPDATE_ESTIMATED_TIME:
                    echo ' * ' . __('Estimation changed: %text', array('%text' => str_replace('&rArr;', '->', \pachno\core\entities\common\Timeable::formatTimeableLog($item->getText(), $item->getPreviousValue(), $item->getCurrentValue(), true, true))));
                    break;
                case LogItem::ACTION_ISSUE_UPDATE_TIME_SPENT:
                    echo ' * ' . __('Time spent: %text', array('%text' => str_replace('&rArr;', '->', \pachno\core\entities\common\Timeable::formatTimeableLog($item->getText(), $item->getPreviousValue(), $item->getCurrentValue(), true, true))));
                    break;
                case LogItem::ACTION_ISSUE_UPDATE_ASSIGNEE:
                    echo ' * ' . __('Assignee changed: %text', array('%text' => $item->getText()));
                    break;
                case LogItem::ACTION_ISSUE_UPDATE_OWNER:
                    echo ' * ' . __('Owner changed: %text', array('%text' => $item->getText()));
                    break;
                case LogItem::ACTION_ISSUE_UPDATE_POSTED_BY:
                    echo ' * ' . __('Posted by changed: %text', array('%text' => str_replace('&rArr;', '->', $item->getText())));
                    break;
                default:
                    if (!$item->getText())
                    {
                        echo ' * ' . __('Issue updated');
                    }
                    else
                    {
                        echo ' * ' . $item->getText();
                    }
                    break;
            }
        ?>

<?php endforeach; ?>
<?php endif; ?>


<?php echo __('Show issue:') . ' ' . $module->getPrefixedUrl($issue->getUrl()); ?>

<?php if (isset($comment) && $comment instanceof \pachno\core\entities\Comment) { echo __('Show comment:') . ' ' . $module->getPrefixedUrl($issue->getUrl()) . '#comment_' . $comment->getID(); } ?>

<?php echo __('Show %project project dashboard:', array('%project' => $issue->getProject()->getName())) . ' ' . $module->generateURL('project_dashboard', array('project_key' => $issue->getProject()->getKey())); ?>


<?php echo __('You were sent this notification email because you are related to, subscribed to, or commented on the issue mentioned in this email.'); ?>

<?php echo __('Depending on your notification settings, you may or may not be notified again when this issue is updated in the future.'); ?>

<?php echo __('To change when and how often we send these emails, update your account settings:') . ' ' . $module->generateURL('profile_account'); ?>
