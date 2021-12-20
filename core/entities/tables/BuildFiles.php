<?php

    namespace pachno\core\entities\tables;

    use b2db\Insertion;
    use Exception;
    use pachno\core\entities\File;
    use pachno\core\entities\traits\FileLink;
    use pachno\core\framework;

    /**
     * Builds <-> Files table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="buildfiles")
     */
    class BuildFiles extends ScopedTable
    {

        use FileLink;

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'buildfiles';

        public const ID = 'buildfiles.id';

        public const SCOPE = 'buildfiles.scope';

        public const USER_ID = 'buildfiles.uid';

        public const ATTACHED_AT = 'buildfiles.attached_at';

        public const FILE_ID = 'buildfiles.file_id';

        public const BUILD_ID = 'buildfiles.build_id';

        public function addByBuildIDandFileID($build_id, $file_id, $attached_at = null, $scope_id = null)
        {
            $query = $this->getQuery();
            $query->where(self::BUILD_ID, $build_id);
            $query->where(self::FILE_ID, $file_id);
            $scope_id = $scope_id ?? framework\Context::getScope()->getID();
            $query->where(self::SCOPE, $scope_id);
            if ($this->count($query) == 0) {
                $insertion = new Insertion();
                $insertion->add(self::SCOPE, $scope_id);
                if ($attached_at === null) {
                    $insertion->add(self::ATTACHED_AT, NOW);
                } else {
                    $insertion->add(self::ATTACHED_AT, $attached_at);
                }
                $insertion->add(self::BUILD_ID, $build_id);
                $insertion->add(self::FILE_ID, $file_id);
                $this->rawInsert($insertion);
            }
        }

        public function getByBuildID($build_id)
        {
            $query = $this->getQuery();
            $query->where(self::BUILD_ID, $build_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $res = $this->rawSelect($query);

            $ret_arr = [];

            if ($res) {
                while ($row = $res->getNextRow()) {
                    try {
                        $file = new File($row->get(Files::ID), $row);
                        $file->setUploadedAt($row->get(self::ATTACHED_AT));
                        $ret_arr[$row->get(Files::ID)] = $file;
                    } catch (Exception $e) {
                        $this->rawDeleteById($row->get(self::ID));
                    }
                }
            }

            return $ret_arr;
        }

        public function removeByBuildIDandFileID($build_id, $file_id)
        {
            $query = $this->getQuery();
            $query->where(self::BUILD_ID, $build_id);
            $query->where(self::FILE_ID, $file_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $this->rawDelete($query);
        }

        public function deleteFilesByBuildID($build_id)
        {
            $query = $this->getQuery();
            $query->where(self::BUILD_ID, $build_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $this->rawDelete($query);
        }

        public function getBuildsByFileID($file_id)
        {
            $query = $this->getQuery();
            $query->where(self::FILE_ID, $file_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $build_ids = [];
            if ($res = $this->rawSelect($query)) {
                while ($row = $res->getNextRow()) {
                    $a_id = $row->get(self::BUILD_ID);
                    $build_ids[$a_id] = $a_id;
                }
            }

            return $build_ids;
        }

        protected function initialize(): void
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::USER_ID, Users::getTable(), Users::ID);
            parent::addForeignKeyColumn(self::BUILD_ID, Builds::getTable(), Builds::ID);
            parent::addForeignKeyColumn(self::FILE_ID, Files::getTable(), Files::ID);
            parent::addInteger(self::ATTACHED_AT, 10);
        }

    }
