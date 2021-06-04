<?php

    namespace pachno\core\entities\tables;

    /**
     * @Table(name="articleviews")
     */
    class ArticleViews extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'articleviews';

        public const ID = 'articleviews.id';

        public const ARTICLE_ID = 'articleviews.article_id';

        public const USER_ID = 'articleviews.user_id';

        public const SCOPE = 'articleviews.scope';

        protected function initialize(): void
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::USER_ID, Users::getTable(), Users::ID);
            parent::addForeignKeyColumn(self::ARTICLE_ID, Articles::getTable(), Articles::ID);
        }
    }

