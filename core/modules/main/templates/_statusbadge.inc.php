<?php

    use pachno\core\entities\Status;

    /** @var Status $status */

?>
<div class="status-badge"
     style="background-color: <?php echo ($status instanceof Status) ? $status->getColor() : '#FFF'; ?>; color: <?php echo ($status instanceof Status) ? $status->getTextColor() : 'inherit'; ?>;"
     title="<?php echo ($status instanceof Status) ? __($status->getName()) : __('Status not determined'); ?>">
    <span><?php echo ($status instanceof Status) ? $status->getName() : __('Unknown'); ?></span>
</div>
