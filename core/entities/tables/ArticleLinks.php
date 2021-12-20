<?php

    namespace pachno\core\entities\tables;

    use b2db\Insertion;
    use pachno\core\framework;

    /**
     * @Table(name="articlelinks")
     * @Entity(class="\pachno\core\entities\ArticleLink")
     */
    class ArticleLinks extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'articlelinks';

        public const ID = 'articlelinks.id';

        public const ARTICLE_NAME = 'articlelinks.article_name';

        public const LINK_ARTICLE_NAME = 'articlelinks.link_article_name';

        public const SCOPE = 'articlelinks.scope';

        public function deleteLinksByArticle($article_name)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE_NAME, $article_name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawDelete($query);
        }

        public function addArticleLink($article_name, $linked_article_name)
        {
            $insertion = new Insertion();
            $insertion->add(self::ARTICLE_NAME, $article_name);
            $insertion->add(self::LINK_ARTICLE_NAME, $linked_article_name);
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawInsert($insertion);
        }

        public function getArticleLinks($article_name)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE_NAME, $article_name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawSelect($query);

            return $res;
        }

        public function getLinkingArticles($linked_article_name)
        {
            $query = $this->getQuery();
            $query->where(self::LINK_ARTICLE_NAME, $linked_article_name);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawSelect($query);

            return $res;
        }

        public function getUniqueArticleNames()
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::ARTICLE_NAME);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->setIsDistinct();

            $names = [];
            if ($res = $this->rawSelect($query)) {
                while ($row = $res->getNextRow()) {
                    $article_name = $row->get(self::ARTICLE_NAME);
                    $names[$article_name] = $article_name;
                }
            }

            return $names;
        }

        public function getUniqueLinkedArticleNames()
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::LINK_ARTICLE_NAME);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->setIsDistinct();

            $names = [];
            if ($res = $this->rawSelect($query)) {
                while ($row = $res->getNextRow()) {
                    $article_name = $row->get(self::LINK_ARTICLE_NAME);
                    $names[$article_name] = $article_name;
                }
            }

            return $names;
        }

        protected function initialize(): void
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addVarchar(self::ARTICLE_NAME, 300);
            parent::addVarchar(self::LINK_ARTICLE_NAME, 300);
        }

    }
