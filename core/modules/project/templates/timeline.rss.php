<<?php ?>?xml version="1.0" encoding="<?php use pachno\core\entities\LogItem;

echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" ?>
<rss version="2.0">
    <channel>
        <title><?php echo \pachno\core\framework\Settings::getSiteHeaderName() . ' ~ '. __('%project_name project timeline', array('%project_name' => \pachno\core\framework\Context::getCurrentProject()->getName())); ?></title>
        <link><?php echo make_url('project_timeline', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey()), false); ?></link>
        <description><?php echo strip_tags(\pachno\core\framework\Settings::getSiteHeaderName()); ?></description>
        <language><?php echo (mb_strtolower(str_replace('_', '-', \pachno\core\framework\Context::getI18n()->getCurrentLanguage()))); ?></language>
        <image>
        <?php if (\pachno\core\framework\Settings::isUsingCustomHeaderIcon() == '2'): ?>
            <url><?php echo \pachno\core\framework\Settings::getHeaderIconURL(); ?></url>
        <?php elseif (\pachno\core\framework\Settings::isUsingCustomHeaderIcon() == '1'): ?>
            <url><?php echo \pachno\core\framework\Context::getUrlHost().\pachno\core\framework\Context::getWebroot().'header.png'; ?></url>
        <?php else: ?>
            <url><?php echo image_url('logo_24.png', false, null, false); ?></url>
        <?php endif; ?>
            <title><?php echo \pachno\core\framework\Settings::getSiteHeaderName() . ' ~ '. __('%project_name project timeline', array('%project_name' => \pachno\core\framework\Context::getCurrentProject()->getName())); ?></title>
            <link><?php echo make_url('project_timeline', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey()), false); ?></link>
        </image>
<?php foreach ($recent_activities as $timestamp => $activities): ?>
<?php foreach ($activities as $activity): ?>
<?php if (array_key_exists('target_type', $activity) && $activity['target_type'] == 1 && ($issue = \pachno\core\entities\tables\Issues::getTable()->selectById($activity['target'])) && $issue instanceof \pachno\core\entities\Issue): ?>
<?php if ($issue->isDeleted()): continue; endif; ?>
        <item>
            <title><![CDATA[
                <?php
                    $activity['text'] = str_replace("&rArr;", '->', html_entity_decode($activity['text']));
                    switch ($activity['change_type'])
                    {
                        case LogItem::ACTION_ISSUE_CREATED:
                            echo __('Issue created');
                            break;
                        case LogItem::ACTION_ISSUE_CLOSE:
                            echo __('Issue closed %text', array('%text' => $activity['text']));
                            break;
                        case LogItem::ACTION_ISSUE_REOPEN:
                            echo __('Issue reopened');
                            break;
                        case LogItem::ACTION_ISSUE_UPDATE_FREE_TEXT:
                            echo $activity['text'];
                            break;
                        case LogItem::ACTION_ISSUE_UPDATE_CATEGORY:
                            echo __('Category changed: %text', array('%text' => $activity['text']));
                            break;
                        case LogItem::ACTION_ISSUE_UPDATE_CUSTOMFIELD:
                            echo __('Custom field changed: %text', array('%text' => $activity['text']));
                            break;
                        case LogItem::ACTION_ISSUE_UPDATE_STATUS:
                            echo __('Status changed: %text', array('%text' => $activity['text']));
                            break;
                        case LogItem::ACTION_ISSUE_UPDATE_REPRODUCABILITY:
                            echo __('Reproducability changed: %text', array('%text' => $activity['text']));
                            break;
                        case LogItem::ACTION_ISSUE_UPDATE_PRIORITY:
                            echo __('Priority changed: %text', array('%text' => $activity['text']));
                            break;
                        case LogItem::ACTION_ISSUE_UPDATE_SEVERITY:
                            echo __('Severity changed: %text', array('%text' => $activity['text']));
                            break;
                        case LogItem::ACTION_ISSUE_UPDATE_RESOLUTION:
                            echo __('Resolution changed: %text', array('%text' => $activity['text']));
                            break;
                        case LogItem::ACTION_ISSUE_UPDATE_PERCENT_COMPLETE:
                            echo __('Percent completed: %text', array('%text' => $activity['text']));
                            break;
                        case LogItem::ACTION_ISSUE_UPDATE_MILESTONE:
                            echo __('Target milestone changed: %text', array('%text' => $activity['text']));
                            break;
                        case LogItem::ACTION_ISSUE_UPDATE_ISSUETYPE:
                            echo __('Issue type changed: %text', array('%text' => $activity['text']));
                            break;
                        case LogItem::ACTION_ISSUE_UPDATE_ESTIMATED_TIME:
                            echo __('Estimation changed: %text', array('%text' => \pachno\core\entities\common\Timeable::formatTimeableLog($activity['text'], $activity['previous_value'], $activity['current_value'], true, true)));
                            break;
                        case LogItem::ACTION_ISSUE_UPDATE_TIME_SPENT:
                            echo __('Time spent: %text', array('%text' => \pachno\core\entities\common\Timeable::formatTimeableLog($activity['text'], $activity['previous_value'], $activity['current_value'], true, true)));
                            break;
                        case LogItem::ACTION_ISSUE_UPDATE_ASSIGNEE:
                            echo __('Assignee changed: %text', array('%text' => $activity['text']));
                            break;
                        case LogItem::ACTION_ISSUE_UPDATE_OWNER:
                            echo __('Owner changed: %text', array('%text' => $activity['text']));
                            break;
                        case LogItem::ACTION_ISSUE_UPDATE_POSTED_BY:
                            echo __('Posted by changed: %text', array('%text' => $activity['text']));
                            break;
                        default:
                            if (empty($activity['text']))
                            {
                                echo __('Issue updated');
                            }
                            else
                            {
                                echo $activity['text'];
                            }
                            break;
                    }

                ?>: <?php echo $issue->getFormattedIssueNo(true) . ' - ' . $issue->getTitle(); ?>]]></title>
            <description><![CDATA[<?php echo strip_tags($issue->getDescription()); ?>]]></description>
            <pubDate><?php echo \pachno\core\framework\Context::getI18n()->formatTime($issue->getLastUpdatedTime(), 21); ?></pubDate>
            <link><?php echo $issue->getUrl(false); ?></link>
            <guid isPermaLink="false"><?php echo sha1($timestamp.$activity['text']); ?></guid>
        </item>
        
<?php endif; ?>
<?php endforeach; ?>
<?php endforeach; ?>

    </channel>
</rss>
