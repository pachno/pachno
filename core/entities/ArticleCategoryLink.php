<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\IdentifiableScoped;

    /**
     * @Table(name="\pachno\core\entities\tables\ArticleCategoryLinks")
     */
    class ArticleCategoryLink extends IdentifiableScoped
    {

        /**
         * The original article
         *
         * @var Article
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Article")
         */
        protected $_article_id = null;

        /**
         * The category name
         *
         * @var string
         * @Column(type="varchar", length=300)
         */
        protected $_article_name = null;

        /**
         * The category
         *
         * @var Article
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Article")
         */
        protected $_category_id = null;

        /**
         * The category name
         *
         * @var string
         * @Column(type="varchar", length=300)
         */
        protected $_category_name = null;

        /**
         * @return Article
         */
        public function getArticle()
        {
            return $this->_b2dbLazyLoad('_article_id');
        }

        /**
         * @param Article|int $article_id
         */
        public function setArticle($article_id)
        {
            $this->_article_id = $article_id;
        }

        /**
         * @return Article
         */
        public function getCategory()
        {
            return $this->_b2dbLazyLoad('_category_id');
        }

        /**
         * @param Article|int $category_id
         */
        public function setCategory($category_id)
        {
            $this->_category_id = $category_id;
        }

    }
