<?php

    use pachno\core\framework\Settings;

    $syntax = (isset($syntax)) ? $syntax : Settings::SYNTAX_MD;
    if (is_numeric($syntax)) {
        $syntaxClass = Settings::getSyntaxClass($syntax);
    } else {
        $syntaxClass = $syntax;
    }

    $base_id = (isset($area_id)) ? $area_id : $area_name;
    $invisible = (isset($invisible)) ? $invisible : false;
    $mentionable = isset($target_type) && isset($target_id);

?>
<div class="textarea_container syntax_<?php echo $syntaxClass; ?>">
    <input type="hidden" value="<?= Settings::getSyntaxValue($syntaxClass); ?>" name="<?= $area_name; ?>_syntax" id="<?= $area_name; ?>_syntax_<?= $syntaxClass; ?>">
    <textarea name="<?php echo $area_name; ?>" data-upload-url="<?php echo make_url('upload_file'); ?>" data-status-text="<?= __('Github-flavored markdown supported. Drop images on the textarea to attach an image.'); ?>" id="<?php echo $base_id; ?>" <?php if ($mentionable): ?> data-target-type="<?php echo $target_type; ?>" data-target-id="<?php echo $target_id; ?>" <?php endif; ?> class="syntax_<?php echo $syntaxClass; ?> <?php if ($syntaxClass == Settings::getSyntaxClass(Settings::SYNTAX_MD)) echo 'markuppable'; ?> <?php if ($mentionable) echo ' mentionable'; ?> <?php if ($invisible) echo ' invisible'; ?>" style="<?php if (isset($height)) echo 'height: '.$height; ?>; <?php if (isset($width)) echo "width: {$width};"; ?>" <?php if (isset($placeholder)): ?>placeholder="<?= $placeholder; ?>"<?php endif; ?>><?php echo $value; ?></textarea>
</div>
<script type="text/javascript">
    //require(['pachno/index', 'domReady', 'mention'], function (Pachno, domReady, mention) {
    //    domReady(function () {
    //        $("<?php //echo $base_id; ?>//").on('focus', function (e) {
    //            Pachno.Main.initializeMentionable(e.target);
    //            var ec = this.up('.editor_container');
    //            if (ec != undefined)
    //                ec.addClass('focussed');
    //        });
    //        $("<?php //echo $base_id; ?>//").on('blur', function (e) {
    //            var ec = this.up('.editor_container');
    //            if (ec != undefined)
    //                ec.removeClass('focussed');
    //        });
    //        Pachno.UI.MarkitUp([$("<?php //echo $base_id; ?>//")], '#article-editor-header');
    //    });
    //});
</script>
