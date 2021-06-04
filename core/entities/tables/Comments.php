<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\Query;
    use b2db\QueryColumnSort;
    use b2db\Table;
    use b2db\Update;
    use pachno\core\framework;

    /**
     * Comments table
     *
     * @package pachno
     * @subpackage tables
     *
     * @method static Comments getTable()
     *
     * @Table(name="comments")
     * @Entity(class="\pachno\core\entities\Comment")
     * @Discriminator(column="target_type")
     * @Discriminators(\pachno\core\entities\Issue=1, \pachno\core\entities\Article=2, \pachno\core\entities\Commit=3)
     */
    class Comments extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 4;

        public const B2DBNAME = 'comments';

        public const ID = 'comments.id';

        public const SCOPE = 'comments.scope';

        public const TARGET_ID = 'comments.target_id';

        public const TARGET_TYPE = 'comments.target_type';

        public const CONTENT = 'comments.content';

        public const IS_PUBLIC = 'comments.is_public';

        public const POSTED_BY = 'comments.posted_by';

        public const POSTED = 'comments.posted';

        public const UPDATED_BY = 'comments.updated_by';

        public const UPDATED = 'comments.updated';

        public const DELETED = 'comments.deleted';

        public const MODULE = 'comments.module';

        public const COMMENT_NUMBER = 'comments.comment_number';

        public const SYSTEM_COMMENT = 'comments.system_comment';

        public const HAS_ASSOCIATED_CHANGES = 'comments.has_associated_changes';

        protected $_preloaded_counts = [];

        public function getComments($target_id, $target_type, $sort_order = QueryColumnSort::SORT_ASC)
        {
            $query = $this->getQuery();
            if ($target_id != 0) {
                $query->where(self::TARGET_ID, $target_id);
            }
            $query->where(self::TARGET_TYPE, $target_type);
            $query->where(self::DELETED, false);
            $query->where(self::SYSTEM_COMMENT, false);
            $query->addOrderBy(self::COMMENT_NUMBER, $sort_order);
            $res = $this->select($query, false);

            return $res;
        }

        public function getCommentIDs($target_id, $target_type, $sort_order = QueryColumnSort::SORT_ASC)
        {
            $query = $this->getQuery();
            if ($target_id != 0) {
                $query->where(self::TARGET_ID, $target_id);
            }
            $query->where(self::TARGET_TYPE, $target_type);
            $query->where(self::SYSTEM_COMMENT, false);
            $query->addSelectionColumn(self::ID);
            $query->addOrderBy(self::POSTED, $sort_order);
            $res = $this->rawSelect($query, false);

            $ids = [];
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $ids[] = $row[self::ID];
                }
            }

            return $ids;
        }

        public function countComments($target_id, $target_type)
        {
            $query = $this->getQuery();
            if ($target_id != 0) {
                $query->where(self::TARGET_ID, $target_id);
            }
            $query->where(self::TARGET_TYPE, $target_type);
            $query->where(self::DELETED, 0);
            $query->where(self::SYSTEM_COMMENT, false);

            return $this->count($query);
        }

        public function preloadCommentCounts($target_type, $target_ids)
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::ID, 'num_comments', Query::DB_COUNT);
            $query->addSelectionColumn(self::TARGET_ID, 'identifier');
            $query->where(self::TARGET_ID, $target_ids, Criterion::IN);
            $query->where(self::TARGET_TYPE, $target_type);
            $query->where(self::DELETED, 0);
            $query->where(self::SYSTEM_COMMENT, false);
            $query->addGroupBy(self::TARGET_ID);

            $res = $this->rawSelect($query, false);
            $this->_preloaded_counts[$target_type] = [];
            if ($res) {
                while ($row = $res->getNextRow()) {
                    $this->_preloaded_counts[$target_type][$row->get('identifier')] = $row->get('num_comments');
                }
            }
        }

        public function clearPreloadedCommentCounts($target_type)
        {
            unset($this->_preloaded_counts[$target_type]);
        }

        public function getPreloadedCommentCount($target_type, $target_id)
        {
            if (!array_key_exists($target_type, $this->_preloaded_counts) || !is_array($this->_preloaded_counts[$target_type])) return null;

            if (isset($this->_preloaded_counts[$target_type][$target_id])) {
                $val = $this->_preloaded_counts[$target_type][$target_id];
                unset($this->_preloaded_counts[$target_type][$target_id]);

                return $val;
            }

            return 0;
        }

        public function getNextCommentNumber($target_id, $target_type)
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::COMMENT_NUMBER, 'max_no', Query::DB_MAX, '', '+1');
            $query->where(self::TARGET_ID, $target_id);
            $query->where(self::TARGET_TYPE, $target_type);

            $row = $this->rawSelectOne($query);

            return ($row->get('max_no')) ? $row->get('max_no') : 1;
        }

        public function getRecentCommentsByUserIDandTargetType($user_id, $target_type, $limit = 10)
        {
            $query = $this->getQuery();
            $query->where(self::POSTED_BY, $user_id);
            $query->where(self::TARGET_TYPE, $target_type);
            $query->where(self::SYSTEM_COMMENT, false);
            $query->addOrderBy(self::POSTED, QueryColumnSort::SORT_DESC);
            $query->setLimit($limit);

            return $this->select($query);
        }

        public function fixFileComments()
        {
            $query = $this->getQuery();
            $query->where(self::CONTENT, 'A file was uploaded%', Criterion::LIKE);

            $update = new Update();
            $update->add(self::SYSTEM_COMMENT, true);

            $this->rawUpdate($update, $query);
        }

        protected function setupIndexes(): void
        {
            $this->addIndex('type_target', [self::TARGET_TYPE, self::TARGET_ID]);
            $this->addIndex('type_target_deleted_system', [self::TARGET_TYPE, self::TARGET_ID, self::DELETED, self::SYSTEM_COMMENT]);
        }

    }
