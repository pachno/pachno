<?php

    namespace pachno\core\entities\tables;

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
