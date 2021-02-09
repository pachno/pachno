<?php

    $can_remove = false;
    if ($mode == 'issue' && $issue->canRemoveAttachments())
        $can_remove = true;
    if ($mode == 'article' && $article->canEdit())
        $can_remove = true;

?>
<?php if ($file instanceof \pachno\core\entities\File): ?>
    <div id="<?php echo $base_id . '_' . $file_id; ?>" class="attachment <?php if ($file->isImage()) echo 'type-image'; ?>">
        <?php if ($file->isImage()): ?>
            <a href="<?php echo make_url('showfile', array('id' => $file_id)); ?>" target="_new" class="preview" title="<?php echo $file->getOriginalFilename(); ?>"><?php echo image_tag(make_url('showfile', array('id' => $file_id)), [], true); ?></a>
            <div class="information">
                <?= $file->getOriginalFilename(); ?>
            </div>
        <?php else: ?>
            <a href="<?php echo make_url('downloadfile', array('id' => $file_id)); ?>" title="<?php echo $file->getOriginalFilename(); ?>"><?php echo $file->getOriginalFilename(); ?></a>
        <?php endif; ?>
        <div class="information">
            <?= $file->getReadableFilesize(); ?>
        </div>
        <div class="actions-container">
            <a href="<?php echo make_url('downloadfile', array('id' => $file_id)); ?>" class="button icon secondary highlight">
                <?php echo fa_image_tag('download'); ?>
                <?php //echo ($file->hasDescription()) ? $file->getDescription() : $file->getOriginalFilename(); ?>
            </a>
            <?php if ($file->isImage()): ?>
                <?php echo javascript_link_tag(fa_image_tag('code'), ['onclick' => "Pachno.UI.Dialog.showModal('".__('Embedding this file in descriptions or comments')."', '".__('Use this tag to include this image: [[Image:%filename|thumb|Image description]]', ['%filename' => $file->getRealFilename()])."');", 'class' => 'button icon secondary']); ?>
            <?php endif; ?>
            <?php if ($can_remove): ?>
                <?php if ($mode == 'issue'): ?>
                    <?php echo javascript_link_tag(fa_image_tag('times', ['class' => 'icon']), ['onclick' => "Pachno.UI.Dialog.show('".__('Do you really want to detach this file?')."', '".__('If you detach this file, it will be deleted. This action cannot be undone. Are you sure you want to remove this file?')."', {yes: {click: function() {Pachno.Issues.File.remove('".make_url('issue_detach_file', ['issue_id' => $issue->getID(), 'file_id' => $file_id])."', ".$file_id."); }}, no: { click: Pachno.UI.Dialog.dismiss }});", 'class' => 'button secondary icon remove-button']); ?>
                <?php elseif ($mode == 'article'): ?>
                    <?php echo javascript_link_tag(fa_image_tag('times', ['class' => 'icon']), ['onclick' => "Pachno.UI.Dialog.show('".__('Do you really want to detach this file?')."', '".__('If you detach this file, it will be deleted. This action cannot be undone. Are you sure you want to remove this file?')."', {yes: {click: function() {Pachno.Main.detachFileFromArticle('".make_url('article_detach_file', ['article_name' => $article->getName(), 'file_id' => $file_id])."', ".$file_id.", ".$article->getID()."); }}, no: { click: Pachno.UI.Dialog.dismiss }});", 'class' => 'button secondary icon remove-button']); ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
