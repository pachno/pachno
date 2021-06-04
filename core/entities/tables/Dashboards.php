<?php

    namespace pachno\core\entities\tables;

    use b2db\Query;
    use pachno\core\entities\Dashboard;
    use pachno\core\framework\Context;

    /**
     * User dashboards table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * User dashboards table
     *
     * @method Dashboard[] select(Query $query, $join = 'all')
     * @method static Dashboards getTable()
     *
     * @Table(name="dashboards")
     * @Entity(class="\pachno\core\entities\Dashboard")
     */
    class Dashboards extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const SCOPE = 'dashboards.scope';

        /**
         * @param $user_id
         * @return Dashboard[]
         */
        public function getByUserIdScoped($user_id): array
        {
            $query = $this->getQuery();
            $query->where('dashboards.user_id', $user_id);
            $query->where('dashboards.scope', Context::getScope()->getID());

            return $this->select($query);
        }

    }
