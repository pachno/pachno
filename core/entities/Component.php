<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\QaLeadable;
    use pachno\core\framework;
    use pachno\core\framework\Event;

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

        /**
         * Whether or not the current user can access the component
         *
         * @return boolean
         */
        public function hasAccess()
        {
            return ($this->isReleased() || $this->getProject()->canSeeInternalComponents());
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

        protected function _postSave(bool $is_new): void
        {
            if ($is_new) {
                Event::createNew('core', 'Component::createNew', $this)->trigger();
            }
        }

        protected function _preDelete(): void
        {
            tables\IssueAffectsComponent::getTable()->deleteByComponentID($this->getID());
            tables\EditionComponents::getTable()->deleteByComponentID($this->getID());
        }

    }
