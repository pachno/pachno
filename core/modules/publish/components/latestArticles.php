<?php

    use pachno\core\entities\Article;
    use pachno\core\modules\publish\Publish;

    /**
     * @var Article[] $latest_articles
     * @var Publish $publish
     */

    $publish = \pachno\core\framework\Context::getModule('publish');

?>
<div class="container_div toggled">
    <div class="header" onclick="$(this).up().toggleClass('visible');">
        <?php echo __('Recently edited pages here'); ?>
    </div>
    <div class="toggle_info"><?php echo __('Click the header to show / hide'); ?></div>
    <div class="content">
        <?php if (count($latest_articles)): ?>
            <ul class="article_list">
                <?php foreach($latest_articles as $article): ?>
                    <li>
                        <div>
                            <?php echo image_tag('news_item_medium.png', array('style' => 'float: left;'), false, 'publish'); ?>
                            <?php echo link_tag(make_url('publish_article', array('article_name' => $article->getName())), \pachno\core\framework\Settings::get('allow_camelcase_links', 'publish', \pachno\core\framework\Context::getScope()->getID(), 0) ? $article->getSpacedName() : $article->getTitle()); ?>
                            <br>
                            <span><?php echo __('%time, by %user', array('%time' => \pachno\core\framework\Context::getI18n()->formatTime($article->getPostedDate(), 3), '%user' => '<b>'.(($article->getAuthor() instanceof \pachno\core\entities\common\Identifiable) ? '<a href="javascript:void(0);" onclick="Pachno.UI.Backdrop.show(\'' . make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $article->getAuthor()->getID())) . '\');">' . $article->getAuthor()->getName() . '</a>' : __('System')).'</b>')); ; ?></span>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div><?php echo __('There are no recent pages here'); ?></div>
        <?php endif; ?>
    </div>
</div>
