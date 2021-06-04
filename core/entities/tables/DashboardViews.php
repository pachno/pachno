<?php

    namespace pachno\core\entities\tables;

    use b2db\Insertion;
    use b2db\Query;
    use pachno\core\entities\DashboardView;
    use pachno\core\framework;

    /**
     * User dashboard views table
     *
     * @method DashboardView[] select(Query $query, $join = 'all')
     * @method static DashboardViews getTable()
     *
     * @Table(name="dashboard_views")
     * @Entity(class="\pachno\core\entities\DashboardView")
     */
    class DashboardViews extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 2;

        public const B2DBNAME = 'dashboard_views';

        public const ID = 'dashboard_views.id';

        public const NAME = 'dashboard_views.name';

        public const VIEW = 'dashboard_views.view';

        public const TEAM_ID = 'dashboard_views.tid';

        public const PROJECT_ID = 'dashboard_views.pid';

        public const TARGET_TYPE = 'dashboard_views.target_type';

        public const SCOPE = 'dashboard_views.scope';

        public const TYPE_USER = 1;

        public const TYPE_PROJECT = 2;

        public const TYPE_TEAM = 3;

        public const TYPE_CLIENT = 4;

        public function addView($target_id, $target_type, $view)
        {
            if ($view['type']) {
                $view_id = (array_key_exists('id', $view)) ? $view['id'] : 0;
                $insertion = new Insertion();
                $insertion->add(self::TEAM_ID, $target_id);
                $insertion->add(self::TARGET_TYPE, $target_type);
                $insertion->add(self::NAME, $view['type']);
                $insertion->add(self::VIEW, $view_id);
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                $this->rawInsert($insertion);
            }
        }

        public function clearViews($target_id, $target_type)
        {
            $query = $this->getQuery();
            $query->where(self::TEAM_ID, $target_id);
            $query->where(self::TARGET_TYPE, $target_type);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $this->rawDelete($query);
        }

        public function getViews($target_id, $target_type)
        {
            $query = $this->getQuery();
            $query->where(self::TEAM_ID, $target_id);
            $query->where(self::TARGET_TYPE, $target_type);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->addOrderBy(self::ID);
            $res = $this->select($query);

            return $res;
        }

        /**
         * @param $dashboard_id
         * @return DashboardView[]
         */
        public function getByDashboardIdScoped($dashboard_id)
        {
            $query = $this->getQuery();
            $query->where('dashboard_views.dashboard_id', $dashboard_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($query);
        }

    }
