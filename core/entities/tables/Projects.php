<?php

    namespace pachno\core\entities\tables;

    use b2db\Criteria;
    use b2db\Criterion;
    use b2db\Query;
    use b2db\QueryColumnSort;
    use b2db\Update;
    use pachno\core\entities\IssuetypeScheme;
    use pachno\core\entities\Project;
    use pachno\core\framework;

    /**
     * Projects table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Projects table
     *
     * @package pachno
     * @subpackage tables
     *
     * @method static Projects getTable() Retrieves an instance of this table
     * @method Project selectById(integer $id) Retrieves a project
     * @method Project[] select(Query $query, $join = 'all')
     *
     * @Table(name="projects")
     * @Entity(class="\pachno\core\entities\Project")
     */
    class Projects extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 3;

        public const B2DBNAME = 'projects';

        public const ID = 'projects.id';

        public const SCOPE = 'projects.scope';

        public const NAME = 'projects.name';

        public const KEY = 'projects.key';

        public const PREFIX = 'projects.prefix';

        public const USE_PREFIX = 'projects.use_prefix';

        public const USE_SCRUM = 'projects.use_scrum';

        public const HOMEPAGE = 'projects.homepage';

        public const OWNER_USER = 'projects.owner_user';

        public const OWNER_TEAM = 'projects.owner_team';

        public const LEADER_TEAM = 'projects.leader_team';

        public const LEADER_USER = 'projects.leader_user';

        public const CLIENT = 'projects.client';

        public const DESCRIPTION = 'projects.description';

        public const DOC_URL = 'projects.doc_url';

        public const WIKI_URL = 'projects.wiki_url';

        public const RELEASED = 'projects.isreleased';

        public const PLANNED_RELEASED = 'projects.isplannedreleased';

        public const RELEASE_DATE = 'projects.release_date';

        public const ENABLE_BUILDS = 'projects.enable_builds';

        public const ENABLE_EDITIONS = 'projects.enable_editions';

        public const ENABLE_COMPONENTS = 'projects.enable_components';

        public const SHOW_IN_SUMMARY = 'projects.show_in_summary';

        public const SUMMARY_DISPLAY = 'projects.summary_display';

        public const HAS_DOWNLOADS = 'projects.has_downloads';

        public const QA = 'projects.qa_responsible';

        public const QA_TYPE = 'projects.qa_responsible_type';

        public const LOCKED = 'projects.locked';

        public const ISSUES_LOCK_TYPE = 'projects.issues_lock_type';

        public const DELETED = 'projects.deleted';

        public const SMALL_ICON = 'projects.small_icon';

        public const LARGE_ICON = 'projects.large_icon';

        public const ALLOW_CHANGING_WITHOUT_WORKING = 'projects.allow_freelancing';

        public const WORKFLOW_SCHEME_ID = 'projects.workflow_scheme_id';

        public const ISSUETYPE_SCHEME_ID = 'projects.issuetype_scheme_id';

        public const AUTOASSIGN = 'projects.autoassign';

        public const PARENT_PROJECT_ID = 'projects.parent';

        public const ARCHIVED = 'projects.archived';

        public function getByPrefix($prefix)
        {
            $query = $this->getQuery();
            $query->where(self::PREFIX, $prefix);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->selectOne($query);
        }

        /**
         * @param bool $all_scopes
         *
         * @return Project[]
         */
        public function getAll($all_scopes = false): array
        {
            $query = $this->getQuery();
            $query->addOrderBy(self::NAME, QueryColumnSort::SORT_ASC);
            if (!$all_scopes) {
                $query->where(self::SCOPE, framework\Context::getScope()->getID());
            }
            $query->where(self::DELETED, false);

            return $this->select($query, false);
        }

        public function getAllIncludingDeleted()
        {
            $query = $this->getQuery();
            $query->addOrderBy(self::NAME, QueryColumnSort::SORT_ASC);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->select($query, false);

            return $res;
        }

        public function getByID($id, $scoped = true)
        {
            if ($scoped) {
                $query = $this->getQuery();
                $query->where(self::SCOPE, framework\Context::getScope()->getID());
                $row = $this->rawSelectById($id, $query, false);
            } else {
                $row = $this->rawSelectById($id);
            }

            return $row;
        }

        public function getByClientID($id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::CLIENT, $id);
            $row = $this->rawSelect($query, false);

            return $row;
        }

        public function getByParentID($id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::PARENT_PROJECT_ID, $id);
            $query->where(self::DELETED, false);

            $res = $this->select($query, false);

            return $res;
        }

        public function quickfind($projectname)
        {
            $query = $this->getQuery();
            $query->where(self::DELETED, false);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $criteria = new Criteria();
            $criteria->where(self::NAME, "%{$projectname}%", Criterion::LIKE);
            $criteria->or(self::KEY, strtolower("%{$projectname}%"), Criterion::LIKE);
            $query->and($criteria);

            return $this->select($query);
        }

        public function getByKey($key)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::KEY, $key);
            $query->where(self::KEY, '', Criterion::NOT_EQUALS);
            $row = $this->rawSelectOne($query, false);

            return $row;
        }

        public function countByIssuetypeSchemeID($scheme_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::ISSUETYPE_SCHEME_ID, $scheme_id);
            $query->where(self::DELETED, false);
            $query->where(self::ARCHIVED, false);

            return (int)$this->count($query);
        }

        public function countByWorkflowSchemeID($scheme_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::WORKFLOW_SCHEME_ID, $scheme_id);
            $query->where(self::DELETED, false);
            $query->where(self::ARCHIVED, false);

            return $this->count($query);
        }

        public function countProjects($scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $query = $this->getQuery();
            $query->where(self::SCOPE, $scope);
            $query->where(self::DELETED, false);
            $query->where(self::ARCHIVED, false);

            return $this->count($query);
        }

        public function getByUserID($user_id)
        {
            $query = $this->getQuery();

            $criteria = new Criteria();
            $criteria->where(self::LEADER_USER, $user_id);
            $criteria->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where($criteria);

            $criteria = new Criteria();
            $criteria->where(self::OWNER_USER, $user_id);
            $criteria->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->or($criteria);

            return $this->select($query);
        }

        public function updateByIssuetypeSchemeID($scheme_id)
        {
            $schemes = IssuetypeScheme::getAll();
            foreach ($schemes as $default_scheme_id => $scheme) {
                break;
            }

            $query = $this->getQuery();
            $update = new Update();

            $update->add(self::ISSUETYPE_SCHEME_ID, $default_scheme_id);

            $query->where(self::ISSUETYPE_SCHEME_ID, $scheme_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $res = $this->rawUpdate($update, $query);
        }

        public function getByFileID($file_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $criteria = new Criteria();
            $criteria->where(self::SMALL_ICON, $file_id);
            $criteria->or(self::LARGE_ICON, $file_id);
            $query->and($criteria);

            return $this->select($query);
        }

        public function getFileIds()
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::SMALL_ICON, 'file_id_small');
            $query->addSelectionColumn(self::LARGE_ICON, 'file_id_large');

            $res = $this->rawSelect($query);
            $file_ids = [];
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $file_ids[$row['file_id_small']] = $row['file_id_small'];
                    $file_ids[$row['file_id_large']] = $row['file_id_large'];
                }
            }

            return $file_ids;
        }

        protected function setupIndexes(): void
        {
            $this->addIndex('scope', self::SCOPE);
            $this->addIndex('scope_name', [self::SCOPE, self::NAME]);
            $this->addIndex('workflow_scheme_id', self::WORKFLOW_SCHEME_ID);
            $this->addIndex('issuetype_scheme_id', self::ISSUETYPE_SCHEME_ID);
            $this->addIndex('parent', self::PARENT_PROJECT_ID);
            $this->addIndex('parent_scope', [self::PARENT_PROJECT_ID, self::SCOPE]);
        }

    }
