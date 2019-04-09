<?php

    namespace pachno\core\entities;

    use pachno\core\entities\Article;
    use \pachno\core\framework;

    /**
     * @Table(name="\pachno\core\entities\tables\ArticleLinks")
     */
    class ArticleLink extends \pachno\core\entities\common\IdentifiableScoped
    {

        /**
         * The original article
         *
         * @var \pachno\core\entities\Article
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Article")
         */
        protected $_article_id = null;

        /**
         * The article being linked to
         *
         * @var \pachno\core\entities\Article
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Article")
         */
        protected $_linked_article_id = null;

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
        public function getLinkedArticle()
        {
            return $this->_b2dbLazyLoad('_linked_article_id');
        }

        /**
         * @param Article|int $linked_article_id
         */
        public function setLinkedArticle($linked_article_id)
        {
            $this->_linked_article_id = $linked_article_id;
        }

    }
