"<?php echo __("Project"); ?>","<?php echo __("Issue number"); ?>","<?php echo __("Issue title"); ?>","<?php echo __("Assigned to"); ?>","<?php echo __("Status"); ?>","<?php echo __('Category'); ?>","<?php echo __('Priority'); ?>","<?php echo __('Reproducability'); ?>","<?php echo __('Severity'); ?>","<?php echo __("Resolution"); ?>","<?php echo __('Targetted for'); ?>","<?php echo __("Last updated"); ?>","<?php echo __("Percentage complete"); ?>","<?php echo __("Time estimated");?>","<?php echo __("Time spent"); ?>","<?php echo __("User pain"); ?>","<?php echo __("Votes"); ?>","<?php echo __("Description"); ?>","<?php echo __("Reproduction Steps"); ?>","<?php echo __("Comments"); ?>"
<?php $search_object->setIssuesPerPage(0); ?>
<?php if ($search_object->getNumberOfIssues()): ?>
<?php foreach ($search_object->getIssues() as $issue): ?>
<?php 
/* Deal with issue assignee */
$temp = $issue->getAssignee();
if ($temp instanceof \pachno\core\entities\User && !($temp->isDeleted()))
{
    $assignee = $temp->getBuddyname();
}
elseif ($temp instanceof \pachno\core\entities\Team) {
    $assignee = $temp->getName();
}
else
{
    $assignee = '-';
}

/* Deal with issue status */
$temp = $issue->getStatus();
if ($temp instanceof \pachno\core\entities\Status)
{
    $status = $temp->getName();
}
else
{
    $status = '-';
}

/* Deal with issue priority */
$temp = $issue->getPriority();
if ($temp instanceof \pachno\core\entities\Priority)
{
    $priority = $temp->getName();
}
else
{
    $priority = '-';
}

/* Deal with issue resolution */
$temp = $issue->getResolution();
if ($temp instanceof \pachno\core\entities\Resolution)
{
    $resolution = $temp->getName();
}
else
{
    $resolution = '-';
}

/* Deal with issue category */
$temp = $issue->getCategory();
if ($temp instanceof \pachno\core\entities\Category)
{
    $category = $temp->getName();
}
else
{
    $category = '-';
}

/* Deal with issue reproducability */
$temp = $issue->getReproducability();
if ($temp instanceof \pachno\core\entities\Reproducability)
{
    $reproducability = $temp->getName();
}
else
{
    $reproducability = '-';
}

/* Deal with issue severity */
$temp = $issue->getSeverity();
if ($temp instanceof \pachno\core\entities\Severity)
{
    $severity = $temp->getName();
}
else
{
    $severity = '-';
}

/* Deal with issue milestone */
$temp = $issue->getMilestone();
if ($temp instanceof \pachno\core\entities\Milestone)
{
    $milestone = $temp->getName();
}
else
{-
    $milestone = '-';
}

/* Deal with issue Reproduction steps */
$temp = $issue->getReproductionSteps();
if (!empty($temp))
{
    $reproductionsteps = $issue->getReproductionSteps();
}
else
{
    $reproductionsteps = '-';
}

unset($temp);

$percent = $issue->getPercentCompleted().'%';

?>

"<?php echo str_replace('"', '\"', html_entity_decode($issue->getProject()->getName(), ENT_QUOTES, \pachno\core\framework\Context::getI18n()->getCharset())); ?>","<?php echo html_entity_decode($issue->getFormattedIssueNo(), ENT_QUOTES, \pachno\core\framework\Context::getI18n()->getCharset()); ?>","<?php echo str_replace('"', '\"', html_entity_decode(strip_tags($issue->getTitle()), ENT_QUOTES, \pachno\core\framework\Context::getI18n()->getCharset())); ?>","<?php echo html_entity_decode($assignee, ENT_QUOTES, \pachno\core\framework\Context::getI18n()->getCharset()); ?>","<?php echo html_entity_decode($status, ENT_QUOTES, \pachno\core\framework\Context::getI18n()->getCharset()); ?>","<?php echo html_entity_decode($category, ENT_QUOTES, \pachno\core\framework\Context::getI18n()->getCharset()); ?>","<?php echo html_entity_decode($priority, ENT_QUOTES, \pachno\core\framework\Context::getI18n()->getCharset()); ?>","<?php echo html_entity_decode($reproducability, ENT_QUOTES, \pachno\core\framework\Context::getI18n()->getCharset()); ?>","<?php echo html_entity_decode($severity, ENT_QUOTES, \pachno\core\framework\Context::getI18n()->getCharset()); ?>","<?php echo html_entity_decode($resolution, ENT_QUOTES, \pachno\core\framework\Context::getI18n()->getCharset()); ?>","<?php echo html_entity_decode($milestone, ENT_QUOTES, \pachno\core\framework\Context::getI18n()->getCharset()); ?>","<?php echo \pachno\core\framework\Context::getI18n()->formatTime($issue->getLastUpdatedTime(), 21); ?>","<?php echo $percent; ?>","<?php echo \pachno\core\entities\Issue::getFormattedTime($issue->getEstimatedTime()); ?>","<?php echo $issue->getFormattedTime($issue->getSpentTime(true, true));?>","<?php echo $issue->getUserpain(); ?>","<?php echo $issue->getVotes(); ?>","<?php echo str_replace('"', '\"', html_entity_decode($issue->getDescription(), ENT_QUOTES, \pachno\core\framework\Context::getI18n()->getCharset())); ?> ", "<?php echo str_replace('"', '\"', html_entity_decode($reproductionsteps, ENT_QUOTES, \pachno\core\framework\Context::getI18n()->getCharset())); ?>", "

<?php if ($issue->getCommentCount() == 0)
{
echo '-' ;
} ?>
<?php foreach (\pachno\core\entities\Comment::getComments($issue->getFormattedIssueNo(), '1') as $comment): ?>
        <?php

            $options = compact('comment', 'comment_count_div');
            if (isset($issue))
                echo $comment->getPostedBy() .' : '. $comment->getContent(). ' ';
        ?>
    <?php endforeach; ?>"
<?php endforeach; ?>
<?php endif; ?>
