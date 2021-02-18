<li>
    <?= link_tag($duplicate_issue->getUrl(), ($duplicate_issue->getIssueType()->isTask() ? $duplicate_issue->getTitle() : $duplicate_issue->getFormattedTitle())); ?>
</li>