<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\Update;
    use pachno\core\entities\BoardColumn;

    /**
     * Agile board columns table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

    /**
     * Agile board columns table
     *
     * @package pachno
     * @subpackage tables
     *
     * @method static BoardColumns getTable() Retrieves an instance of this table
     * @method BoardColumn selectById(integer $id) Retrieves an agile board
     *
     * @Table(name="agileboard_columns")
     * @Entity(class="\pachno\core\entities\BoardColumn")
     */
    class BoardColumns extends ScopedTable
    {

        public const SCOPE = 'agileboard_columns.scope';
        
        public function updateSortOrderByBoardId($board_id, $order, $old_order, $column_id)
        {
            $update = new Update();
            $update->update('agileboard_columns.sort_order', $old_order);
            
            $query = $this->getQuery();
            $query->where('agileboard_columns.sort_order', $order);
            $query->where('agileboard_columns.board_id', $board_id);
            $query->where('agileboard_columns.id', $column_id, Criterion::NOT_EQUALS);
            
            $this->rawUpdate($update, $query);
        }

    }
