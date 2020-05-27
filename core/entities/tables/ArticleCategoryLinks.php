<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\Insertion;
    use b2db\Query;
    use b2db\QueryColumnSort;
    use b2db\Table;
    use b2db\Update;
    use pachno\core\entities\Article;
    use pachno\core\framework;

    /**
     * @method ArticleCategoryLinks[] select(Query $query, $join = 'all')
     *
     * @Table(name="articlecategories")
     * @Entity(class="\pachno\core\entities\ArticleCategoryLink")
     */
    class ArticleCategoryLinks extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'articlecategories';
        const ID = 'articlecategories.id';
        const ARTICLE_ID = 'articlecategories.article_id';
        const CATEGORY_ID = 'articlecategories.category_id';
        const ARTICLE_NAME = 'articlecategories.article_name';
        const CATEGORY_NAME = 'articlecategories.category_name';
        const SCOPE = 'articlecategories.scope';

        /**
         * @param $article_id
         *
         * @return ArticleCategoryLinks[]
         */
        public function getCategoriesByArticleId($article_id)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE_ID, $article_id);
            $query->where(self::CATEGORY_ID, 0, Criterion::NOT_EQUALS);

            return $this->select($query);
        }

        /**
         * @param $category_id
         *
         * @return ArticleCategoryLinks[]
         */
        public function getArticlesByCategoryId($category_id)
        {
            $query = $this->getQuery();
            $query->where(self::CATEGORY_ID, $category_id);
            $query->where(self::ARTICLE_ID, 0, Criterion::NOT_EQUALS);

            return $this->select($query);
        }

        /**
         * @param int[] $existing_article_ids
         *
         * @return int[]
         */
        public function getArticlesIdsWithCategory($existing_article_ids = null)
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::ARTICLE_ID, 'article_id', Query::DB_DISTINCT);
            if ($existing_article_ids !== null) {
                $query->where(self::ARTICLE_ID, $existing_article_ids, Criterion::NOT_IN);
            }

            $article_ids = [];
            $res = $this->rawSelect($query);
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $article_ids[] = $row['article_id'];
                }
            }

            return $article_ids;
        }

        /**
         * @param $category_id
         *
         * @return int
         */
        public function countArticlesByCategoryId($category_id)
        {
            $query = $this->getQuery();
            $query->where(self::CATEGORY_ID, $category_id);
            $query->where(self::ARTICLE_ID, 0, Criterion::NOT_EQUALS);

            return $this->count($query);
        }

        /**
         * @param $article_id
         *
         * @return int
         */
        public function countCategoriesByArticleId($article_id)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE_ID, $article_id);
            $query->where(self::CATEGORY_ID, 0, Criterion::NOT_EQUALS);

            return $this->count($query);
        }

        /**
         * @param $article_id
         */
        public function deleteByArticleId($article_id)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE_ID, $article_id);
            $query->or(self::CATEGORY_ID, $article_id);

            $this->rawDelete($query);
        }

        /**
         * @param $category_name
         *
         * @return int
         */
        public function countArticlesByCategoryName($category_name)
        {
            $query = $this->getQuery();
            $query->where(self::CATEGORY_NAME, $category_name);

            return $this->count($query);
        }

        public function updateArticleId($article_id, $article_name)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE_NAME, $article_name);
            $update = new Update();
            $update->update(self::ARTICLE_ID, $article_id);

            $this->rawUpdate($update, $query);
        }

        public function updateCategoryId($category_id, $category_name)
        {
            $query = $this->getQuery();
            $query->where(self::CATEGORY_NAME, $category_name);
            $update = new Update();
            $update->update(self::CATEGORY_ID, $category_id);

            $this->rawUpdate($update, $query);
        }

        public function removeDuplicate($article_id, $category_id, $id)
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE_ID, $article_id);
            $query->where(self::CATEGORY_ID, $category_id);
            $query->where(self::ID, $id, Criterion::NOT_EQUALS);
            $this->rawDelete($query);
        }

        public function getDuplicates()
        {
            $query = $this->getQuery();
            $query->where(self::ARTICLE_ID, 0);
            $query->or(self::CATEGORY_ID, 0);
            $this->rawDelete($query);

            $query = $this->getQuery();
            $query->addSelectionColumn(self::ID);
            $query->addSelectionColumn(self::ARTICLE_ID);
            $query->addSelectionColumn(self::CATEGORY_ID);
            $query->addGroupBy(self::CATEGORY_ID);
            $query->addGroupBy(self::ARTICLE_ID);
            $query->addGroupBy(self::ID);
            $res = $this->rawSelect($query);

            return $res;
        }

        protected function migrateData(Table $old_table)
        {

        }

    }
