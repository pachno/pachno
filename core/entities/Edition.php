<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\QaLeadable;
    use pachno\core\framework;
    use pachno\core\framework\Event;

    /**
     * Edition class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage main
     */

    /**
     * Edition class
     *
     * @package pachno
     * @subpackage main
     *
     * @Table(name="\pachno\core\entities\tables\Editions")
     */
    class Edition extends QaLeadable
    {

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * The project
         *
         * @var Project
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Project")
         */
        protected $_project;

        /**
         * Editions components
         *
         * @var array|Component
         * @Relates(class="\pachno\core\entities\Component", collection=true, manytomany=true, joinclass="\pachno\core\entities\tables\EditionComponents")
         */
        protected $_components;

        /**
         * Edition builds
         *
         * @var array|Build
         * @Relates(class="\pachno\core\entities\Build", collection=true, foreign_column="edition")
         */
        protected $_builds;

        /**
         * @Column(type="string", length=200)
         */
        protected $_description;

        /**
         * The editions documentation URL
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_doc_url;

        /**
         * Whether the item is locked or not
         *
         * @var boolean
         * @access protected
         * @Column(type="boolean")
         */
        protected $_locked;

        /**
         * Whether or not this edition has a component enabled
         *
         * @param Component|integer $component The component to check for
         *
         * @return boolean
         */
        public function hasComponent($component)
        {
            $component_id = ($component instanceof Component) ? $component->getID() : $component;

            return array_key_exists($component_id, $this->getComponents());
        }

        /**
         * Returns an array with all components
         *
         * @return Component[]
         */
        public function getComponents()
        {
            $this->_populateComponents();

            return $this->_components;
        }

        /**
         * Populates components inside the edition
         *
         * @return void
         */
        protected function _populateComponents()
        {
            if ($this->_components === null) {
                $this->_b2dbLazyLoad('_components');
            }
        }

        /**
         * Whether this edition has a description set
         *
         * @return string
         */
        public function hasDescription()
        {
            return (bool)$this->getDescription();
        }

        /**
         * Returns the description
         *
         * @return string
         */
        public function getDescription()
        {
            return $this->_description;
        }

        /**
         * Set the edition description
         *
         * @param string $description
         */
        public function setDescription($description)
        {
            $this->_description = $description;
        }

        /**
         * Adds an existing component to the edition
         *
         * @param Component|integer $component
         */
        public function addComponent($component)
        {
            $component_id = ($component instanceof Component) ? $component->getID() : $component;

            return tables\EditionComponents::getTable()->addEditionComponent($this->getID(), $component_id);
        }

        /**
         * Removes an existing component from the edition
         *
         * @param Component|integer $component
         */
        public function removeComponent($component)
        {
            $component_id = ($component instanceof Component) ? $component->getID() : $component;
            tables\EditionComponents::getTable()->removeEditionComponent($this->getID(), $component_id);
        }

        /**
         * Returns the documentation url
         *
         * @return string
         */
        public function getDocumentationURL()
        {
            return $this->_doc_url;
        }

        /**
         * Returns the component specified
         *
         * @param integer $c_id
         *
         * @return Component
         */
        public function getComponent($c_id)
        {
            $this->_populateComponents();
            if (array_key_exists($c_id, $this->_components)) {
                return $this->_components[$c_id];
            }

            return null;
        }

        public function getReleasedBuilds()
        {
            $builds = $this->getBuilds();
            foreach ($builds as $id => $build) {
                if (!$build->isReleased()) unset($builds[$id]);
            }

            return $builds;
        }

        /**
         * Returns an array with all builds
         *
         * @return Build[]
         */
        public function getBuilds()
        {
            $this->_populateBuilds();

            return $this->_builds;
        }

        /**
         * Populates builds inside the edition
         *
         * @return void
         */
        protected function _populateBuilds()
        {
            if ($this->_builds === null) {
                $this->_b2dbLazyLoad('_builds');
            }
        }

        /**
         * Set the editions documentation url
         *
         * @param string $doc_url
         */
        public function setDocumentationURL($doc_url)
        {
            $this->_doc_url = $doc_url;
        }

        /**
         * Whether or not the current user can access the edition
         *
         * @return boolean
         */
        public function hasAccess()
        {
            return (!$this->isLocked() || $this->getProject()->canSeeInternalEditions());
        }

        /**
         * Returns the parent project
         *
         * @return Project
         */
        public function getProject()
        {
            return $this->_b2dbLazyLoad('_project');
        }

        public function setProject($project)
        {
            $this->_project = $project;
        }

        /**
         * Returns whether or not this item is locked
         *
         * @return boolean
         * @access public
         */
        public function isLocked()
        {
            return $this->_locked;
        }

        /**
         * Specify whether or not this item is locked
         *
         * @param boolean $locked [optional]
         */
        public function setLocked($locked = true)
        {
            $this->_locked = (bool)$locked;
        }

        /**
         * Return the items name
         *
         * @return string
         */
        public function getName()
        {
            return $this->_name;
        }

        /**
         * Set the edition name
         *
         * @param string $name
         */
        public function setName($name)
        {
            $this->_name = $name;
        }

        protected function _postSave($is_new)
        {
            if ($is_new) {
                Event::createNew('core', 'Edition::createNew', $this)->trigger();
            }
        }

        protected function _preDelete()
        {
            tables\EditionComponents::getTable()->deleteByEditionID($this->getID());
        }

    }
