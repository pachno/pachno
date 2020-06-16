<?php

    $issues_array = array();
    if ($issues)
    {
        foreach ($issues as $issue)
        {
            $issues_array[] = $issue->getFormattedTitle(true);
        }
    }
    echo json_encode(array($searchterm, $issues_array));
