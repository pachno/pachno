<?php

    $syntax = (isset($syntax)) ? $syntax : \pachno\core\framework\Settings::SYNTAX_MW;
    if (is_numeric($syntax)) $syntax = \pachno\core\framework\Settings::getSyntaxClass($syntax);

    switch ($syntax)
    {
        case 'mw':
            $syntaxname = __('Mediawiki');
            break;
        case 'md':
        case 'pt':
            $syntaxname = __('Markdown');
            break;
    }
    $base_id = (isset($area_id)) ? $area_id : $area_name;
    $invisible = (isset($invisible)) ? $invisible : false;
    $mentionable = isset($target_type) && isset($target_id);
    $syntax_options = [
        'md' => ['name' => 'Markdown', 'description' => __('GitHub-flavor Markdown syntax')],
        'mw' => ['name' => 'Mediawiki', 'description' => __('Mediawiki / Wikipedia-style syntax for advanced formatting and templating')],
    ]

?>
<div class="textarea_container syntax_<?php echo $syntax; ?>">
    <div class="syntax_picker_container">
        <div class="fancy-dropdown-container">
            <div class="fancy-dropdown">
                <label><?php echo __('Formatting'); ?></label>
                <span class="value"><?= $syntaxname ?></span>
                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                <div class="dropdown-container">
                    <div class="list-mode">
                        <?php foreach ($syntax_options as $syntax_key => $syntax_description): ?>
                            <input type="radio" value="<?= \pachno\core\framework\Settings::getSyntaxValue($syntax_key); ?>" class="fancy-checkbox" name="<?= $area_name; ?>_syntax" id="<?= $area_name; ?>_syntax_<?= $syntax_key; ?>" <?php if ($syntax == $syntax_key) echo 'checked'; ?>>
                            <label for="<?= $area_name; ?>_syntax_<?= $syntax_key; ?>" class="list-item multiline">
                                <span class="icon"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?></span>
                                <span class="name">
                                    <span class="title value"><?= $syntax_description['name']; ?></span>
                                    <span class="additional_information"><?= $syntax_description['description']; ?></span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <textarea name="<?php echo $area_name; ?>" id="<?php echo $base_id; ?>" <?php if ($mentionable): ?> data-target-type="<?php echo $target_type; ?>" data-target-id="<?php echo $target_id; ?>" <?php endif; ?> class="syntax_<?php echo $syntax; ?> trumbowyggable <?php if ($mentionable) echo ' mentionable'; ?> <?php if ($invisible) echo ' invisible'; ?>" style="<?php if (isset($height)) echo 'height: '.$height; ?>; <?php if (isset($width)) echo "width: {$width};"; ?>" <?php if (isset($placeholder)): ?>placeholder="<?= $placeholder; ?>"<?php endif; ?>><?php echo $value; ?></textarea>
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
