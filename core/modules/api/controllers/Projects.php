<?php

    namespace pachno\core\modules\api\controllers;

    use pachno\core\framework,
        pachno\core\entities,
        pachno\core\entities\tables;
    use pachno\core\modules\project\Project;

    /** @noinspection PhpInconsistentReturnPointsInspection */

    /**
     * Main actions for the api module
     *
     * @property entities\Project[] $projects
     *
     * @Routes(name_prefix="api_projects_", url_prefix="/api/v1/projects")
     */
    class Projects extends ProjectNamespacedController
    {

        /**
         * List all projects
         *
         * @Route(name="list", url="/")
         * @param framework\Request $request
         */
        public function runProjects(framework\Request $request): framework\JsonOutput
        {
            $projects = framework\Context::getUser()->getAssociatedProjects();

            $projects_json = [];
            foreach ($projects as $project) {
                $projects_json[] = $project->toJSON(false);
            }

            return $this->renderJSON(['count' => count($projects), 'projects' => $projects_json]);
        }

        /**
         * Show details about one project
         *
         * @Route(name="get", url="/:project_id")
         *
         * @param framework\Request $request
         */
        public function runProject(framework\Request $request): framework\JsonOutput
        {
            if (!$this->selected_project instanceof entities\Project) {
                $this->getResponse()->setHttpStatus(404);
                return $this->renderJSON(['error' => 'This project does not exist']);
            }

            return $this->renderJSON(['project' => $this->selected_project->toJSON()]);
        }

        /**
         * Show details about one project
         *
         * @Route(name="issues_list", url="/:project_id/issues")
         *
         * @param framework\Request $request
         */
        public function runListIssues(framework\Request $request): framework\JsonOutput
        {
            $filters = ['project_id' => ['v' => $this->selected_project->getID(), 'o' => '=']];
            $filter_state = $request->getParameter('state', 'open');
            $filter_issuetype = $request->getParameter('issuetype', 'all');
            $filter_assigned_to = $request->getParameter('assigned_to', 'all');
            $filter_relation = $request->getParameter('relation');

            if (mb_strtolower($filter_state) != 'all') {
                $filters['state'] = array('o' => '=', 'v' => '');
                if (mb_strtolower($filter_state) == 'open')
                    $filters['state']['v'] = entities\Issue::STATE_OPEN;
                elseif (mb_strtolower($filter_state) == 'closed')
                    $filters['state']['v'] = entities\Issue::STATE_CLOSED;
            }

            if (mb_strtolower($filter_issuetype) != 'all') {
                $issuetype = entities\Issuetype::getByKeyish($filter_issuetype);
                if ($issuetype instanceof entities\Issuetype) {
                    $filters['issuetype'] = array('o' => '=', 'v' => $issuetype->getID());
                }
            }

            if (mb_strtolower($filter_assigned_to) != 'all') {
                $user_id = 0;
                switch (mb_strtolower($filter_assigned_to)) {
                    case 'me':
                        $user_id = framework\Context::getUser()->getID();
                        break;
                    case 'none':
                        $user_id = 0;
                        break;
                    default:
                        try {
                            $user = entities\User::findUser(mb_strtolower($filter_assigned_to));
                            if ($user instanceof entities\User)
                                $user_id = $user->getID();
                        } catch (\Exception $e) {

                        }
                        break;
                }

                $filters['assignee_user'] = array('o' => '=', 'v' => $user_id);
            }

            if (is_numeric($filter_relation) && in_array((string)$filter_relation, array('4', '3', '2', '1', '0'))) {
                $filters['relation'] = array('o' => '=', 'v' => $filter_relation);
            }

            foreach ($filters as $key => $options) {
                $filters[$key] = \pachno\core\entities\SearchFilter::createFilter($key, $options);
            }

            [$issues, $count] = entities\Issue::findIssues($filters, 50);
            $issues_json = [];
            foreach ($issues as $issue) {
                $issues_json[] = $issue->toJSON();
            }

            return $this->renderJSON(['count' => $count, 'issues' => $issues_json]);
        }

    }
