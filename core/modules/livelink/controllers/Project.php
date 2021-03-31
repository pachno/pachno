<?php

    namespace pachno\core\modules\livelink\controllers;

    use pachno\core\entities\Branch;
    use pachno\core\entities\Comment;
    use pachno\core\entities\Commit;
    use pachno\core\entities\tables\BranchCommits;
    use pachno\core\entities\tables\Branches;
    use pachno\core\entities\tables\Comments;
    use pachno\core\entities\tables\Commits;
    use pachno\core\entities\tables\IssueCommits;
    use pachno\core\entities\tables\Issues;
    use pachno\core\entities\tables\Permissions;
    use pachno\core\framework;
    use pachno\core\helpers\Pagination;
    use pachno\core\helpers\ProjectActions;
    use pachno\core\modules\livelink\Livelink;

    /**
     * Main controller for the livelink module
     *
     * @property Commit $commit
     * @property Branch $branch
     */
    class Project extends ProjectActions
    {

        /**
         * @Route(name="livelink_project_commits_post", url="/:project_key/commits", methods="POST")
         *
         * @param framework\Request $request
         *
         * @return bool
         */
        public function runPostProjectCommits(framework\Request $request)
        {
            $selected_branch = $request['branch'];
            if ($selected_branch !== '*') {
                $branch = Branches::getTable()->getByBranchNameAndProject($selected_branch, $this->selected_project);
            }
            if (!$branch instanceof Branch) {
                return $this->return404('Invalid branch');
            }

            $total_commits_count = BranchCommits::getTable()->getPaginationItemCount($branch->getID());
            $offset = $request['offset'];

            $commit = null;
            if ($request->hasParameter('from_commit')) {
                $from_commit_ref = trim($request['from_commit']);
                if (strlen($from_commit_ref) < 7) {
                    return $this->return404('Invalid commit ref');
                }

                $commit = Commits::getTable()->getCommitByHash($from_commit_ref, $this->selected_project);
            }
            $commits = $branch->getCommits($commit);
            $commit_ids = array_reduce($commits, function ($ids, $commit) {
                $ids[] = $commit->getID();

                return $ids;
            }, []);
            $offset += count($commit_ids);

            $branch_points = Branches::getTable()->getByCommitsAndProject($commit_ids, $this->selected_project);
            Comments::getTable()->preloadCommentCounts(Comment::TYPE_COMMIT, $commit_ids);

            return $this->renderJSON(['content' => $this->getComponentHTML('livelink/projectcommitslist', ['commits' => $commits, 'total_commits_count' => $total_commits_count, 'offset' => $offset, 'branches' => $branch_points, 'selected_project' => $this->selected_project, 'branch' => $branch])]);
        }

        /**
         * @Route(name="livelink_project_commits", url="/:project_key/commits", methods="GET")
         *
         * @param framework\Request $request
         *
         * @return bool
         */
        public function runProjectCommits(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectAccess(Permissions::PERMISSION_PROJECT_ACCESS_CODE));

            $this->branches = Branches::getTable()->getByProject($this->selected_project);
            $this->is_importing = $this->getModule()->isProjectImportInProgress($this->selected_project);
            $this->connector = $this->getModule()->getProjectConnector($this->selected_project);
        }

        /**
         * @return Livelink
         */
        protected function getModule()
        {
            return framework\Context::getModule('livelink');
        }

        /**
         * @Route(name="livelink_project_commit_import", url="/:project_key/commits/:commit_hash/import/*", methods="GET")
         *
         * @param framework\Request $request
         *
         * @return bool
         */
        public function runProjectImportCommit(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectAccess(Permissions::PERMISSION_PROJECT_ACCESS_CODE));

            $this->commit = Commits::getTable()->getCommitByHash($request['commit_hash'], $this->selected_project);
            if (!$this->commit instanceof Commit || $this->commit->isImported()) {
                $this->return404('Invalid commit');
            }

            if ($request->hasParameter('branch')) {
                $this->branch = Branches::getTable()->getByBranchNameAndProject($request['branch'], $this->selected_project);
                if (!$this->branch instanceof Branch) {
                    $this->return404('Invalid branch');
                }
            }

            $connector = $this->getModule()->getConnectorModule($this->getModule()->getProjectConnector($this->selected_project));
            $connector->importSingleCommit($this->selected_project, $this->commit);

            $options = ['project_key' => $this->selected_project->getKey(), 'commit_hash' => $this->commit->getRevision()];
            if (isset($this->branch)) {
                $options['branch'] = $this->branch->getName();
            }

            $this->forward($this->getRouting()->generate('livelink_project_commit', $options));
        }

        /**
         * @Route(name="livelink_project_commit", url="/:project_key/commits/:commit_hash/*", methods="GET")
         *
         * @param framework\Request $request
         *
         * @return bool
         */
        public function runProjectCommit(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectAccess(Permissions::PERMISSION_PROJECT_ACCESS_CODE));
            $options = [
                'project' => $this->selected_project
            ];

            if ($request->hasParameter('branch')) {
                $branch = Branches::getTable()->getByBranchNameAndProject($request['branch'], $this->selected_project);
                if (!$branch instanceof Branch) {
                    $this->return404('Invalid branch');
                }
                $options['branch'] = $branch;
            }

            $commit = Commits::getTable()->getCommitByHash($request['commit_hash'], $this->selected_project);
            if (!$commit instanceof Commit) {
                $this->return404('Invalid commit');
            }

            if (!$commit->isImported()) {
                $connector = $this->getModule()->getConnectorModule($this->getModule()->getProjectConnector($this->selected_project));
                $connector->importSingleCommit($this->selected_project, $commit);
            }

            $options['is_importing'] = $this->getModule()->isProjectImportInProgress($this->selected_project);
            $options['commit'] = $commit;

            return $this->renderJSON([
                'component' => $this->getComponentHTML('livelink/projectcommit', $options),
                'commit' => $this->getComponentHTML('livelink/commitrow', $options),
            ]);
        }

        /**
         * @Route(name="livelink_project_commits_more", url="/:project_key/commits/more")
         *
         * @param framework\Request $request
         *
         * @return bool
         */
        public function runProjectCommitsMore(framework\Request $request)
        {
            $this->forward403unless($this->_checkProjectAccess(Permissions::PERMISSION_PROJECT_ACCESS_CODE));

            $branch = Branches::getTable()->getByBranchNameAndProject($request['branch'], $this->selected_project);
            if (!$branch instanceof Branch) {
                $this->return404('Invalid branch');
            }

            $commit = null;
            if ($request->hasParameter('from_commit')) {
                $from_commit_ref = trim($request['from']);
                if (strlen($from_commit_ref) < 7) {
                    $this->return404('Invalid commit ref');
                }

                $commit = Commits::getTable()->getCommitByHash($from_commit_ref, $this->selected_project);
            }
            $this->commits = $branch->getCommits($commit);

            if (count($this->commits)) {
                $last_commit_hash = array_shift(array_values($this->commits))->getShortRevision();
            } else {
                $last_commit_hash = $from_commit_ref ?? '';
            }

            return $this->renderJSON(['content' => $this->getComponentHTML('livelink/projectcommits', ['commits' => $this->commits, 'selected_project' => $this->selected_project]), 'last_commit' => $last_commit_hash]);
        }

        /**
         * @Route(name="livelink_project_issue_commits_more", url="/:project_key/issues/:issue_no/commits/more", methods="POST")
         *
         * @param framework\Request $request
         *
         * @return bool
         */
        public function runProjectIssueCommitsMore(framework\Request $request)
        {
            $issue = Issues::getTable()->getByProjectIDAndIssueNo($this->selected_project->getID(), $request['issue_no']);
            $links = IssueCommits::getTable()->getByIssueID($issue->getID(), $request->getParameter('limit', 0), $request->getParameter('offset', 0));

            return $this->renderJSON(['content' => $this->getComponentHTML('livelink/issuecommits', ["projectId" => $this->selected_project->getID(), "links" => $links])]);
        }

    }

