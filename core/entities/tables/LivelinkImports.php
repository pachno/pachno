<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\Query;
    use b2db\Update;
    use pachno\core\entities\LivelinkImport;
    use pachno\core\entities\Project;

    /**
     * LiveLink imports table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * LiveLink imports table
     *
     * @method static LivelinkImports getTable()
     * @method LivelinkImport[] select(Query $query, $join = 'all')
     *
     * @Table(name="livelink_imports")
     * @Entity(class="\pachno\core\entities\LivelinkImport")
     */
    class LivelinkImports extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'livelink_imports';

        public const SCOPE = 'livelink_imports.scope';

        /**
         * @return LivelinkImport[]
         */
        public function getAndReservePending()
        {
            $query = $this->getQuery();
            $query->where('livelink_imports.status', LivelinkImport::STATUS_CREATED);
            $query->setLimit(5);

            if ($res = $this->select($query, false)) {
                $this->reserveImports(array_keys($res));
            }

            return $res;
        }

        public function reserveImports($ids)
        {
            if (!count($ids)) {
                return;
            }

            $query = $this->getQuery();
            $query->where('livelink_imports.id', $ids, Criterion::IN);
            $update = new Update();
            $update->update('livelink_imports.status', LivelinkImport::STATUS_IMPORTING);
            $this->rawUpdate($update, $query);
        }

        public function hasPendingByProject(Project $project)
        {
            $query = $this->getQuery();
            $query->where('livelink_imports.completed_at', 0);
            $query->where('livelink_imports.project_id', $project->getID());

            return (bool)$this->count($query);
        }

    }
