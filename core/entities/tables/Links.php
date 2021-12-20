<?php

    namespace pachno\core\entities\tables;

    use b2db\Query;
    use b2db\QueryColumnSort;
    use b2db\Update;
    use pachno\core\entities\Link;
    use pachno\core\entities\Scope;
    use pachno\core\framework;

    /**
     * Links table
     * @method Link[] select(Query $query)
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="links")
     * @Entity(class="\pachno\core\entities\Link")
     */
    class Links extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'links';

        public const ID = 'links.id';

        public const UID = 'links.uid';

        public const URL = 'links.url';

        public const LINK_ORDER = 'links.link_order';

        public const DESCRIPTION = 'links.description';

        public const TARGET_TYPE = 'links.target_type';

        public const TARGET_ID = 'links.target_id';

        public const SCOPE = 'links.scope';

        public function getNextOrder($target_type, $target_id, $scope = null)
        {
            $scope = ($scope === null) ? framework\Context::getScope()->getID() : $scope;
            $query = $this->getQuery();
            $query->addSelectionColumn(self::LINK_ORDER, 'max_order', Query::DB_MAX, '', '+1');
            $query->where(self::TARGET_TYPE, $target_type);
            $query->where(self::TARGET_ID, $target_id);
            $query->where(self::SCOPE, $scope);

            $row = $this->rawSelectOne($query);
            $link_order = ($row->get('max_order')) ? $row->get('max_order') : 1;

            return $link_order;
        }

        public function addLinkToIssue($issue_id, $url, $description = null)
        {
            return $this->addLink('issue', $issue_id, $url, $description);
        }

        /**
         * @param $target_type
         * @param int $target_id
         * @param null $url
         * @param null $description
         * @param null $link_order
         * @param null $scope
         *
         * @return Link
         */
        public function addLink($target_type, $target_id = 0, $url = null, $description = null, $link_order = null, $scope = null)
        {
            $link = new Link();
            $link->setTargetType($target_type);
            $link->setTargetId($target_id);
            $link->setUrl($url);
            $link->setDescription($description);
            $link->setLinkOrder($link_order);
            if ($scope !== null) {
                $link->setScope($scope);
            }
            $link->save();

            framework\Context::getCache()->clearCacheKeys([framework\Cache::KEY_MAIN_MENU_LINKS]);

            return $link;
        }

        public function getMainLinks()
        {
            return $this->getLinks('main_menu');
        }

        /**
         * @param $target_type
         * @param int $target_id
         *
         * @return Link[]
         */
        public function getLinks($target_type, $target_id = 0)
        {
            $query = $this->getQuery();
            $query->where(self::TARGET_TYPE, $target_type);
            $query->where(self::TARGET_ID, $target_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->addOrderBy(self::LINK_ORDER, QueryColumnSort::SORT_ASC);

            return $this->select($query);
        }

        public function getByIssueID($issue_id)
        {
            return $this->getLinks('issue', $issue_id);
        }

        public function removeByIssueIDandLinkID($issue_id, $link_id)
        {
            return $this->removeByTargetTypeTargetIDandLinkID('issue', $issue_id, $link_id);
        }

        public function removeByTargetTypeTargetIDandLinkID($target_type, $target_id, $link_id = null)
        {
            $query = $this->getQuery();
            $query->where(self::TARGET_TYPE, $target_type);
            $query->where(self::TARGET_ID, $target_id);
            if ($link_id !== null) {
                $query->where(self::ID, $link_id);
            }
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawDelete($query);

            framework\Context::getCache()->clearCacheKeys([framework\Cache::KEY_MAIN_MENU_LINKS]);

            return true;
        }

        public function saveLinkOrder($links)
        {
            foreach ($links as $key => $link_id) {
                $update = new Update();
                $update->add(self::LINK_ORDER, $key + 1);
                $this->rawUpdateById($update, $link_id);
            }
            framework\Context::getCache()->clearCacheKeys([framework\Cache::KEY_MAIN_MENU_LINKS]);
        }

        public function addMainMenuLink($url = null, $description = null, $link_order = null, $scope = null)
        {
            return $this->addLink('main_menu', 0, $url, $description, $link_order, $scope);
        }

        protected function setupIndexes(): void
        {
            $this->addIndex('targettype_targetid_scope', [self::TARGET_TYPE, self::TARGET_ID, self::SCOPE]);
        }

    }
