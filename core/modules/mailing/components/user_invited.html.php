<?php if ($user instanceof \pachno\core\entities\User): ?>
    <h3>
        You have been invited to collaborate<br>
    </h3>
    You have been invited to collaborate in Pachno, an open collaboration platform.<br>
    <br>
    Please visit the following link to confirm and activate your account:<br>
    <a href="<?php echo $link_to_activate; ?>"><?php echo $link_to_activate; ?></a><br>
<?php endif; ?>