<?php

    if ($pachno_user->hasProjectPageAccess('project_planning', $project) || $pachno_user->hasProjectPageAccess('project_only_planning', $project)) {
        echo link_tag(make_url('agile_index', array('project_key' => $project->getKey())), fa_image_tag('chalkboard').'<span>'.__('Planning & boards').'</span>', array('class' => 'button secondary'));
    }
