<?php

    use pachno\core\entities\Permission;
    use pachno\core\entities\tables\Permissions;
    use pachno\core\modules\publish\Publish;

    /**
     * @var \pachno\core\entities\User $pachno_user
     * @var \pachno\core\entities\Project $project
     */

    if ($pachno_user->hasProjectPermission(Permission::PERMISSION_PROJECT_ACCESS_DOCUMENTATION, $project)) {
        if ($project->hasWikiURL()) {
            echo link_tag($project->getWikiURL(), fa_image_tag('book') . '<span>'.__('Documentation').'</span>', array('target' => 'blank', 'class' => 'button secondary'));
        } else {
            echo link_tag(Publish::getArticleLink('Main Page', $project), fa_image_tag('book').'<span>'.__('Documentation').'</span>', array('class' => 'button secondary'));
        }
    }
