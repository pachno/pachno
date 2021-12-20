<?php

    namespace pachno\core\entities;

    use b2db\Row;
    use Exception;
    use pachno\core\entities\tables\UserCommits;
    use pachno\core\entities\traits\Commentable;
    use pachno\core\framework\Context;
    use pachno\core\framework\Settings;
    use pachno\core\helpers\Diffable;
    use pachno\core\helpers\TextParser;
    use pachno\core\modules\livelink\Livelink;

    /**
     * Commit class, vcs_integration
     *
     * @package pachno
     * @subpackage vcs_integration
     *
     * @Table(name="\pachno\core\entities\tables\Commits")
     */
    class Commit extends common\IdentifiableScoped implements Diffable
    {

        use Commentable;

        /**
         * Commit log.
         * @var string
         * @Column(type="text")
         */
        protected $_log;

        /**
         * Revision number/hash of previous commit
         * @var string/integer
         * @Column(type="string", length=40)
         */
        protected $_old_rev;

        /**
         * Previous commit
         * @var Commit
         * @Relates(class="\pachno\core\entities\Commit")
         * @Column(type="integer")
         */
        protected $_previous_commit_id;

        /**
         * Revision number/hash of this commit
         * @var string/integer
         * @Column(type="string", length=40)
         */
        protected $_new_rev;

        /**
         * Short revision number/hash of this commit
         * @var string/integer
         * @Column(type="string", length=40)
         */
        protected $_new_rev_short;

        /**
         * Commit author
         * @var User
         * @Relates(class="\pachno\core\entities\User")
         * @Column(type="integer")
         */
        protected $_author;

        /**
         * POSIX timestamp of commit
         * @var integer
         * @Column(type="integer")
         */
        protected $_date;

        /**
         * Misc data
         * @var array
         * @Column(type="serializable", length=500)
         */
        protected $_data = [];

        /**
         * Affected files
         * @var CommitFile[]
         * @Relates(class="\pachno\core\entities\CommitFile", collection=true, foreign_column="commit_id")
         */
        protected $_files;

        /**
         * Affected branches
         * @var BranchCommit[]
         * @Relates(class="\pachno\core\entities\BranchCommit", collection=true, foreign_column="commit_id")
         */
        protected $_branch_commits;

        /**
         * Affected branches
         * @var Branch[]
         */
        protected $_branches;

        /**
         * Affected issues
         * @var array
         */
        protected $_issues;

        /**
         * Project
         * @var Project
         * @Relates(class="\pachno\core\entities\Project")
         * @Column(type="integer", name="project_id")
         */
        protected $_project = null;

        /**
         * Whether the commit is imported
         *
         * @var boolean
         * @Column(type="boolean", default="false")
         */
        protected $_is_imported = false;

        /**
         * Array of users that are subscribed to this commit
         *
         * @var array
         * @Relates(class="\pachno\core\entities\User", collection=true, manytomany=true, joinclass="\pachno\core\entities\tables\UserCommits")
         */
        protected $_subscribers = null;

        protected $_structure;

        protected $_lines_removed;

        protected $_lines_added;

        public function getTitle($full = false)
        {
            $lines = explode("\n", $this->getLog());

            if ($full) {
                return $lines[0];
            }

            $title = substr($lines[0], 0, 60);
            if (strlen($lines[0]) > 60) $title .= '...';

            return $title;
        }

        /**
         * Get the commit log for this commit
         *
         * @return string
         */
        public function getLog()
        {
            return trim($this->_log);
        }

        /**
         * Set a new log for the commit. This will not affect the issues which are affected
         *
         * @param string $log
         */
        public function setLog($log)
        {
            $this->_log = $log;
        }

        public function getMessage($include_title = true)
        {
            $lines = explode("\n", $this->getLog());

            if (count($lines) > 1 || !$include_title) {
                array_shift($lines);

                return implode("\n", $lines);
            }

            if (strlen($lines[0]) > 60) {
                return '...' . substr($lines[0], 60);
            }

            return '';
        }

        public function getRevisionString()
        {
            return (!is_numeric($this->getRevision())) ? mb_substr($this->getRevision(), 0, 7) : $this->getRevision();
        }

        /**
         * Get this commit's revision number or hash
         *
         * @return mixed
         */
        public function getRevision()
        {
            return $this->_new_rev;
        }

        public function getPreviousRevisionString()
        {
            return (!is_numeric($this->getPreviousRevision())) ? mb_substr($this->getPreviousRevision(), 0, 7) : $this->getPreviousRevision();
        }

        /**
         * Get the preceeding commit's revision number or hash
         *
         * @return mixed
         */
        public function getPreviousRevision()
        {
            return $this->_old_rev;
        }

        /**
         * Get any other data for this comment, will need parsing
         *
         * @return array
         */
        public function getMiscData()
        {
            return $this->_data;
        }

        /**
         * Get an array of BranchCommit objects affected by this commit
         *
         * @return Branch[]
         */
        public function getBranches()
        {
            $this->_populateAffectedBranches();

            return $this->_branches;
        }

        private function _populateAffectedBranches()
        {
            if ($this->_branch_commits === null) {
                $this->_branch_commits = $this->_b2dbLazyLoad('_branch_commits');
                $this->_branches = [];
                foreach ($this->_branch_commits as $branch_commit) {
                    $branch = $branch_commit->getBranch();
                    $this->_branches[$branch->getID()] = $branch;
                }
            }
        }

        public function hasIssues()
        {
            return (bool)count($this->getIssues());
        }

        /**
         * Get an array of \pachno\core\entities\Issues affected by this commit
         *
         * @return Issue[]
         */
        public function getIssues()
        {
            $this->_populateAffectedIssues();

            return $this->_issues;
        }

        private function _populateAffectedIssues()
        {
            if ($this->_issues === null) {
                $issue_commits = tables\IssueCommits::getTable()->getByCommitID($this->_id);
                $issues = [];
                foreach ($issue_commits as $issue_commit) {
                    if ($issue_commit->getIssue() instanceof Issue) {
                        $issues[$issue_commit->getIssue()->getId()] = $issue_commit->getIssue();
                    }
                }
                $this->_issues = $issues;
            }
        }

        /**
         * Set a new parent revision
         *
         * @param integer $revno
         */
        public function setPreviousRevision($revno)
        {
            $this->_old_rev = $revno;
        }

        public function setPreviousCommit(Commit $commit)
        {
            $this->_previous_commit_id = $commit;
        }

        /**
         * @return Commit
         */
        public function getPreviousCommit()
        {
            return $this->_b2dbLazyLoad('_previous_commit_id');
        }

        /**
         * Set THIS revisions revno
         *
         * @param integer $revno
         */
        public function setRevision($revno)
        {
            $this->_new_rev = $revno;
            if (!is_numeric($revno) && strlen($revno) > 7) {
                $this->_new_rev_short = substr($revno, 0, 7);
            } else {
                $this->_new_rev_short = $revno;
            }
        }

        public function getShortRevision()
        {
            return $this->_new_rev_short;
        }

        /**
         * Set misc data for this commit (see other docs)
         *
         * @param array $data
         */
        public function setMiscData($data)
        {
            $this->_data = $data;
        }

        /**
         * Get Gitlab url for merge request bz provided id
         *
         * @param integer $merge_request_id
         *
         * @return string
         *
         * @throws Exception
         */
        public function getGitlabUrlForMergeRequestID($merge_request_id)
        {
            $base_url = Context::getModule('vcs_integration')->getSetting('browser_url_' . $this->getProject()->getID());
            $misc_data_array = $this->getMiscDataArray();
            $reposname = null;

            if (array_key_exists('gitlab_repos_ns', $misc_data_array)) {
                $reposname = $misc_data_array['gitlab_repos_ns'];
                $base_url = rtrim($base_url, '/') . '/' . $reposname;
            }

            return $base_url . '/merge_requests/' . $merge_request_id;
        }

        public function isImported()
        {
            return $this->_is_imported;
        }

        public function setIsImported($is_imported = true)
        {
            $this->_is_imported = $is_imported;
        }

        public function getStructure()
        {
            if ($this->_structure === null) {
                $this->_structure = [
                    'dirs' => [],
                    'filepaths' => [],
                ];
                foreach ($this->getFiles() as $file) {
                    $paths = explode('/', $file->getDirectory());
                    $path = array_shift($paths);
                    $new_paths = $this->dirToArray($paths);
                    $this->_structure['dirs'][$path] = (isset($this->_structure['dirs'][$path])) ? array_merge_recursive($this->_structure['dirs'][$path], $new_paths) : $new_paths;
                    if (!isset($this->_structure['filepaths'][$file->getDirectory()])) {
                        $this->_structure['filepaths'][$file->getDirectory()] = [];
                    }
                    $this->_structure['filepaths'][$file->getDirectory()][] = $file;
                }
            }

            return $this->_structure;
        }

        /**
         * Get an array of CommitFiles affected by this commit
         *
         * @return CommitFile[]
         */
        public function getFiles()
        {
            $this->_populateAffectedFiles();

            return $this->_files;
        }

        private function _populateAffectedFiles()
        {
            if ($this->_files === null) {
                $this->_files = $this->_b2dbLazyLoad('_files');
                uasort($this->_files, function ($file_1, $file_2) {
                    /** @var CommitFile $file_1 */
                    /** @var CommitFile $file_2 */
                    return strnatcasecmp($file_1->getPath(), $file_2->getPath());
                });
            }
        }

        protected function dirToArray(&$dirs)
        {

            $result = [];

            while (count($dirs)) {
                $dir = array_shift($dirs);
                $result[$dir] = $this->dirToArray($dirs);
            }

            return $result;
        }

        /**
         * @return int
         */
        public function getLinesRemoved()
        {
            if ($this->_lines_removed === null) {
                $this->_lines_removed = 0;
                foreach ($this->getFiles() as $file) {
                    $this->_lines_removed += $file->getLinesRemoved();
                }
            }

            return $this->_lines_removed;
        }

        /**
         * @return int
         */
        public function getLinesAdded()
        {
            if ($this->_lines_added === null) {
                $this->_lines_added = 0;
                foreach ($this->getFiles() as $file) {
                    $this->_lines_added += $file->getLinesAdded();
                }
            }

            return $this->_lines_added;
        }

        protected function _preSave(bool $is_new): void
        {
            parent::_preSave($is_new);
            if ($is_new) {
                if (!$this->_date) {
                    $this->_date = NOW;
                }

                $log_item = new LogItem();
                $log_item->setChangeType(LogItem::ACTION_COMMIT_CREATED);
                $log_item->setTarget($this->getID());
                $log_item->setTargetType(LogItem::TYPE_COMMIT);
                $log_item->setProject($this->getProject());
                $log_item->setTime($this->getDate());
                $log_item->setUser($this->getAuthor()->getID());
                $log_item->save();
            }
        }

        /**
         * Get the project this commit applies to
         *
         * @return Project
         */
        public function getProject()
        {
            return $this->_b2dbLazyLoad('_project');
        }

        /**
         * Set the project this commit applies to
         *
         * @param Project $project
         */
        public function setProject(Project $project)
        {
            $this->_project = $project;
        }

        /**
         * Get the POSIX timestamp of this comment
         *
         * @return integer
         */
        public function getDate()
        {
            return $this->_date;
        }

        /**
         * Set a new date for the commit
         *
         * @param integer $date
         */
        public function setDate($date)
        {
            $this->_date = $date;
        }

        /**
         * Get the author of this commit
         *
         * @return User
         */
        public function getAuthor()
        {
            return $this->_b2dbLazyLoad('_author');
        }

        /**
         * Set a new commit author
         *
         * @param User $user
         */
        public function setAuthor(User $user)
        {
            $this->_author = $user;
        }

        protected function _postSave(bool $is_new): void
        {
            if ($is_new) {
                $this->_addNotifications();
            }
        }

        /**
         * @return User[]
         */
        public function getMentionedUsers()
        {
            $users = [];
            $parser = new TextParser($this->_log);
            $parser->setOption('plain', true);
            $parser->doParse();
            if ($parser->hasMentions()) {
                foreach ($parser->getMentions() as $user) {
                    $users[$user->getID()] = $user;
                }
            }
            foreach ($this->getComments() as $comment) {
                foreach ($comment->getMentions() as $user) {
                    $users[$user->getID()] = $user;
                }
            }

            return $users;
        }

        public function _addNotifications()
        {
            $parser = new TextParser($this->_log);
            $parser->setOption('plain', true);
            $parser->doParse();
            foreach ($parser->getMentions() as $user) {
                if (!$this->getAuthor() || ($this->getAuthor() instanceof User && $user->getID() == $this->getAuthor()->getID()) || (!$this->getAuthor() instanceof User && $user->getID() == $this->getAuthor()))
                    continue;

                $notification = new Notification();
                $notification->setTarget($this);
                $notification->setTriggeredByUser($this->getAuthor());
                $notification->setUser($user);
                $notification->setNotificationType(Livelink::NOTIFICATION_COMMIT_MENTIONED);
                $notification->setModuleName('livelink');
                $notification->save();
            }
        }

        protected function _construct(Row $row, string $foreign_key = null): void
        {
            parent::_construct($row, $foreign_key);
            $this->_num_comments = tables\Comments::getTable()->getPreloadedCommentCount(Comment::TYPE_COMMIT, $this->_id);
        }

        public function shouldAutomaticallySubscribeUser($user)
        {
            return false;
        }

        public function isSubscriber($user)
        {
            if (!$user instanceof User) return false;

            $user_id = (string)$user->getID();
            $subscribers = (array)$this->getSubscribers();
            $new_subscribers = (array)$this->_new_subscribers;

            return (bool)in_array($user_id, $new_subscribers) || (bool)array_key_exists($user_id, $subscribers);
        }

        public function getSubscribers()
        {
            $this->_b2dbLazyLoad('_subscribers');

            return $this->_subscribers;
        }

        public function addSubscriber($user_id)
        {
            UserCommits::getTable()->addStarredCommit($user_id, $this->getID());
            $this->_new_subscribers[] = $user_id;
        }

    }
