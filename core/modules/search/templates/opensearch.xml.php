<?php print '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/"
                       xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                       xsi:schemaLocation="https://a9.com/-/spec/opensearch/1.1/ ">
    <ShortName><?php echo (\pachno\core\framework\Context::isProjectContext()) ? __('%project_name search', array('%project_name' => \pachno\core\framework\Context::getCurrentProject()->getName())) : __('%site_name search', array('%site_name' => \pachno\core\framework\Settings::getSiteHeaderName())); ?></ShortName>
    <LongName><?php echo (\pachno\core\framework\Context::isProjectContext()) ? __('%project_name search', array('%project_name' => \pachno\core\framework\Context::getCurrentProject()->getName())) : __('%site_name search', array('%site_name' => \pachno\core\framework\Settings::getSiteHeaderName())); ?></LongName>
    <Description><?php echo (\pachno\core\framework\Context::isProjectContext()) ? __('%project_name search', array('%project_name' => \pachno\core\framework\Context::getCurrentProject()->getName())) : __('%site_name search', array('%site_name' => \pachno\core\framework\Settings::getSiteHeaderName())); ?></Description>
    <Image width="16" height="16">
        data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8%2F9hAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAAIgAAACIBB7P0uQAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAKISURBVDiNpZNPiE1xFMc%2F5%2F7ufX%2B853nzZh6G%2BYPBaESDovxPilKabEhZsJOFLBRjITuFiAULkqRYsLAxooiEGAzR%2BPcmRmP8mWnejDf3vXfvu8fmjaiRhbM5ncX5dL6n71dUlf8p%2B%2FehslIS8cnsTFRS7%2FuUst94le3mTi6nT%2F8JmFgv06Yv42Zjc3pqQ%2FUikukw99tv8LZj0E1OkmMDPdo6GsACEJFQopozk2Yyta7JZt2aDbif6tm19ShzlkWiTYvZm0zL7r8CQlFaYklWhmNQ1M8MeZ1s2bSNh239LF64lJomqJnDAceRFaMCii4NhWHwCqDAs54T9FlX0bGv6Xj%2BmHQdjK8jWlHNcRGpEBH5BRCRFOBlv8BALwwPQXbI5dabVqzG03TcHSA%2FBJGxEK9iLrAf2CcizSNPXOc4TktvxuscN4FZ8RTYIfB9sG2YvxYuH4aqGiAAYDmQBw4B2Kp6XkR6ReRs1xP1bAfHL0B6CsTGQSEPue9hd%2Fa0RWKnXtrFxr7Zve%2FMtVKp5IhIkbKRDDADOBaK0N2wAF3Ygi7dTBCvsPpOXWjVN59v65Hr03XHGYJYwr4CbATMiA8U6AcuFfN0vW9nfXcnlV6e2rmrgtTX6EXaug5ixgREoogJ%2BV40Gl3iuu5lG0BVAxH5AfQBMcAr5qg1xoQsUyLTlaHjFNnBTH3MC3%2FoH%2FyOgjsfaEZVR2RsB9oSicRJYDNw1hhzXyxexFN8C0dsf8%2B5ZH7eatqADUAjYOT3MImIKV%2BQBmqBfZZleUEQ3AMmWDY%2FAp%2FXwAMgo6reH2FS1VJZig8MA4%2BMMZkgCLoBO%2FDJAT3AR1X1AGS0OJedZgMOECl3gAKQA3wtL%2F4EbzL%2FhCjT%2FIEAAAAASUVORK5CYII%3D
    </Image>
    <Url type="text/html"
         template="<?php echo (\pachno\core\framework\Context::isProjectContext()) ? make_url('project_issues', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey()), false) : make_url('search', array(), false); ?>?fs[text][o]=%3A&amp;fs[text][v]={searchTerms}"/>
    <Url type="application/x-suggestions+json"
         template="<?php echo (\pachno\core\framework\Context::isProjectContext()) ? make_url('project_quicksearch', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey(), 'format' => 'json'), false) : make_url('quicksearch', array('format' => 'json'), false); ?>?fs[text][o]=%3A&amp;fs[text][v]={searchTerms}"/>
    <AdultContent>false</AdultContent>
    <OutputEncoding><?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?></OutputEncoding>
    <Contact>support@pach.no</Contact>
    <Query role="example" searchTerms="opensearch"/>
    <Attribution>No copyright</Attribution>
    <SyndicationRight>open</SyndicationRight>
</OpenSearchDescription>