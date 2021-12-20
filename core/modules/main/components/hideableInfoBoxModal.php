<?php if ($show_box): ?>
    <div class="fullpage_backdrop infobox_modal" id="infobox_<?= $key; ?>">
        <div class="fullpage_backdrop_content">
            <div class="backdrop_box large">
                <div class="backdrop_detail_header">
                    <span><?= $title; ?></span>
                </div>
                <div class="backdrop_detail_content onboarding-popup">
                    <?php include_component($template, $options); ?>
                </div>
                <div class="form-container">
                    <form id="close_me_<?= $key; ?>_form" action="<?= make_url('hide_infobox', array('key' => $key)); ?>" method="post" accept-charset="<?= \pachno\core\framework\Settings::getCharset(); ?>" data-key="<?= $key;?>" data-simple-submit data-auto-close-container>
                        <div class="form-row submit-container">
                            <span class="explanation">
                                <input type="checkbox" value="1" name="dont_show" id="close_me_<?= $key; ?>" class="fancy-checkbox">
                                <label for="close_me_<?= $key; ?>">
                                    <span class="icon"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?></span>
                                    <span><?= __("Don't show this again"); ?></span>
                                </label>
                            </span>
                            <button type="submit" class="button primary">
                                <span><?= $button_label; ?></span>
                                <?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
