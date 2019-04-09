<?php

    namespace pachno\core\entities\tables;

    use b2db\Insertion;
    use pachno\core\framework,
        pachno\core\entities\tables\ScopedTable,
        b2db\Criteria;

    /**
     * @Table(name="article_unique_categories")
     * @Entity(class="\pachno\core\entities\ArticleCategory")
     */
    class ArticleCategories extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'article_unique_categories';
        const ID = 'article_unique_categories.id';
        const NAME = 'article_unique_categories.category_name';
        const SCOPE = 'article_unique_categories.scope';

    }
