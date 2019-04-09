<?php

    namespace pachno\core\entities\traits;

    /**
     * Trait for looking up files that are not linked
     *
     * @package pachno
     * @subpackage traits
     */
    trait FileLink
    {

        public function getLinkedFileIds()
        {
            $query = $this->getQuery();
            $query->addSelectionColumn(self::FILE_ID, 'file_id');

            $res = $this->rawSelect($query);
            $linked_file_ids = [];

            if ($res) {
                while ($row = $res->getNextRow()) {
                    $linked_file_ids[$row['file_id']] = $row['file_id'];
                }
            }

            return $linked_file_ids;
        }

    }
