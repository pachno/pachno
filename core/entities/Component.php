<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\QaLeadable;
    use pachno\core\framework;

    /**
     * Class used for components
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage main
     */

    /**
     * Class used for components
     *
     * @package pachno
     * @subpackage main
     *
     * @Table(name="\pachno\core\entities\tables\Components")
     */
    class Component extends QaLeadable
    {
        
        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * This components project
         *
         * @var unknown_type
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Project")
         */
        protected $_project = null;
        
        protected function _postSave($is_new)
        {
            if ($is_new)
            {
                framework\Context::setPermission("canseecomponent", $this->getID(), "core", 0, framework\Context::getUser()->getGroup()->getID(), 0, true);
                \pachno\core\framework\Event::createNew('core', 'Component::createNew', $this)->trigger();
            }
        }
        
        /**
         * Returns the parent project
         *
         * @return \pachno\core\entities\Project
         */
        public function getProject()
        {
            return $this->_b2dbLazyLoad('_project');
        }
        
        public function setProject($project)
        {
            $this->_project = $project;
        }
        
        protected function _preDelete()
        {
            tables\IssueAffectsComponent::getTable()->deleteByComponentID($this->getID());
            tables\EditionComponents::getTable()->deleteByComponentID($this->getID());
        }
        
        /**
         * Whether or not the current user can access the component
         * 
         * @return boolean
         */
        public function hasAccess()
        {
            return ($this->getProject()->canSeeAllComponents() || framework\Context::getUser()->hasPermission('canseecomponent', $this->getID()));
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

    }
