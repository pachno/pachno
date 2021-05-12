<?php

    namespace pachno\core\entities;

    use pachno\core\entities\common\IdentifiableScoped;
    use pachno\core\entities\tables\ArticleFiles;
    use pachno\core\framework\Context;
    use pachno\core\framework\Event;
    use pachno\core\framework\Settings;

    /**
     * @Table(name="\pachno\core\entities\tables\Files")
     */
    class File extends IdentifiableScoped
    {

        const TYPE_PROJECT_ICON = 'project_icon';
        const TYPE_ATTACHMENT = 'attachment';
        const TYPE_COVER = 'cover';
        const TYPE_DOWNLOAD = 'download';

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
         * @Column(type="string", length=200)
         */
        protected $_type;

        /**
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\User")
         */
        protected $_uid;

        /**
         * The project
         *
         * @var Project
         * @Column(type="integer", length=10)
         * @Relates(class="\pachno\core\entities\Project")
         */
        protected $_project_id;

        /**
         * Returns the parent project
         *
         * @return Project
         */
        public function getProject()
        {
            return $this->_b2dbLazyLoad('_project_id');
        }

        public function setProject($project)
        {
            $this->_project_id = $project;
        }

        public static function getMimeType($filename)
        {
            $content_type = null;
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
                $content_type = finfo_file($finfo, $filename);
                finfo_close($finfo);
            } elseif (function_exists('mime_content_type')) {
                $content_type = mime_content_type($filename);
            }

            return $content_type;
        }

        public function getIcon()
        {
            $filename = $this->getOriginalFilename();
            if (!$filename || strpos($filename, '.') === false) {
                return 'file';
            }

            $extension = strtolower(explode('.', $filename)[1]);
            if (in_array($extension, ['php', 'cpp', 'h', 'java', 'js', 'tsx', 'jsp', 'pl', 'ts', 'jsx', 'htm', 'html', 'asp', 'rb', 'class', 'cmd', 'cxx', 'json', 'patch', 'xml', 'css', 'scss', 'yml', 'xlf'])) {
                return 'file-code';
            } elseif (in_array($extension, ['jpg', 'jpeg', 'bmp', 'png', 'webp', 'gif', 'img', 'svg'])) {
                return 'file-image';
            } elseif (in_array($extension, ['ppt', 'pptx', 'odp', 'odpt'])) {
                return 'file-powerpoint';
            } elseif (in_array($extension, ['doc', 'docx', 'odt', 'odtt', 'gddoc'])) {
                return 'file-word';
            } elseif (in_array($extension, ['xls', 'xlsx', 'ods', 'odst'])) {
                return 'file-excel';
            } elseif (in_array($extension, ['mpg', 'mp4', 'mpeg', 'avi', 'mov'])) {
                return 'file-video';
            } elseif (in_array($extension, ['txt', 'info', 'nfo', 'inf', 'ini', 'cfg', 'md', 'conf', 'log'])) {
                return 'file-alt';
            } elseif (in_array($extension, ['zip', 'rar', 'gz', 'tar', 'xz', 'jar', 'deb', 'rpm', 'appimage', 'flatpak'])) {
                return 'file-archive';
            } elseif (in_array($extension, ['exe', 'bat'])) {
                return 'window-maximize';
            } elseif (in_array($extension, ['wav', 'mp3', 'ogg'])) {
                return 'file-audio';
            } elseif ($extension === 'csv') {
                return 'file-csv';
            } elseif ($extension === 'pdf') {
                return 'file-pdf';
            }

            return 'file';
        }

        public function getContentType()
        {
            return $this->_content_type;
        }

        public function setContentType($content_type)
        {
            $this->_content_type = $content_type;
        }

        public function isImage()
        {
            return in_array($this->_content_type, self::getImageContentTypes());
        }

        public static function getImageContentTypes()
        {
            return ['image/png', 'image/jpeg', 'image/jpg', 'image/bmp', 'image/gif', 'image/svg', 'image/svg+xml'];
        }

        /**
         * @return User
         */
        public function getUploadedBy()
        {
            return $this->_b2dbLazyLoad('_uid');
        }

        public function setUploadedBy($uploaded_by)
        {
            $this->_uid = $uploaded_by;
        }

        public function getUploadedAt()
        {
            return $this->_uploaded_at;
        }

        public function setUploadedAt($uploaded_at)
        {
            $this->_uploaded_at = $uploaded_at;
        }

        public function getOriginalFilename()
        {
            return $this->getName();
        }

        public function getName()
        {
            return $this->_name;
        }

        public function setName($name)
        {
            $this->_name = $name;
        }

        public function setOriginalFilename($original_filename)
        {
            $this->setName($original_filename);
        }

        public function getContent()
        {
            return $this->_content;
        }

        public function setContent($content)
        {
            $this->_content = $content;
        }

        public function getReadableFilesize()
        {
            $size = $this->getSize();
            if ($size > 1024 * 1024) {
                return round(($size * 100 / (1024 * 1024)) / 100, 2) . 'MB';
            } elseif ($size < 1024) {
                return $size . 'B';
            } else {
                return round(($size * 100 / 1024) / 100, 2) . 'KB';
            }
        }

        public function getSize()
        {
            return $this->_size;
        }

        public function hasDescription()
        {
            return (bool)($this->getDescription() != '');
        }

        public function getDescription()
        {
            return $this->_description;
        }

        public function setDescription($description)
        {
            $this->_description = $description;
        }

        public function move($target_path)
        {
            if (Settings::getUploadStorage() == 'files') {
                rename($this->getFullpath(), Settings::getUploadsLocalpath() . $target_path);
            }
            $this->setRealFilename($target_path);
            $this->save();
        }

        public function hasAccess()
        {
            $issue_ids = tables\IssueFiles::getTable()->getIssuesByFileID($this->getID());

            foreach ($issue_ids as $issue_id) {
                $issue = new Issue($issue_id);
                if ($issue->hasAccess())
                    return true;
            }

            if ($this->getProject() instanceof Project) {
                return $this->getProject()->hasAccess();
            }

            return true;
//            $event = Event::createNew('core', 'pachno\core\entities\File::hasAccess', $this);
//            $event->setReturnValue(false);
//            $event->triggerUntilProcessed();
//
//            return $event->getReturnValue();
        }

        protected function _preDelete()
        {
            if ($this->doesFileExistOnDisk()) {
                unlink($this->getFullpath());
            }
        }

        public function doesFileExistOnDisk()
        {
            return file_exists($this->getFullpath());
        }

        public function getFullpath()
        {
            return Settings::getUploadsLocalpath() . $this->getRealFilename();
        }

        public function getRealFilename()
        {
            return $this->_real_filename;
        }

        public function setRealFilename($real_filename)
        {
            $this->_real_filename = $real_filename;
        }

        /**
         * @return mixed
         */
        public function getType()
        {
            return $this->_type;
        }

        /**
         * @param mixed $type
         */
        public function setType($type)
        {
            $this->_type = $type;
        }

        protected function _preSave($is_new)
        {
            parent::_preSave($is_new);
            if ($is_new) {
                $this->_uploaded_at = NOW;
            }
            if ($this->doesFileExistOnDisk()) {
                $this->_size = filesize($this->getFullpath());
            }
        }

        public function getURL($relative = true)
        {
            return Context::getRouting()->generate('showfile', ['id' => $this->getID()], $relative);
        }

        public function toJSON($detailed = true)
        {
            return [
                'id' => $this->getID(),
                'type' => $this->getType(),
                'content_type' => $this->getContentType(),
                'is_image' => $this->isImage(),
                'icon' => $this->getIcon(),
                'url' => $this->getUrl()
            ];
        }

    }
