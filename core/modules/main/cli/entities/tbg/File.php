<?php

    namespace pachno\core\modules\main\cli\entities\tbg;

    use pachno\core\entities\common\IdentifiableScoped;

    /**
     * @Table(name="\pachno\core\modules\main\cli\entities\tbg\tables\Files")
     */
    class File extends IdentifiableScoped
    {

        /**
         * @Column(type="string", length=200)
         */
        protected $_content_type;

        /**
         * @Column(type="integer", length=10)
         */
        protected $_uploaded_at;

        /**
         * @Column(type="string", length=200)
         */
        protected $_real_filename;

        /**
         * @Column(type="string", length=200, name="original_filename")
         */
        protected $_name;

        /**
         * @Column(type="blob")
         */
        protected $_content;

        /**
         * @Column(type="string", length=200)
         */
        protected $_description;

        /**
         * @Column(type="string", length=200)
         */
        protected $_size;

        /**
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_uid;

    }
