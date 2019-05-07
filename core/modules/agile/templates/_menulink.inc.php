<?php

    /** @var \pachno\core\entities\Link $link */

?>
<?php if ($link->hasUrl()): ?>
    <a id="<?php echo $link->getTargetType(); ?>_<?php echo $link->getTargetId(); ?>_links_<?php echo $link_id; ?>" class="list-item" href="<?= $link->getUrl(); ?>">
<?php else: ?>
    <div id="<?php echo $link->getTargetType(); ?>_<?php echo $link->getTargetId(); ?>_links_<?php echo $link_id; ?>" class="list-item">
<?php endif; ?>
    <span class="icon"></span>
    <span class="name">
        <?php if ($link->getDescription() != ''): ?>
        <?php else: ?>
        <?php endif; ?>
    </span>
    <?php if ($link->getTargetType() == 'wiki' && $link->hasUrl()): ?>
        <?php if ($pachno_routing->getCurrentRouteModule() == 'publish' && $pachno_request['article_name'] == $link->getUrl()): ?>
            <?php echo link_tag(make_url('publish_article', array('article_name' => strip_tags($link->getUrl()))), (($link->getDescription() != '') ? fa_image_tag('book') . \pachno\core\helpers\TextParser::parseText($link->getDescription(), false, null, array('embedded' => true)) : fa_image_tag('book') . strip_tags($link->getUrl())), array('class' => 'selected')); ?>
        <?php else: ?>
            <?php echo link_tag(make_url('publish_article', array('article_name' => strip_tags($link->getUrl()))), (($link->getDescription() != '') ? fa_image_tag('book') . \pachno\core\helpers\TextParser::parseText($link->getDescription(), false, null, array('embedded' => true)) : fa_image_tag('book') . strip_tags($link->getUrl()))); ?>
        <?php endif; ?>
    <?php elseif ($link->isInternalLink()): ?>
        <?php echo link_tag(make_url($link->getUrl()), (($link->getDescription() != '') ? fa_image_tag('envelope') . \pachno\core\helpers\TextParser::parseText($link->getDescription(), false, null, array('embedded' => true)) : fa_image_tag('envelope') . strip_tags($link->getUrl()))); ?>
    <?php elseif ($link->hasUrl()): ?>
        <?php echo link_tag($link->getUrl(), (($link->getDescription() != '') ? fa_image_tag('globe') . \pachno\core\helpers\TextParser::parseText($link->getDescription(), false, null, array('embedded' => true)) : fa_image_tag('globe') . strip_tags($link->getUrl()))); ?>
    <?php elseif ($link->getDescription() != ''): ?>
        <?php echo \pachno\core\helpers\TextParser::parseText($link->getDescription(), false, null, array('embedded' => true)); ?>
    <?php else: ?>
        &nbsp;
    <?php endif; ?>
    <?php if ($pachno_user->canEditMainMenu($link->getTargetType())): ?>
        <?php echo javascript_link_tag(fa_image_tag('close'), array('class' => 'delete-icon', 'style' => 'float: right;', 'onclick' => "Pachno.Main.Helpers.Dialog.show('".__('Please confirm')."', '".__('Do you really want to delete this link?')."', {yes: {click: function() {Pachno.Main.Link.remove('".make_url('remove_link', array('target_type' => $link->getTargetType(), 'target_id' => $link->getTargetId(), 'link_id' => $link_id))."', '{$link->getTargetType()}', '{$link->getTargetId()}', ".$link_id."); }}, no: {click: Pachno.Main.Helpers.Dialog.dismiss}})")); ?>
    <?php endif; ?>
<?php if ($link->hasUrl()): ?>
    </div>
<?php else: ?>
    </a>
<?php endif; ?>
<?php /* if ($pachno_user->canEditMainMenu()): ?>
    <tr id="<?php echo $link['target_type']; ?>_<?php echo $link['target_id']; ?>_links_<?php echo $link_id; ?>_remove_confirm" style="display: none;">
        <td colspan="2">
            <div class="rounded_box white shadowed" style="position: absolute; padding: 0 5px 5px 5px; font-size: 12px; width: 300px; z-index: 10001;">
                <div class="header_div" style="margin-top: 0;"><?php echo __('Are you sure?'); ?></div>
                <div class="content" style="padding: 3px;">
                    <?php echo __('Do you really want to remove this item from the menu?'); ?>
                    <div style="text-align: right;">
                        <?php echo javascript_link_tag(__('Yes'), array('onclick' => "$('{$link['target_type']}_{$link['target_id']}_links_{$link_id}_remove_confirm').toggle();Pachno.Main.Link.remove('".make_url('remove_link', array('target_type' => $link['target_type'], 'target_id' => $link['target_id'], 'link_id' => $link_id))."', '{$link['target_type']}', '{$link['target_id']}', ".$link_id.");")); ?> ::
                        <?php echo javascript_link_tag('<b>'.__('No').'</b>', array('onclick' => "$('{$link['target_type']}_{$link['target_id']}_links_{$link_id}_remove_confirm').toggle();")); ?>
                    </div>
                </div>
            </div>
        </td>
    </tr>
<?php endif; */ ?>
