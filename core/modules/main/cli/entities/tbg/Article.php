<?php

    namespace pachno\core\modules\main\cli\entities\tbg;

    use \pachno\core\framework;

    /**
     * @Table(name="\pachno\core\modules\main\cli\entities\tbg\tables\Articles")
     */
    class Article extends \pachno\core\entities\common\IdentifiableScoped
    {

        const TYPE_WIKI = 1;
        const TYPE_MANUAL = 2;

        /**
         * The article author
         *
         * @var \pachno\core\entities\User
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_author = null;

        /**
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * @Column(type="string", length=200)
         */
        protected $_manual_name;

        /**
         * When the article was posted
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_date = null;

        /**
         * What type of article this is
         *
         * @var integer
         * @Column(type="integer", length=10, default=1)
         */
        protected $_article_type = self::TYPE_WIKI;

        /**
         * The old article content, used for history when saving
         *
         * @var string
         */
        protected $_old_content = null;

        /**
         * The article content
         *
         * @var string
         * @Column(type="text")
         */
        protected $_content = null;

        /**
         * The article content syntax
         *
         * @var integer
         * @Column(type="integer", length=3, default=1)
         */
        protected $_content_syntax = framework\Settings::SYNTAX_MW;

        /**
         * Whether the article is published or not
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_is_published = false;

        /**
         * The parent article, if this article has one
         *
         * @var \pachno\core\entities\Article
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Article")
         */
        protected $_parent_article_id = false;

        /**
         * Child article, if this article has any
         *
         * @var array|\pachno\core\entities\Article
         * @Relates(class="\pachno\core\entities\Article", collection=true, foreign_column="parent_article_id", orderby="name")
         */
        protected $_child_articles = null;

        /**
         * Array of users that are subscribed to this issue
         *
         * @var array
         * @Relates(class="\pachno\core\entities\User", collection=true, manytomany=true, joinclass="\pachno\core\entities\tables\UserArticles")
         */
        protected $_subscribers = null;

    }
