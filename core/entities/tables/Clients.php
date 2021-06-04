<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\QueryColumnSort;
    use pachno\core\framework;

    /**
     * Clients table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Clients table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="clients")
     * @Entity(class="\pachno\core\entities\Client")
     */
    class Clients extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'clients';

        public const ID = 'clients.id';

        public const SCOPE = 'clients.scope';

        public const NAME = 'clients.name';

        public const WEBSITE = 'clients.website';

        public const EMAIL = 'clients.email';

        public const TELEPHONE = 'clients.telephone';

        public const FAX = 'clients.fax';

        public function getAll($limit = null)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->addOrderBy('clients.name', QueryColumnSort::SORT_ASC);

            if (isset($limit)) {
                $query->setLimit($limit);
            }

            return $this->select($query);
        }

        public function doesClientNameExist($client_name)
        {
            $query = $this->getQuery();
            $query->where(self::NAME, $client_name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return (bool)$this->count($query);
        }

        public function quickfind($client_name)
        {
            $query = $this->getQuery();
            $query->where(self::NAME, "%{$client_name}%", Criterion::LIKE);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            return $this->select($query);
        }

    }
