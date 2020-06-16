<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\IdentifiableScoped;

    /**
     * Agile board column class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage agile
     */

    /**
     * Agile board column class
     *
     * @package pachno
     * @subpackage agile
     *
     * @Table(name="\pachno\core\entities\tables\BoardColumns")
     */
    class BoardColumn extends IdentifiableScoped
    {

        /**
         * The name of the column
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * Column description
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_description;

        /**
         * @var AgileBoard
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\AgileBoard")
         */
        protected $_board_id;

        /**
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_sort_order;

        /**
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_max_workitems;

        /**
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_min_workitems;

        /**
         * Associated status ids
         *
         * @var array
         * @Column(type="serializable", length=500)
         */
        protected $_status_ids = [];

        public function getName()
        {
            return $this->_name;
        }

        public function setName($name)
        {
            $this->_name = $name;
        }

        public function hasDescription()
        {
            return (bool)($this->getDescription() != '');
        }

        public function getDescription()
        {
            return $this->_description;
        }

        public function setDescription($description)
        {
            $this->_description = $description;
        }

        public function setBoard($board)
        {
            $this->_board_id = $board;
        }

        function getMaxWorkitems()
        {
            return $this->_max_workitems;
        }

        function setMaxWorkitems($max_workitems)
        {
            $this->_max_workitems = $max_workitems;
        }

        function getMinWorkitems()
        {
            return $this->_min_workitems;
        }

        function setMinWorkitems($min_workitems)
        {
            $this->_min_workitems = $min_workitems;
        }

        public function getSortOrder()
        {
            return $this->_sort_order;
        }

        public function setSortOrder($sort_order)
        {
            $this->_sort_order = $sort_order;
        }

        public function hasStatusId($status_id)
        {
            return in_array($status_id, $this->getStatusIds());
        }

        public function getStatusIds()
        {
            return $this->_status_ids;
        }

        public function setStatusIds($status_ids)
        {
            $this->_status_ids = $status_ids;
        }

        public function hasStatusIds()
        {
            return (count($this->getStatusIds()) > 0);
        }

        public function isStatusIdTaken($status_id)
        {
            foreach ($this->getBoard()->getColumns() as $column) {
                if ($column->getID() != $this->getID() && $column->hasStatusId($status_id)) return true;
            }

            return false;
        }

        /**
         * Returns the associated project
         *
         * @return AgileBoard
         */
        public function getBoard()
        {
            return $this->_b2dbLazyLoad('_board_id');
        }

        public function hasIssue(Issue $issue)
        {
            return $issue->getStatus() instanceof Status && in_array($issue->getStatus()->getID(), $this->getStatusIds());
        }

        public function getColumnOrRandomID()
        {
            return ($this->getID()) ? $this->getID() : md5(rand(0, 1000000));
        }

        public function toJSON($detailed = true)
        {
            $json = parent::toJSON($detailed);
            $json['status_ids'] = $this->_status_ids;
            $json['name'] = $this->_name;
            $json['sort_order'] = $this->_sort_order;

            return $json;
        }

    }
