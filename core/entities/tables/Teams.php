<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\Query;
    use b2db\QueryColumnSort;
    use pachno\core\entities\Team;
    use pachno\core\framework;

    /**
     * Teams table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Teams table
     *
     * @package pachno
     * @subpackage tables
     *
     * @method Team selectById($id, Query $query = null, $join = 'all')
     *
     * @Table(name="teams")
     * @Entity(class="\pachno\core\entities\Team")
     */
    class Teams extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'teams';

        public const ID = 'teams.id';

        public const SCOPE = 'teams.scope';

        public const NAME = 'teams.name';

        public const ONDEMAND = 'teams.ondemand';

        public function getAll()
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::ONDEMAND, false);
            $query->addOrderBy('teams.name', QueryColumnSort::SORT_ASC);

            return $this->select($query);
        }

        public function doesTeamNameExist($team_name)
        {
            $query = $this->getQuery();
            $query->where(self::NAME, $team_name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return (bool)$this->count($query);
        }

        public function doesIDExist($id)
        {
            $query = $this->getQuery();
            $query->where(self::ONDEMAND, false);
            $query->where(self::ID, $id);

            return $this->count($query);
        }

        public function quickfind($team_name)
        {
            $query = $this->getQuery();
            $query->where(self::NAME, "%{$team_name}%", Criterion::LIKE);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::ONDEMAND, false);

            return $this->select($query);
        }

        public function countTeams()
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::ONDEMAND, false);

            return $this->count($query);
        }

        protected function setupIndexes(): void
        {
            $this->addIndex('scope_ondemand', [self::SCOPE, self::ONDEMAND]);
        }

    }
