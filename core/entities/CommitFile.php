<?php

    namespace pachno\core\entities;

    use pachno\core\helpers\Diffable;

    /**
     * Commit file
     *
     * @package pachno
     * @subpackage livelink
     *
     * @Table(name="\pachno\core\entities\tables\CommitFiles")
     */
    class CommitFile extends common\IdentifiableScoped implements Diffable
    {

        public const ACTION_ADDED = 'A';

        public const ACTION_UPDATED = 'U';

        public const ACTION_MODIFIED = 'M';

        public const ACTION_DELETED = 'D';

        public const ACTION_RENAMED = 'R';

        /**
         * File path
         * @var string
         * @Column(type="text")
         */
        protected $_file_name = null;

        /**
         * Action applied to file (Added, Updated or Deleted)
         * @var string
         * @Column(type="string", length=1)
         */
        protected $_action = null;

        /**
         * Associated commit
         * @var Commit
         *
         * @Column(type="integer")
         * @Relates(class="\pachno\core\entities\Commit")
         */
        protected $_commit_id = null;

        /**
         * Associated commit
         * @var CommitFileDiff[]
         *
         * @Relates(class="\pachno\core\entities\CommitFileDiff", collection=true, foreign_column="commit_file_id", orderby="id")
         */
        protected $_commit_file_diffs = null;

        /**
         * Misc data
         * @var array
         * @Column(type="serializable", length=500)
         */
        protected $_data = [];

        protected $_lines_removed;

        protected $_lines_added;

        /**
         * Get the file action
         * @return string
         */
        public function getAction()
        {
            return $this->_action;
        }

        /**
         * Set the action applied (M/A/D)
         *
         * @param string $action
         */
        public function setAction($action)
        {
            $this->_action = $action;
        }

        /**
         * Get the commit with this link
         * @return Commit
         */
        public function getCommit()
        {
            return $this->_b2dbLazyLoad('_commit_id');
        }

        /**
         * Set the file path
         *
         * @param string $path
         */
        public function setPath($path)
        {
            $this->_file_name = $path;
        }

        public function getFilename()
        {
            return basename($this->_file_name);
        }

        public function getDirectory()
        {
            return trim(dirname($this->_file_name), " /");
        }

        /**
         * Set the commit this change applies to
         *
         * @param Commit $commit
         */
        public function setCommit(Commit $commit)
        {
            $this->_commit_id = $commit;
        }

        public function getFontAwesomeIcon()
        {
            switch ($this->getExtension()) {
                case 'png':
                case 'webp':
                case 'bpg':
                case 'jpg':
                case 'jpeg':
                case 'gif':
                case 'bmp':
                case 'tiff':
                case 'raw':
                    return 'file-image';
                case 'xls':
                case 'xlsx':
                case 'ods':
                    return 'file-excel';
                case 'doc':
                case 'docx':
                case 'odt':
                    return 'file-word';
                case 'ppt':
                case 'pptx':
                case 'odp':
                    return 'file-powerpoint';
                case 'pdf':
                    return 'file-pdf';
                case 'wav':
                case 'ogg':
                case 'mp3':
                    return 'file-audio';
                default:
                    return 'file-code';
            }
        }

        protected function getExtension()
        {
            return substr(strrchr($this->getPath(), '.'), 1);
        }

        /**
         * Get the file path
         * @return string
         */
        public function getPath()
        {
            return $this->_file_name;
        }

        public function getFontAwesomeIconStyle()
        {
            return 'far';
        }

        public function getCommitFileDiffs()
        {
            return $this->getDiffs();
        }

        /**
         * @return CommitFileDiff[]
         */
        public function getDiffs()
        {
            return $this->_b2dbLazyLoad('_commit_file_diffs');
        }

        /**
         * @return array
         */
        public function getData()
        {
            return $this->_data;
        }

        /**
         * @param array $data
         */
        public function setData($data)
        {
            $this->_data = $data;
        }

        /**
         * @return int
         */
        public function getLinesRemoved()
        {
            if ($this->_lines_removed === null) {
                $this->_lines_removed = 0;
                foreach ($this->getDiffs() as $diff) {
                    $this->_lines_removed += $diff->getLinesRemoved();
                }
            }

            return $this->_lines_removed;
        }

        /**
         * @return int
         */
        public function getLinesAdded()
        {
            if ($this->_lines_added === null) {
                $this->_lines_added = 0;
                foreach ($this->getDiffs() as $diff) {
                    $this->_lines_added += $diff->getLinesAdded();
                }
            }

            return $this->_lines_added;
        }

    }
