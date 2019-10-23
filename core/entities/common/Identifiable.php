<?php

    namespace pachno\core\entities\common;

    use b2db\Saveable;

    /**
     * An identifiable class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage core
     */

    /**
     * An identifiable class
     *
     * @package pachno
     * @subpackage core
     */
    abstract class Identifiable extends Saveable
    {

        /**
         * The id for this item, usually identified by a record in the database
         *
         * @var integer
         * @Id
         * @Column(type="integer", not_null=true, auto_increment=1, length=10, unsigned=true)
         */
        protected $_id;

        /**
         * Create a JSON representation of this Entity.
         *
         * @param bool $detailed [optional] Include detailed information or not. (default false)
         *
         * @return array
         */
        public function toJSON($detailed = true)
        {
            return ['id' => $this->getID()];
        }

        /**
         * Return the items id
         *
         * @return integer
         */
        public function getID()
        {
            return (int)$this->_id;
        }

        /**
         * Set the items id
         *
         * @param integer $id
         */
        public function setID($id)
        {
            $this->_id = (int)$id;
        }

    }
