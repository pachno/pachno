<div class="toggle-favourite <?php if ($starred) echo 'starred'; ?>" data-url="<?= $url; ?>">
    <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
    <?php echo fa_image_tag('star', array('class' => 'unsubscribed')); ?>
    <?php echo fa_image_tag('star', array('class' => 'subscribed')); ?>
    <?php if (isset($include_user) && $include_user): ?>
        <?php include_component('main/userdropdown', compact('user')); ?>
    <?php endif; ?>
</div>