<<?php ?>?xml version="1.0" encoding="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" ?>
<rss version="2.0">
    <channel>
        <title><?php echo \pachno\core\framework\Settings::getSiteHeaderName() . ' ~ '. $searchtitle; ?></title>
        <link><?php echo make_url('home', array(), false); ?></link>
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
            <title><?php echo \pachno\core\framework\Settings::getSiteHeaderName() . ' ~ '. $searchtitle; ?></title>
            <link><?php echo make_url('home', array(), false); ?></link>
        </image>
<?php if ($search_object->getNumberOfIssues()): ?>
    <?php foreach ($search_object->getIssues() as $issue): ?>
        
        <item>
            <title><?php echo $issue->getFormattedIssueNo(true) . ' - ' . strip_tags($issue->getTitle()); ?></title>
            <?php if ($issue->getDescription() == ''): ?>
            <description><?php echo __('Nothing entered.'); ?></description>
            <?php else: ?>
            <description><![CDATA[<?php echo strip_tags($issue->getDescription()); ?>]]></description>
            <?php endif; ?>
            <pubDate><?php echo \pachno\core\framework\Context::getI18n()->formatTime($issue->getLastUpdatedTime(), 21); ?></pubDate>
            <link><?php echo $issue->getUrl(false); ?></link>
            <guid><?php echo $issue->getUrl(false); ?></guid>
        </item>
<?php endforeach; ?>
<?php endif; ?>
    </channel>
</rss>