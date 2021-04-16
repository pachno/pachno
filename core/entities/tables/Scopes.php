<?php

    namespace pachno\core\entities\tables;

    use b2db\Criteria;
    use b2db\Criterion;
    use b2db\Join;
    use b2db\Query;
    use b2db\QueryColumnSort;
    use b2db\Table;
    use pachno\core\entities\Scope;

    /**
     * Scopes table
     *
     * @package pachno
     * @subpackage tables
     *
     * @method static Scopes getTable()
     * @method Scope[] selectAll()
     *
     * @Entity(class="\pachno\core\entities\Scope")
     * @Table(name="scopes")
     */
    class Scopes extends Table
    {

        const B2DB_TABLE_VERSION = 2;

        const B2DBNAME = 'scopes';

        const ID = 'scopes.id';

        const ENABLED = 'scopes.enabled';

        const CUSTOM_WORKFLOWS_ENABLED = 'scopes.custom_workflows_enabled';

        const MAX_WORKFLOWS = 'scopes.max_workflows';

        const UPLOADS_ENABLED = 'scopes.uploads_enabled';

        const MAX_UPLOAD_LIMIT = 'scopes.max_upload_limit';

        const MAX_USERS = 'scopes.max_users';

        const MAX_TEAMS = 'scopes.max_teams';

        const MAX_PROJECTS = 'scopes.max_projects';

        const DESCRIPTION = 'scopes.description';

        const NAME = 'scopes.name';

        const ADMINISTRATOR = 'scopes.administrator';

        public function getByHostname($hostname)
        {
            $query = $this->getQuery();
            $query->join(ScopeHostnames::getTable(), ScopeHostnames::SCOPE_ID, self::ID);
            $query->where(ScopeHostnames::HOSTNAME, $hostname);
            $row = $this->rawSelectOne($query);

            return $row;
        }

        public function getByIds($ids)
        {
            $query = $this->getQuery();
            $query->where(self::ID, $ids, Criterion::IN);

            return $this->select($query);
        }

        public function getPaginationItems($exclude_empty_projects, $exclude_empty_issues)
        {
            $query = $this->getQuery();
            $query->addOrderBy(self::NAME, QueryColumnSort::SORT_ASC);
            $query->indexBy(self::ID);
            $query->addSelectionColumn(self::ID);
            $query->addSelectionColumn(self::NAME);

            if ($exclude_empty_projects) {
                $query->join(Projects::getTable(), Projects::SCOPE, self::ID);
                $query->addSelectionColumn(Projects::ID, 'num_projects', Query::DB_COUNT);
                $query->addSelectionColumn(Projects::SCOPE);
                $criteria = new Criteria();
                $criteria->having('num_projects', 0, Criterion::GREATER_THAN);
                $query->addGroupBy(self::ID);
                $query->where($criteria);
            }
            if ($exclude_empty_issues) {
                $query->join(Issues::getTable(), Issues::SCOPE, self::ID);
                $query->addSelectionColumn(Issues::ID, 'num_issues', Query::DB_COUNT);
                $query->addSelectionColumn(Issues::SCOPE);
                $criteria = new Criteria();
                $criteria->having('num_issues', 0, Criterion::GREATER_THAN);
                $query->addGroupBy(self::ID);
                $query->where($criteria);
            }

            $res = $this->rawSelect($query);
            $scope_ids = [];

            while ($row = $res->getNextRow()) {
                $scope_ids[] = $row[self::ID];
            }

            return $scope_ids;
        }

        public function getDefault()
        {
            return $this->rawSelectById(1);
        }

        public function getByHostnameOrDefault($hostname = null)
        {
            $query = $this->getQuery();
            if ($hostname !== null) {
                $query->join(ScopeHostnames::getTable(), ScopeHostnames::SCOPE_ID, self::ID);
                $query->where(ScopeHostnames::HOSTNAME, $hostname);
                $query->or(self::ID, 1);
                $query->addOrderBy(self::ID, 'desc');
            } else {
                $query->where(self::ID, 1);
            }

            return $this->selectOne($query);
        }

    }
