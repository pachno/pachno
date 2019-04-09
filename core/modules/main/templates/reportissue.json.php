<?php

    if (isset($issue) && $issue instanceof \pachno\core\entities\Issue)
    {
        echo json_encode(array('issue' => $issue->toJSON()));
    }
    elseif (count($errors))
    {
        echo json_encode(array('error' => join(', ', $errors)));
    }
    elseif (!$pachno_request->isPost())
    {
        echo json_encode(array('error' => __('This page cannot be loaded')));
    }
    else
    {
        echo json_encode(array('error' => __('There was an error creating this issue')));
    }
