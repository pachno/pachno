<?php

    namespace pachno\core\entities\tables;

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
     * @method BoardColumns getTable() Retrieves an instance of this table
     * @method BoardColumn selectById(integer $id) Retrieves an agile board
     *
     * @Table(name="agileboard_columns")
     * @Entity(class="\pachno\core\entities\BoardColumn")
     */
    class BoardColumns extends ScopedTable
    {

        public const SCOPE = 'agileboard_columns.scope';

    }
