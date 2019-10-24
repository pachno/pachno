<?php

/**
 * @var \pachno\core\entities\Project $project
 */

?>
<div class="form-container">
    <div class="form-row">
        <h3>
            <span><?= __('Client'); ?></span>
            <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                <button class="button secondary" onclick="Pachno.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', ['key' => 'edit_client', 'project_id' => $project->getID()]); ?>');">
                    <?= fa_image_tag('plus', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Create client'); ?></span>
                </button>
            <?php endif; ?>
        </h3>
        <div class="helper-text">
            <div class="image-container"><?= image_tag('/unthemed/onboarding_project_client_icon.png', [], true); ?></div>
            <span class="description">
                <?= __('If this project is related to an external client, add that client here to grant access.'); ?>
            </span>
        </div>
    </div>
    <div class="form-row">
        <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
            <div class="fancy-dropdown-container">
                <div class="fancy-dropdown">
                    <label><?= __('Client'); ?></label>
                    <span class="value"><?= __('No client assigned'); ?></span>
                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                    <div class="dropdown-container list-mode">
                        <?php if (count(\pachno\core\entities\Client::getAll())): ?>
                            <input type="radio" class="fancy-checkbox" id="client_id_checkbox_0" name="client_id" value="0" <?php if (!$project->hasClient()) echo 'checked'; ?>>
                            <label for="client_id_checkbox_0" class="list-item">
                                <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                <span class="name value"><?= __('No client assigned'); ?></span>
                            </label>
                            <?php foreach (\pachno\core\entities\Client::getAll() as $client): ?>
                                <input type="radio" class="fancy-checkbox" id="client_id_checkbox_<?= $client->getID(); ?>" name="client_id" value="<?= $client->getID(); ?>" <?php if ($project->hasClient() && $project->getClient()->getID() == $client->getID()) echo 'checked'; ?>>
                                <label for="client_id_checkbox_<?= $client->getID(); ?>" class="list-item">
                                    <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                    <span class="name value"><?= $client->getName(); ?></span>
                                </label>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <input type="radio" class="fancy-checkbox" id="client_id_checkbox_0" name="client_id" value="0">
                            <label for="client_id_checkbox_0" class="list-item disabled">
                                <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                <span class="name value"><?= __('No clients exist'); ?></span>
                            </label>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php if ($project->getClient() == null): echo __('No client'); else: echo $project->getClient()->getName(); endif; ?>
            <label for="client"><?= __('Client'); ?></label>
        <?php endif; ?>
    </div>
</div>
