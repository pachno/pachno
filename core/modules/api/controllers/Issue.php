<?php

    namespace pachno\core\modules\api\controllers;

    use pachno\core\framework,
        pachno\core\entities;

    /** @noinspection PhpInconsistentReturnPointsInspection */

    /**
     * Workflow actions for the api module
     *
     * @property entities\Project[] $projects
     * @property entities\Issuetype[] $issuetypes
     * @property entities\Issue $issue
     * @property array $json
     *
     * @Routes(name_prefix="api_issue_", url_prefix="/api/v1/projects/:project_id/issues/:issue_no")
     */
    class Issue extends ProjectNamespacedController
    {

        /**
         * The currently selected project in actions where there is one
         *
         * @param framework\Request $request
         * @param string $action
         */
        public function preExecute(framework\Request $request, $action)
        {
            parent::preExecute($request, $action);

            try {
                $issue = entities\Issue::getIssueFromLink($request['issue_no']);
            } catch (\Exception $e) {
                $this->getResponse()->setHttpStatus(500);
                return $this->renderJSON(array('error' => 'An exception occurred: ' . $e));
            }

            if (!$issue instanceof entities\Issue || $issue->getProject()->getID() != $this->selected_project->getID()) {
                $this->getResponse()->setHttpStatus(404);
                return $this->renderJSON(array('error' => 'Issue not found or not valid for this project'));
            }

            $this->issue = $issue;
        }

        /**
         * @Route(name="get", url="/", methods="GET")
         * @param framework\Request $request
         */
        public function runViewIssue(framework\Request $request): framework\JsonOutput
        {
            return $this->renderJSON($this->issue);
        }

        /**
         * @Route(name="update", url="/", methods="POST")
         * @param framework\Request $request
         */
        public function runUpdateIssueDetails(framework\Request $request): framework\JsonOutput
        {
            if ($this->selected_project->isArchived()) {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(['error' => $this->getI18n()->__('This issue cannot be updated')]);
            }

            try {
                $i18n = framework\Context::getI18n();

                $workflow_transition = null;
                if ($passed_transition = $request['workflow_transition']) {
                    //echo "looking for transition ";
                    $key = str_replace(' ', '', mb_strtolower($passed_transition));
                    //echo $key . "\n";
                    foreach ($this->issue->getAvailableWorkflowTransitions() as $transition) {
                        //echo str_replace(' ', '', mb_strtolower($transition->getName())) . "?";
                        if (mb_strpos(str_replace(' ', '', mb_strtolower($transition->getName())), $key) !== false) {
                            $workflow_transition = $transition;
                            //echo "found transition " . $transition->getID();
                            break;
                        }
                        //echo "no";
                    }

                    if (!$workflow_transition instanceof entities\WorkflowTransition)
                        throw new \Exception("This transition ({$key}) is not valid");
                }
                $fields = $request->getRawParameter('fields', array());
                $return_values = array();
                if ($workflow_transition instanceof entities\WorkflowTransition) {
                    foreach ($fields as $field_key => $field_value) {
                        $classname = "\\pachno\\core\\entities\\" . ucfirst($field_key);
                        $method = "set" . ucfirst($field_key);
                        $choices = $classname::getAll();
                        $found = false;
                        foreach ($choices as $choice_key => $choice) {
                            if (mb_strpos(str_replace(' ', '', mb_strtolower($choice->getName())), str_replace(' ', '', mb_strtolower($field_value))) !== false) {
                                $request->setParameter($field_key . '_id', $choice->getId());
                                break;
                            }
                        }
                    }
                    $request->setParameter('comment_body', $request['message']);
                    $return_values['applied_transition'] = $workflow_transition->getName();
                    if ($workflow_transition->validateFromRequest($request)) {
                        $retval = $workflow_transition->transitionIssueToOutgoingStepFromRequest($this->issue, $request);
                        $return_values['transition_ok'] = ($retval === false) ? false : true;
                    } else {
                        $return_values['transition_ok'] = false;
                        $return_values['message'] = "Please pass all information required for this transition";
                    }
                } elseif ($this->issue->isUpdateable()) {
                    foreach ($fields as $field_key => $field_value) {
                        try {
                            if (in_array($field_key, array_merge(array('title', 'state'), entities\Datatype::getAvailableFields(true)))) {
                                switch ($field_key) {
                                    case 'state':
                                        $this->issue->setState(($field_value == 'open') ? entities\Issue::STATE_OPEN : entities\Issue::STATE_CLOSED);
                                        break;
                                    case 'title':
                                        if ($field_value != '')
                                            $this->issue->setTitle($field_value);
                                        else
                                            throw new \Exception($i18n->__('Invalid title'));
                                        break;
                                    case 'shortname':
                                    case 'description':
                                    case 'reproduction_steps':
                                        $method = "set" . ucfirst($field_key);
                                        $this->issue->$method($field_value);
                                        break;
                                    case 'status':
                                    case 'resolution':
                                    case 'reproducability':
                                    case 'priority':
                                    case 'severity':
                                    case 'category':
                                        $classname = "\\pachno\\core\\entities\\" . ucfirst($field_key);
                                        $method = "set" . ucfirst($field_key);
                                        $choices = $classname::getAll();
                                        $found = false;
                                        foreach ($choices as $choice_key => $choice) {
                                            if (str_replace(' ', '', mb_strtolower($choice->getName())) == str_replace(' ', '', mb_strtolower($field_value))) {
                                                $this->issue->$method($choice);
                                                $found = true;
                                            }
                                        }
                                        if (!$found) {
                                            throw new \Exception('Could not find this value');
                                        }
                                        break;
                                    case 'percent_complete':
                                        $this->issue->setPercentCompleted($field_value);
                                        break;
                                    case 'owner':
                                    case 'assignee':
                                        $set_method = "set" . ucfirst($field_key);
                                        $unset_method = "un{$set_method}";
                                        switch (mb_strtolower($field_value)) {
                                            case 'me':
                                                $this->issue->$set_method(framework\Context::getUser());
                                                break;
                                            case 'none':
                                                $this->issue->$unset_method();
                                                break;
                                            default:
                                                try {
                                                    $user = entities\User::findUser(mb_strtolower($field_value));
                                                    if ($user instanceof entities\User)
                                                        $this->issue->$set_method($user);
                                                } catch (\Exception $e) {
                                                    throw new \Exception('No such user found');
                                                }
                                                break;
                                        }
                                        break;
                                    case 'estimated_time':
                                    case 'spent_time':
                                        $set_method = "set" . ucfirst(str_replace('_', '', $field_key));
                                        $this->issue->$set_method($field_value);
                                        break;
                                    case 'milestone':
                                        $found = false;
                                        foreach ($this->selected_project->getMilestones() as $milestone) {
                                            if (str_replace(' ', '', mb_strtolower($milestone->getName())) == str_replace(' ', '', mb_strtolower($field_value))) {
                                                $this->issue->setMilestone($milestone->getID());
                                                $found = true;
                                            }
                                        }
                                        if (!$found) {
                                            throw new \Exception('Could not find this milestone');
                                        }
                                        break;
                                    default:
                                        throw new \Exception($i18n->__('Invalid field'));
                                }
                            }
                            $return_values[$field_key] = array('success' => true);
                        } catch (\Exception $e) {
                            $return_values[$field_key] = array('success' => false, 'error' => $e->getMessage());
                        }
                    }
                }

                if (!$workflow_transition instanceof entities\WorkflowTransition)
                    $this->issue->getWorkflow()->moveIssueToMatchingWorkflowStep($this->issue);

                if (!array_key_exists('transition_ok', $return_values) || $return_values['transition_ok']) {
                    $comment = new entities\Comment();
                    $comment->setContent($request->getParameter('message', null, false));
                    $comment->setPostedBy(framework\Context::getUser()->getID());
                    $comment->setTargetID($this->issue->getID());
                    $comment->setTargetType(entities\Comment::TYPE_ISSUE);
                    $comment->setModuleName('core');
                    $comment->setIsPublic(true);
                    $comment->setSystemComment(false);
                    $comment->save();
                    $this->issue->setSaveComment($comment);
                    $this->issue->save();
                }

                return $this->renderJSON($return_values);
            } catch (\Exception $e) {
                $this->getResponse()->setHttpStatus(400);
                return $this->renderJSON(['error' => $e->getMessage()]);
            }
        }

        /**
         * @Route(name="list_transitions", url="/transitions")
         * @param framework\Request $request
         */
        public function runListTransitions(framework\Request $request): framework\JsonOutput
        {
            $transitions = [];
            foreach ($this->issue->getAvailableWorkflowTransitions() as $transition) {
                $transitions[] = $transition->toJSON();
            }

            return $this->renderJSON($transitions);
        }

    }
