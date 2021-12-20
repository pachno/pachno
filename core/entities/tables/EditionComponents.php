<?php

    namespace pachno\core\entities\tables;

    use b2db\Insertion;
    use pachno\core\framework;

    /**
     * Edition components table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Edition components table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="editioncomponents")
     */
    class EditionComponents extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'editioncomponents';

        public const ID = 'editioncomponents.id';

        public const SCOPE = 'editioncomponents.scope';

        public const EDITION = 'editioncomponents.edition';

        public const COMPONENT = 'editioncomponents.component';

        public function getByEditionID($edition_id)
        {
            $query = $this->getQuery();
            $query->where(self::EDITION, $edition_id);
            $res = $this->rawSelect($query);

            return $res;
        }

        public function deleteByEditionID($edition_id)
        {
            $query = $this->getQuery();
            $query->where(self::EDITION, $edition_id);
            $res = $this->rawDelete($query);

            return $res;
        }

        public function addEditionComponent($edition_id, $component_id)
        {
            if ($this->getByEditionIDandComponentID($edition_id, $component_id) == 0) {
                $insertion = new Insertion();
                $insertion->add(self::EDITION, $edition_id);
                $insertion->add(self::COMPONENT, $component_id);
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                $res = $this->rawInsert($insertion);

                return true;
            }

            return false;
        }

        public function getByEditionIDandComponentID($edition_id, $component_id)
        {
            $query = $this->getQuery();
            $query->where(self::EDITION, $edition_id);
            $query->where(self::COMPONENT, $component_id);

            return $this->count($query);
        }

        public function removeEditionComponent($edition_id, $component_id)
        {
            $query = $this->getQuery();
            $query->where(self::EDITION, $edition_id);
            $query->where(self::COMPONENT, $component_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawDelete($query);
        }

        public function deleteByComponentID($component_id)
        {
            $query = $this->getQuery();
            $query->where(self::COMPONENT, $component_id);
            $res = $this->rawDelete($query);

            return $res;
        }

        protected function initialize(): void
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::EDITION, Editions::getTable(), Editions::ID);
            parent::addForeignKeyColumn(self::COMPONENT, Components::getTable(), Components::ID);
        }

    }
