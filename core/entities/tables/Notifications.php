<?php

    namespace pachno\core\entities\tables;

    use b2db\Core;
    use b2db\Criterion;
    use b2db\RawQuery;
    use b2db\Statement;
    use b2db\Table;
    use b2db\Update;
    use pachno\core\entities\Notification;
    use pachno\core\framework;

    /**
     * Notifications table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="notifications")
     * @Entity(class="\pachno\core\entities\Notification")
     */
    class Notifications extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 3;

        public const B2DBNAME = 'notifications';

        public const ID = 'notifications.id';

        public const SCOPE = 'notifications.scope';

        public const MODULE_NAME = 'notifications.module_name';

        public const NOTIFICATION_TYPE = 'notifications.notification_type';

        public const TARGET_ID = 'notifications.target_id';

        public const TRIGGERED_BY_UID = 'notifications.triggered_by_user_id';

        public const USER_ID = 'notifications.user_id';

        public const IS_READ = 'notifications.is_read';

        public const CREATED_AT = 'notifications.created_at';

        public const SHOWN_AT = 'notifications.shown_at';

        public function getCountsByUserIDAndGroupableMinutes($user_id, $minutes = 0)
        {
            if ($minutes <= 0 || !is_numeric($minutes)) return $this->getCountsByUserID($user_id);

            $notification_type_col = Core::getTablePrefix() . self::NOTIFICATION_TYPE;
            $created_at_col = Core::getTablePrefix() . self::CREATED_AT;
            $id_col = Core::getTablePrefix() . self::ID;
            $notification_type_issue_updated_col = Notification::TYPE_ISSUE_UPDATED;
            $b2dbname = Core::getTablePrefix() . self::B2DBNAME;
            $user_id_col = Core::getTablePrefix() . self::USER_ID;
            $triggered_by_uid_col = Core::getTablePrefix() . self::TRIGGERED_BY_UID;
            $is_read_col = Core::getTablePrefix() . self::IS_READ;
            $seconds = $minutes * 60;

            $custom_sql_unread = "SELECT SUM(subquery.custom_count) as custom_count FROM (SELECT {$notification_type_col}, {$created_at_col} DIV {$seconds}, COUNT({$id_col}) as real_count, (CASE WHEN {$notification_type_col} = '{$notification_type_issue_updated_col}' THEN 1 ELSE COUNT({$id_col}) END) as custom_count FROM {$b2dbname} WHERE {$user_id_col} = {$user_id} AND {$triggered_by_uid_col} != {$user_id} AND $is_read_col = 0 GROUP BY {$notification_type_col}, {$created_at_col} DIV {$seconds}, {$triggered_by_uid_col}) as subquery";
            $query = new RawQuery($custom_sql_unread);
            $statement = Statement::getPreparedStatement($query);
            $res = $statement->execute();
            if (!$res) {
                $unread_count = 0;
            } else {
                $resultset = $statement->fetch();
                $unread_count = is_null($resultset['custom_count']) ? 0 : $resultset['custom_count'];
            }

            $custom_sql_unread = "SELECT SUM(subquery.custom_count) as custom_count FROM (SELECT {$notification_type_col}, {$created_at_col} DIV {$seconds}, COUNT({$id_col}) as real_count, (CASE WHEN {$notification_type_col} = '{$notification_type_issue_updated_col}' THEN 1 ELSE COUNT({$id_col}) END) as custom_count FROM {$b2dbname} WHERE {$user_id_col} = {$user_id} AND {$triggered_by_uid_col} != {$user_id} AND $is_read_col = 1 GROUP BY {$notification_type_col}, {$created_at_col} DIV {$seconds}, {$triggered_by_uid_col}) as subquery";
            $query = new RawQuery($custom_sql_unread);
            $statement = Statement::getPreparedStatement($query);
            $res = $statement->execute();
            if (!$res) {
                $read_count = 0;
            } else {
                $resultset = $statement->fetch();
                $read_count = is_null($resultset['custom_count']) ? 0 : $resultset['custom_count'];
            }

            return [$unread_count, $read_count];
        }

        public function getCountsByUserID($user_id)
        {
            $query = $this->getQuery();
            $query->where(self::USER_ID, $user_id);
            $query->where(self::IS_READ, false);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::TRIGGERED_BY_UID, $user_id, Criterion::NOT_EQUALS);
            $unread_count = $this->count($query);

            $query = $this->getQuery();
            $query->where(self::USER_ID, $user_id);
            $query->where(self::IS_READ, true);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::TRIGGERED_BY_UID, $user_id, Criterion::NOT_EQUALS);
            $read_count = $this->count($query);

            return [$unread_count, $read_count];
        }

        public function getByUserIDAndGroupableMinutes($user_id, $minutes = 0)
        {
            if ($minutes <= 0 || !is_numeric($minutes)) return $this->getByUserID($user_id);

            $notification_type_issue_updated_col = Notification::TYPE_ISSUE_UPDATED;
            $seconds = $minutes * 60;

            list($target_id_col, $notification_type_col, $module_name_col, $is_read_col, $created_at_col, $triggered_by_user_id_col, $user_id_col, $shown_at_col, $scope_col, $id_col) = $this->getAliasColumns();

            $sql = 'SELECT ';
            $sql_selects = [];
            foreach ($this->getAliasColumns() as $column) $sql_selects[] = $column . ' AS ' . str_replace('.', '_', $column);

            $sql .= join(', ', $sql_selects);
            $sql .= ", (CASE WHEN {$notification_type_col} = '{$notification_type_issue_updated_col}' THEN 1 ELSE {$id_col} END) as {$this->b2db_alias}_custom_group_by";
            $sql .= ' FROM ' . Core::getTablePrefix() . $this->getB2DBName() . ' ' . $this->getB2DBAlias();
            $sql .= " WHERE {$user_id_col} = {$user_id} AND {$triggered_by_user_id_col} != {$user_id}";
            $sql .= " GROUP BY {$this->b2db_alias}_custom_group_by, {$created_at_col} DIV {$seconds}, {$triggered_by_user_id_col}";
            $sql .= " ORDER BY {$id_col} DESC";

            $query = new RawQuery($sql);
            $statement = Statement::getPreparedStatement($query);
            $resultset = $statement->execute();

            return $this->populateFromResultset(($resultset->count()) ? $resultset : null);
        }

        /**
         * @param $user_id
         * @return ?Notification[]
         */
        public function getByUserID($user_id): ?array
        {
            $query = $this->getQuery();
            $query->where(self::USER_ID, $user_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::TRIGGERED_BY_UID, $user_id, Criterion::NOT_EQUALS);
            $query->addOrderBy(self::ID, 'DESC');
            $query->setLimit(100);

            return $this->select($query);
        }

        public function markUserNotificationsReadByTypesAndIdAndGroupableMinutes($types, $id, $user_id, $minutes = 0, $is_read = 1, $mark_all = true)
        {
            if (!is_array($types)) $types = [$types];

            $notification_type_issue_updated_col = Notification::TYPE_ISSUE_UPDATED;

            if (($key = array_search($notification_type_issue_updated_col, $types)) === false || ($minutes <= 0 || !is_numeric($minutes))) {
                if (!$mark_all) return;

                return $this->markUserNotificationsReadByTypesAndId($types, $id, $user_id);
            }

            $cols = array_map(function ($col) {
                return str_replace(self::B2DBNAME . '.', '', $col);
            }, [
                'id' => self::ID,
                'target_id' => self::TARGET_ID,
                'created_at' => self::CREATED_AT,
                'is_read' => self::IS_READ,
                'notification_type' => self::NOTIFICATION_TYPE,
                'user_id' => self::USER_ID,
                'scope' => self::SCOPE,
            ]);

            $b2dbname = Core::getTablePrefix() . self::B2DBNAME;
            $seconds = $minutes * 60;
            $scope = framework\Context::getScope()->getID();

            $sub_sql = "SELECT {$cols['id']}, {$cols['target_id']}, ({$cols['created_at']} DIV {$seconds}) AS created_at_div FROM {$b2dbname} WHERE ";

            if (is_array($id)) {
                $sub_sql .= $cols['target_id'] . ' IN (' . implode(', ', $id) . ')';
            } else {
                $sub_sql .= "{$cols['target_id']} = {$id}";
            }

            $sql = "UPDATE {$b2dbname} a JOIN ({$sub_sql}) b ON a.{$cols['id']} = b.{$cols['id']} SET a.{$cols['is_read']} = {$is_read} WHERE (a.{$cols['notification_type']} = '{$notification_type_issue_updated_col}') AND (a.{$cols['user_id']} = {$user_id}) AND (a.{$cols['scope']} = {$scope}) AND ((a.{$cols['created_at']} DIV {$seconds}) * a.{$cols['created_at']} DIV (a.{$cols['created_at']})) IN (b.created_at_div)";

            $query = new RawQuery($sql);
            $statement = Statement::getPreparedStatement($query);
            $statement->execute();

            if (!$mark_all) return;

            unset($types[$key]);
            $this->markUserNotificationsReadByTypesAndId($types, $id, $user_id);
        }

        public function markUserNotificationsReadByTypesAndId($types, $id, $user_id)
        {
            if (!is_array($types)) $types = [$types];

            $query = $this->getQuery();
            $query->where(self::USER_ID, $user_id);
            if (count($types)) {
                if (is_array($id)) {
                    $query->where(self::TARGET_ID, $id, Criterion::IN);
                } else {
                    $query->where(self::TARGET_ID, $id);
                }
                $query->where(self::NOTIFICATION_TYPE, $types, Criterion::IN);
            }
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $update = new Update();
            $update->add(self::IS_READ, true);
            $this->rawUpdate($update, $query);

            $query = $this->getQuery();
            $query->where(self::USER_ID, $user_id);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            $query->where(self::IS_READ, true);
            $query->where('notifications.created_at', NOW - (86400 * 30), Criterion::LESS_THAN_EQUAL);
            $this->rawDelete($query);
        }

        protected function migrateData(Table $old_table): void
        {
            switch ($old_table::B2DB_TABLE_VERSION) {
                case 2:
                    $update = new Update();
                    $update->add(self::SHOWN_AT, time());
                    $this->rawUpdate($update);
                    break;
            }
        }

        protected function setupIndexes(): void
        {
            $this->addIndex('userid_targetid_notificationtype_scope', [self::USER_ID, self::TARGET_ID, self::NOTIFICATION_TYPE, self::SCOPE]);
        }

    }
