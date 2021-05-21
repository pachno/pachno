<?php

    /**
     * @var \pachno\core\entities\Scope[] $scopes
     */

?>
<div class="flexible-table">
    <div class="row header">
        <div class="column header name-container"><?= __('Name'); ?></div>
        <div class="column header"><?= __('Hostname(s)'); ?></div>
        <div class="column header numeric"><?= __('Project(s)'); ?></div>
        <div class="column header numeric"><?= __('Issue(s)'); ?></div>
        <div class="column header actions"></div>
    </div>
    <div class="body">
        <?php foreach ($scopes as $scope): ?>
            <?php include_component('configuration/scope', ['scope' => $scope]); ?>
        <?php endforeach; ?>
    </div>
</div>
<?php include_component('main/pagination', ['pagination' => $pagination]); ?>
