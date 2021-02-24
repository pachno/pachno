<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\Insertion;
    use b2db\Query;
    use pachno\core\entities\File;
    use pachno\core\entities\traits\FileLink;
    use pachno\core\framework;

    /**
     * Issues <-> Files table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="issuefiles")
     */
    class IssueFiles extends ScopedTable
    {

        use FileLink;

        const B2DB_TABLE_VERSION = 1;

        const B2DBNAME = 'issuefiles';

        const ID = 'issuefiles.id';

        const SCOPE = 'issuefiles.scope';

        const UID = 'issuefiles.uid';

        const ATTACHED_AT = 'issuefiles.attached_at';

        const FILE_ID = 'issuefiles.file_id';

        const ISSUE_ID = 'issuefiles.issue_id';

        protected $_preloaded_issue_counts;

        public function addByIssueIDandFileID($issue_id, $file_id, $timestamp = null)
        {
            $query = $this->getQuery();
            $query->where(self::ISSUE_ID, $issue_id);
            $query->where(self::FILE_ID, $file_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            if ($this->count($query) == 0) {
                $insertion = new Insertion();
                $insertion->add(self::SCOPE, framework\Context::getScope()->getID());
                if ($timestamp === null) {
                    $insertion->add(self::ATTACHED_AT, NOW);
                } else {
                    $insertion->add(self::ATTACHED_AT, $timestamp);
                }
                $insertion->add(self::ISSUE_ID, $issue_id);
                $insertion->add(self::FILE_ID, $file_id);
                $this->rawInsert($insertion);

                return true;
            }

            return false;
        }

        public function getByIssueID($issue_id)
        {
            $query = $this->getQuery();
            $query->where(self::ISSUE_ID, $issue_id);
            $query->join(Files::getTable(), Files::ID, self::FILE_ID);
            $res = $this->rawSelect($query, false);

            $ret_arr = [];

            if ($res) {
                while ($row = $res->getNextRow()) {
                    $file = new File($row->get(Files::ID), $row);
                    $file->setUploadedAt($row->get(self::ATTACHED_AT));
                    $ret_arr[$row->get(Files::ID)] = $file;
                }
            }

            return $ret_arr;
        }

        public function preloadIssueFileCounts($target_ids)
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::ID, 'num_files', Query::DB_COUNT);
            $query->addSelectionColumn(self::ISSUE_ID);
            $query->where(self::ISSUE_ID, $target_ids, Criterion::IN);
            $query->addGroupBy(self::ISSUE_ID);

            $res = $this->rawSelect($query, false);
            $this->_preloaded_issue_counts = [];
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $this->_preloaded_issue_counts[$row->get(self::ISSUE_ID)] = $row->get('num_files');
                }
            }
        }

        public function clearPreloadedIssueFileCounts()
        {
            $this->_preloaded_issue_counts = null;
        }

        public function getPreloadedIssueFileCount($target_id)
        {
            if (!is_array($this->_preloaded_issue_counts)) return null;

            if (isset($this->_preloaded_issue_counts[$target_id])) {
                $val = $this->_preloaded_issue_counts[$target_id];
                unset($this->_preloaded_issue_counts[$target_id]);

                return $val;
            }

            return 0;
        }

        public function countByIssueID($issue_id)
        {
            $query = $this->getQuery();
            $query->where(self::ISSUE_ID, $issue_id);

            return $this->count($query);
        }

        public function getIssuesByFileID($file_id)
        {
            $query = $this->getQuery();
            $query->where(self::FILE_ID, $file_id);

            $issue_ids = [];
            if ($res = $this->rawSelect($query)) {
                while ($row = $res->getNextRow()) {
                    $i_id = $row->get(self::ISSUE_ID);
                    $issue_ids[$i_id] = $i_id;
                }
            }

            return $issue_ids;
        }

        public function removeByIssueIDandFileID($issue_id, $file_id)
        {
            $query = $this->getQuery();
            $query->where(self::ISSUE_ID, $issue_id);
            $query->where(self::FILE_ID, $file_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $this->rawDelete($query);
        }

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addForeignKeyColumn(self::UID, Users::getTable(), Users::ID);
            parent::addForeignKeyColumn(self::ISSUE_ID, Issues::getTable(), Issues::ID);
            parent::addForeignKeyColumn(self::FILE_ID, Files::getTable(), Files::ID);
            parent::addInteger(self::ATTACHED_AT, 10);
        }

        protected function setupIndexes()
        {
            $this->addIndex('issueid', self::ISSUE_ID);
        }

    }
