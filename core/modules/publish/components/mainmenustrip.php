<?php

use pachno\core\framework;

/**
 * @var \pachno\core\entities\User $pachno_user
 */
?>
<div class="name-container shaded">
    <div class="form-container">
        <form id="documentation-search">
            <div class="form-row">
                <div class="search-container">
                    <label for="documentation-search-input" class="icon"><?= fa_image_tag('search'); ?></label>
                    <input id="documentation-search-input" type="search" name="value" placeholder="<?= __('Search documentation') ;?>">
                </div>
            </div>
        </form>
    </div>
</div>
<div class="spacer"></div>
<?php if ($pachno_user->isAuthenticated()): ?>
    <?php include_component('publish/headeractions'); ?>
<?php endif; ?>
