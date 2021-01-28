<?php

    use pachno\core\framework\Settings;

    /**
     * @var int|string $syntax
     * @var string $syntaxClass
     * @var string $base_id
     * @var string $area_name
     * @var bool $invisible
     * @var bool $mentionable
     * @var bool $markuppable
     * @var string $target_type
     * @var int|string $target_id
     */

?>
<div class="textarea_container syntax_<?php echo $syntaxClass; ?>">
    <input type="hidden" value="<?= Settings::getSyntaxValue($syntaxClass); ?>" name="<?= $area_name; ?>_syntax" id="<?= $area_name; ?>_syntax_<?= $syntaxClass; ?>">
    <textarea
        name="<?php echo $area_name; ?>"
        data-upload-url="<?php echo make_url('upload_file'); ?>"
        data-status-text="<?= __('Github-flavored markdown supported. Drop images on the textarea to attach an image.'); ?>"
        id="<?php echo $base_id; ?>"
        data-editable-textarea
        <?php if (isset($field)): ?>
            data-field="<?= $field; ?>"
            data-issue-id="<?= $target_id; ?>"
        <?php endif; ?>
        <?php if ($mentionable): ?>
            data-target-type="<?php echo $target_type; ?>"
            data-target-id="<?php echo $target_id; ?>"
        <?php endif; ?>
        class="syntax_<?php echo $syntaxClass; ?> <?php if ($markuppable) echo 'markuppable'; ?> <?php if ($mentionable) echo ' mentionable'; ?> <?php if ($invisible) echo ' invisible'; ?>"
        style="<?php if (isset($height)) echo 'height: '.$height; ?>; <?php if (isset($width)) echo "width: {$width};"; ?>"
        <?php if (isset($placeholder)): ?>
            placeholder="<?= $placeholder; ?>"
        <?php endif; ?>
    ><?php echo $value; ?></textarea>
</div>
