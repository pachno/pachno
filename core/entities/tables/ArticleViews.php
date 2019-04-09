<?php

    namespace pachno\core\entities\tables;

    use pachno\core\entities\tables\Articles;
    use pachno\core\entities\tables\ScopedTable;

    /**
     * @Table(name="articleviews")
     */
    class ArticleViews extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'articleviews';
        const ID = 'articleviews.id';
        const ARTICLE_ID = 'articleviews.article_id';
        const USER_ID = 'articleviews.user_id';
        const SCOPE = 'articleviews.scope';

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::USER_ID, \pachno\core\entities\tables\Users::getTable(), \pachno\core\entities\tables\Users::ID);
            parent::addForeignKeyColumn(self::ARTICLE_ID, Articles::getTable(), Articles::ID);
        }
    }

