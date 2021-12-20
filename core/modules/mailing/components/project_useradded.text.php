* You have been invited to collaborate *

You have been invited to collaborate on the project "<?= $project->getName(); ?>" in Pachno, an open collaboration platform.
<?php echo $module->generateURL('home'); ?>

<?php if ($user->isActivated()): ?>
    Log in and visit the project:
    <?php echo $module->generateURL('project_dashboard', ['project_key' => $project->getKey()]); ?>
<?php else: ?>
    Before you can use your account, you need to confirm it by visiting the following link:
    <?php echo $link_to_activate; ?>

    You can log in and visit the project after activating your account:
    <?php echo $module->generateURL('project_dashboard', ['project_key' => $project->getKey()]); ?>
<?php endif; ?>
