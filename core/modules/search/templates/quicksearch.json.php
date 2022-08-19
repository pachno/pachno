<?php
    
    use pachno\core\entities\Project;
    
    /**
     * @var Project[] $projects
     */
    
    $results_json = [];
    if ($projects) {
        foreach ($projects as $project) {
            $results_json[] = \pachno\core\modules\search\Search::getQuicksearchJsonFromProject($project);
        }
    }
    echo json_encode($results_json);
