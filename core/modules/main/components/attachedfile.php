<?php

    use pachno\core\entities\Article;
    use pachno\core\entities\Issue;
    use pachno\core\entities\User;
    use pachno\core\entities\File;

    /**
     * @var Issue $issue
     * @var Article $article
     * @var File $file
     * @var User $pachno_user
     */

    $can_remove = false;
    if ($mode == 'issue' && $issue->canRemoveAttachment($pachno_user, $file)) {
        $can_remove = true;
    } elseif ($mode == 'article' && ($article->canEdit() || ($article->getProject() instanceof \pachno\core\entities\Project && $pachno_user->canManageProject($article->getProject())) || ($file->getUploadedBy() instanceof User && $file->getUploadedBy()->getID() === $pachno_user->getID()))) {
        $can_remove = true;
    }

?>
<?php if ($file instanceof File): ?>
    <div id="attachment_<?= $file_id; ?>" class="attachment <?php if ($file->isImage()) echo 'type-image'; ?>" data-attachment data-file-id="<?= $file->getId(); ?>">
        <?php if ($file->isImage()): ?>
            <a href="<?php echo make_url('showfile', array('id' => $file_id)); ?>" target="_new" class="preview" title="<?php echo $file->getOriginalFilename(); ?>"><?php echo image_tag(make_url('showfile', array('id' => $file_id)), [], true); ?></a>
            <div class="information">
                <?= $file->getOriginalFilename(); ?>
            </div>
        <?php else: ?>
            <a href="<?php echo make_url('downloadfile', array('id' => $file_id)); ?>" title="<?php echo $file->getOriginalFilename(); ?>">
                <?= fa_image_tag($file->getIcon(), ['class' => 'icon'], 'far'); ?>
                <span class="name"><?php echo $file->getOriginalFilename(); ?></span>
            </a>
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
                <?php if ($mode == 'issue'): ?>
                    <?php echo javascript_link_tag(fa_image_tag('code'), ['onclick' => "Pachno.UI.Dialog.showModal('" . __('Embedding this file in descriptions or comments') . "', '" . __('Use this tag to include this image: [[Image:%filename|thumb|Image description]]', ['%filename' => $file->getRealFilename()]) . "');", 'class' => 'button icon secondary']); ?>
                    <button class="button secondary icon <?= ($issue->getCoverImageFile() instanceof File && $issue->getCoverImageFile()->getId() == $file->getId()) ? 'trigger-clear-cover' : 'trigger-set-cover'; ?>" data-dynamic-field-value data-field="cover_image_file" data-file-id="<?= $file->getId(); ?>" data-issue-id="<?= $issue->getId(); ?>">
                        <?= fa_image_tag(($issue->getCoverImageFile() instanceof File && $issue->getCoverImageFile()->getId() == $file->getId()) ? 'minus-square' : 'images', ['class' => 'icon'], 'far'); ?>
                    </button>
                <?php else: ?>
                    <button class="button secondary highlight icon trigger-embed" data-url="<?= $file->getURL(false); ?>">
                        <?= fa_image_tag('file-import'); ?>
                    </button>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($can_remove): ?>
                <?php if ($mode == 'issue'): ?>
                    <?php echo javascript_link_tag(fa_image_tag('times', ['class' => 'icon']), ['onclick' => "Pachno.UI.Dialog.show('" . __('Do you really want to detach this file?') . "', '" . __('If you detach this file, it will be deleted. This action cannot be undone. Are you sure you want to remove this file?') . "', {yes: {click: function() { Pachno.trigger(Pachno.EVENTS.issue.removeFile, { url: '" . make_url('issue_detach_file', ['issue_id' => $issue->getID(), 'file_id' => $file_id]) . "', file_id: " . $file_id . ", issue_id: " . $issue->getId() . "}); }}, no: { click: Pachno.UI.Dialog.dismiss }});", 'class' => 'button secondary icon remove-button']); ?>
                <?php elseif ($mode == 'article'): ?>
                    <?php echo javascript_link_tag(fa_image_tag('times', ['class' => 'icon']), ['onclick' => "Pachno.UI.Dialog.show('" . __('Do you really want to detach this file?') . "', '" . __('If you detach this file, it will be deleted. This action cannot be undone. Are you sure you want to remove this file?') . "', {yes: {click: function() { Pachno.trigger(Pachno.EVENTS.article.removeFile, { url: '" . make_url('article_detach_file', ['article_id' => $article->getID(), 'file_id' => $file_id]) . "', file_id: " . $file_id . ", article_id: " . $article->getID() . "}); }}, no: { click: Pachno.UI.Dialog.dismiss }});", 'class' => 'button secondary icon remove-button']); ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
