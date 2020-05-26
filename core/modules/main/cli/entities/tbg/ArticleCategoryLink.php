<?php

    namespace pachno\core\modules\main\cli\entities\tbg;

    use pachno\core\entities\common\IdentifiableScoped;

    /**
     * @Table(name="\pachno\core\modules\main\cli\entities\tbg\tables\ArticleCategoryLinks")
     */
    class ArticleCategoryLink extends IdentifiableScoped
    {

        /**
         * The category name
         *
         * @var string
         * @Column(type="varchar", length=300)
         */
        protected $_article_name = null;

        /**
         * The category name
         *
         * @var string
         * @Column(type="varchar", length=300)
         */
        protected $_category_name = null;

    }
