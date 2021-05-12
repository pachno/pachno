<?php

    namespace pachno\core\entities\tables;

    use b2db\Criteria;
    use b2db\Criterion;
    use b2db\Insertion;
    use b2db\Query;
    use b2db\Row;
    use b2db\Table;
    use b2db\Update;
    use pachno\core\entities\File;
    use pachno\core\framework;

    /**
     * Files table
     *
     * @package pachno
     * @subpackage tables
     *
     * @method static Files getTable()
     *
     * @method File[] select(Query $query, $join = 'all')
     * @method File selectById($id, Query $query = null, $join = 'all')
     *
     * @Table(name="files")
     * @Entity(class="\pachno\core\entities\File")
     */
    class Files extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;

        const B2DBNAME = 'files';

        const ID = 'files.id';

        const SCOPE = 'files.scope';

        const USER_ID = 'files.uid';

        const UPLOADED_AT = 'files.uploaded_at';

        const REAL_FILENAME = 'files.real_filename';

        const ORIGINAL_FILENAME = 'files.original_filename';

        const CONTENT_TYPE = 'files.content_type';

        const CONTENT = 'files.content';

        const DESCRIPTION = 'files.description';

        public function saveFile($real_filename, $original_filename, $content_type, $description = null, $content = null)
        {
            $insertion = new Insertion();
            $insertion->add(self::USER_ID, framework\Context::getUser()->getID());
            $insertion->add(self::REAL_FILENAME, $real_filename);
            $insertion->add(self::UPLOADED_AT, NOW);
            $insertion->add(self::ORIGINAL_FILENAME, $original_filename);
            $insertion->add(self::CONTENT_TYPE, $content_type);
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
            if ($description !== null) {
                $insertion->add(self::DESCRIPTION, $description);
            }
            if ($content !== null) {
                $insertion->add(self::CONTENT, $content);
            }
            $res = $this->rawInsert($insertion);

            return $res->getInsertID();
        }

        public function getAllContentFiles()
        {
            $query = $this->getQuery();
            $query->where(self::CONTENT, null, Criteria::DB_IS_NOT_NULL);
            $query->where(self::CONTENT, '', Criterion::NOT_EQUALS);

            return $this->select($query);
        }

        public function getUnattachedFiles()
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::ID, 'id');

            $res = $this->rawSelect($query);
            $file_ids = [];
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $file_ids[$row['id']] = $row['id'];
                }
            }

            $file_ids = array_diff($file_ids, IssueFiles::getTable()->getLinkedFileIds($file_ids));

            $event = framework\Event::createNew('core', 'pachno\core\entities\\tables\Files::getUnattachedFiles', $this, ['file_ids' => $file_ids], []);
            $event->trigger();
            if ($event->isProcessed()) {
                foreach ($event->getReturnList() as $linked_file_ids) {
                    $file_ids = array_diff($file_ids, $linked_file_ids);
                }
            }
            $system_file_ids = Settings::getTable()->getFileIds();
            $file_ids = array_diff($file_ids, $system_file_ids);

            $project_file_ids = Projects::getTable()->getFileIds();
            $file_ids = array_diff($file_ids, $project_file_ids);

            return $file_ids;
        }

        public function getByID($id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $row = $this->rawSelectById($id, $query, false);

            return $row;
        }

        public function getByScopeID($scope_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, $scope_id);

            return $this->select($query);
        }

        public function getSizeByScopeID($scope_id)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, $scope_id);
            $query->addSelectionColumn('files.size', 'totalsize', Query::DB_SUM);

            $result = $this->rawSelectOne($query);

            return $result['totalsize'];
        }

        public function fixScopes()
        {
            $issue_file_scopes = [];
            $issue_files_query = IssueFiles::getTable()->getQuery();
            $issue_files_query->addSelectionColumn(IssueFiles::SCOPE);
            $issue_files_query->addSelectionColumn(IssueFiles::FILE_ID);

            $issue_files_res = IssueFiles::getTable()->rawSelect($issue_files_query);

            if (!$issue_files_res) {
                return;
            }

            while ($row = $issue_files_res->getNextRow()) {
                $issue_file_scopes[$row->get(IssueFiles::FILE_ID)] = $row->get(IssueFiles::SCOPE);
            }

            $query = $this->getQuery();
            $query->addSelectionColumn(self::ID);
            $query->where(self::SCOPE, 0);
            $res = $this->rawSelect($query);

            $fixRow = function (Row $row) use ($issue_file_scopes) {
                if (!isset($issue_file_scopes[$row->getID()])) {
                    return;
                }

                $update = new Update();
                $update->add(self::SCOPE, $issue_file_scopes[$row->getID()]);
                $this->rawUpdateById($update, $row->getID());
            };

            if ($res) {
                while ($row = $res->getNextRow()) {
                    $fixRow($row);
                }
            }
        }

        /**
         * @param $type
         * @return File[]
         */
        public function getByType($type): array
        {
            $query = $this->getQuery();
            $query->where('files.type', $type);

            return $this->select($query);
        }

    }
