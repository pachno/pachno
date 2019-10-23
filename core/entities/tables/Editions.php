<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use pachno\core\entities\Edition;
    use pachno\core\framework;

    /**
     * Editions table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Editions table
     *
     * @method static Editions getTable()
     * @method Edition selectById($id)
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="editions")
     * @Entity(class="\pachno\core\entities\Edition")
     */
    class Editions extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;

        const B2DBNAME = 'editions';

        const ID = 'editions.id';

        const SCOPE = 'editions.scope';

        const NAME = 'editions.name';

        const DESCRIPTION = 'editions.description';

        const PROJECT = 'editions.project';

        const LEAD_BY = 'editions.leader';

        const LEAD_TYPE = 'editions.leader_type';

        const OWNED_BY = 'editions.owner';

        const OWNED_TYPE = 'editions.owner_type';

        const DOC_URL = 'editions.doc_url';

        const QA = 'editions.qa_responsible';

        const QA_TYPE = 'editions.qa_responsible_type';

        const RELEASED = 'editions.isreleased';

        const PLANNED_RELEASED = 'editions.isplannedreleased';

        const RELEASE_DATE = 'editions.release_date';

        const LOCKED = 'editions.locked';

        public function preloadEditions($edition_ids)
        {
            if (!count($edition_ids))
                return;

            $query = $this->getQuery();
            $query->where(self::ID, $edition_ids, Criterion::IN);
            $this->select($query);
        }

        public function getByProjectID($project_id)
        {
            $query = $this->getQuery();
            $query->where(self::PROJECT, $project_id);
            $res = $this->rawSelect($query);

            return $res;
        }

        public function getProjectIDsByEditionIDs($edition_ids)
        {
            if (count($edition_ids)) {
                $query = $this->getQuery();
                $query->where(self::ID, $edition_ids, Criterion::IN);
                $edition_ids = [];
                if ($res = $this->rawSelect($query)) {
                    while ($row = $res->getNextRow()) {
                        $edition_ids[$row->get(self::ID)] = $row->get(self::PROJECT);
                    }
                }
            }

            return $edition_ids;
        }

        public function getByIDs($ids)
        {
            if (empty($ids)) return [];

            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::ID, $ids, Criterion::IN);

            return $this->select($query);
        }

    }
