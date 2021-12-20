<?php if ($user instanceof \pachno\core\entities\User): ?>
    <h3>
        You have been invited to collaborate<br>
    </h3>
    You have been invited to collaborate on the project "<?= $project->getName(); ?>" in Pachno, an open collaboration platform.<br>
    <br>
    <?php if ($user->isActivated()): ?>
        Log in and visit the project:<br>
        <?php echo link_tag($module->generateURL('project_dashboard', ['project_key' => $project->getKey()])); ?><br>
    <?php else: ?>
        Please visit the following link to confirm and activate your account:<br>
        <a href="<?php echo $link_to_activate; ?>"><?php echo $link_to_activate; ?></a><br>
        <br>
        You can log in and visit the project after activating your account:<br>
        <?php echo link_tag($module->generateURL('project_dashboard', ['project_key' => $project->getKey()])); ?><br>
    <?php endif; ?>
<?php endif; ?>