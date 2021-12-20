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

        public const B2DB_TABLE_VERSION = 2;

        public const B2DBNAME = 'editions';

        public const ID = 'editions.id';

        public const SCOPE = 'editions.scope';

        public const NAME = 'editions.name';

        public const DESCRIPTION = 'editions.description';

        public const PROJECT = 'editions.project';

        public const LEAD_BY = 'editions.leader';

        public const LEAD_TYPE = 'editions.leader_type';

        public const OWNED_BY = 'editions.owner';

        public const OWNED_TYPE = 'editions.owner_type';

        public const DOC_URL = 'editions.doc_url';

        public const QA = 'editions.qa_responsible';

        public const QA_TYPE = 'editions.qa_responsible_type';

        public const RELEASED = 'editions.isreleased';

        public const PLANNED_RELEASED = 'editions.isplannedreleased';

        public const RELEASE_DATE = 'editions.release_date';

        public const LOCKED = 'editions.locked';

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
