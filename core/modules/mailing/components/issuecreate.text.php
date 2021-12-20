* <?php echo $issue->getFormattedTitle(true); ?> *
<?php echo __('Created by %name', array('%name' =>  $issue->getPostedBy()->getNameWithUsername())); ?>


* <?php echo __('Description') . ':'; ?> *
<?php echo \pachno\core\helpers\TextParser::parseText($issue->getDescription()); ?>

<?php if ($issue->getReproductionSteps()): ?>
    * <?php echo __('Reproduction steps') . ':'; ?> *
    <?php echo \pachno\core\helpers\TextParser::parseText($issue->getReproductionSteps()); ?>
<?php endif; ?>

<?php echo __('Show issue:') . ' ' . $module->getPrefixedUrl($issue->getUrl()); ?>

<?php echo __('Show %project project dashboard:', array('%project' => $issue->getProject()->getName())) . ' ' . $module->generateURL('project_dashboard', array('project_key' => $issue->getProject()->getKey())); ?>


<?php echo __('You were sent this notification email because you are related to the issue mentioned in this email.'); ?>

<?php echo __('To change when and how often we send these emails, update your account settings:') . ' ' . $module->generateURL('profile_account'); ?>
