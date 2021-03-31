<?php

    use pachno\core\entities\tables\Permissions;

    /**
     * @var \pachno\core\entities\User $pachno_user
     * @var \pachno\core\entities\Project $project
     */

    if ($pachno_user->hasProjectPermission(Permissions::PERMISSION_PROJECT_ACCESS_BOARDS, $project)) {
        echo link_tag(make_url('agile_index', array('project_key' => $project->getKey())), fa_image_tag('chalkboard').'<span>'.__('Boards').'</span>', array('class' => 'button secondary'));
    }
