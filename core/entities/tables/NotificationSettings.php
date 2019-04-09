<?php

    namespace pachno\core\entities\tables;

    use pachno\core\entities\tables\ScopedTable;

    /**
     * User notification settings table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.3
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * User notification settings table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="notificationsettings")
     * @Entity(class="\pachno\core\entities\NotificationSetting")
     */
    class NotificationSettings extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 2;

        public function getByModuleAndNameAndUserId($module, $name, $user_id)
        {
            $query = $this->getQuery();
            $query->where('notificationsettings.module_name', $module);
            $query->where('notificationsettings.name', $name);
            $query->where('notificationsettings.user_id', $user_id);

            return $this->selectOne($query);
        }

    }
