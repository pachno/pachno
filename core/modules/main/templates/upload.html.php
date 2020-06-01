<?php if (isset($error)): ?>
    frameElement.parent.Pachno.UI.Message.error('<?php echo $error; ?>');
<?php else: ?>
    frameElement.parent.Pachno.UI.Message.success('<?php echo __('The file "%filename" was uploaded successfully'); ?>');
<?php endif; ?>
