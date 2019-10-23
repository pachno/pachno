<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\IdentifiableScoped;

    /**
     * @Table(name="\pachno\core\entities\tables\ArticleCategories")
     */
    class ArticleCategory extends IdentifiableScoped
    {

        /**
         * The category name
         *
         * @var string
         * @Column(type="varchar", length=200)
         */
        protected $_name = null;

        /**
         * Board description
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_description;

        /**
         * Related project
         *
         * @var Project
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Project")
         */
        protected $_project_id;

        /**
         * @return Project
         */
        public function getProject()
        {
            return $this->_b2dbLazyLoad('_project_id');
        }

        public function setProject($project_id)
        {
            $this->_project_id = $project_id;
        }

        /**
         * Return the category name
         *
         * @return string
         */
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

    }
