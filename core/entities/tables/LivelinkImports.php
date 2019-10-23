<?php

    namespace pachno\core\entities\tables;

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
     *
     * @Table(name="livelink_imports")
     * @Entity(class="\pachno\core\entities\LivelinkImport")
     */
    class LivelinkImports extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;

        const B2DBNAME = 'livelink_imports';

        const SCOPE = 'livelink_imports.scope';

        /**
         * @return LivelinkImport[]
         */
        public function getPending()
        {
            $query = $this->getQuery();
            $query->where('livelink_imports.completed_at', 0);
            $res = $this->select($query, false);

            return $res;
        }

        public function hasPendingByProject(Project $project)
        {
            $query = $this->getQuery();
            $query->where('livelink_imports.completed_at', 0);
            $query->where('livelink_imports.project_id', $project->getID());

            return (bool)$this->count($query);
        }

    }
