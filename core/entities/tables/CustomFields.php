<?php

    namespace pachno\core\entities\tables;

    use b2db\Row;
    use pachno\core\framework;

    /**
     * Custom fields table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Custom fields table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="customfields")
     * @Entity(class="\pachno\core\entities\CustomDatatype")
     */
    class CustomFields extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 2;

        public const B2DBNAME = 'customfields';

        public const ID = 'customfields.id';

        public const FIELD_NAME = 'customfields.name';

        public const FIELD_DESCRIPTION = 'customfields.description';

        public const FIELD_INSTRUCTIONS = 'customfields.instructions';

        public const FIELD_KEY = 'customfields.key';

        public const FIELD_TYPE = 'customfields.itemtype';

        public const SCOPE = 'customfields.scope';

        public function getAll()
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->indexBy(self::FIELD_KEY);

            return $this->select($query);
        }

        public function countByKey($key)
        {
            $query = $this->getQuery();
            $query->where(self::FIELD_KEY, $key);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->count($query);
        }

        public function getByKey($key)
        {
            $query = $this->getQuery();
            $query->where(self::FIELD_KEY, $key);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->rawSelectOne($query);
        }

        public function getKeyFromID($id)
        {
            $query = $this->getQuery();
            $query->where(self::ID, $id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $row = $this->rawSelectOne($query);
            if ($row instanceof Row) {
                return $row->get(self::FIELD_KEY);
            }

            return null;
        }
    }
