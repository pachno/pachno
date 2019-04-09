<?php

    $pachno_response->addBreadcrumb(__('Project files'), make_url('project_files', array('project_key' => $selected_project->getKey())));
    $pachno_response->setTitle(__('"%project_name" files', array('%project_name' => $selected_project->getName())));

?>
