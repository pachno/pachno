<?php

    namespace pachno\core\modules\main\cli\entities\tbg;

    use pachno\core\entities\common\Releaseable;
    use pachno\core\entities\Edition;
    use pachno\core\entities\Milestone;

    /**
     * Class used for builds/versions
     *
     * @package pachno
     * @subpackage main
     *
     * @Table(name="\pachno\core\modules\main\cli\entities\tbg\tables\Builds")
     */
    class Build extends Releaseable
    {

        /**
         * The name of the object
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_name;

        /**
         * This builds edition
         *
         * @var Edition
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Edition")
         */
        protected $_edition;

        /**
         * This builds project
         *
         * @var Project
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Project")
         */
        protected $_project;

        /**
         * This builds milestone, if any
         *
         * @var Milestone
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Milestone")
         */
        protected $_milestone;

        /**
         * An attached file, if exists
         *
         * @var File
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\File")
         */
        protected $_file_id;

        /**
         * An url to download this releases file, if any
         *
         * @var string
         * @Column(type="string", length=255)
         */
        protected $_file_url;

        /**
         * Major version
         *
         * @var integer
         * @access protected
         * @Column(type="integer", length=5)
         */
        protected $_version_major = 0;

        /**
         * Minor version
         *
         * @var integer
         * @access protected
         * @Column(type="integer", length=5)
         */
        protected $_version_minor = 0;

        /**
         * Revision
         *
         * @var integer
         * @access protected
         * @Column(type="string", length=30)
         */
        protected $_version_revision = 0;

        /**
         * Whether the item is locked or not
         *
         * @var boolean
         * @access protected
         * @Column(type="boolean")
         */
        protected $_locked;

    }
