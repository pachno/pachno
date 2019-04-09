<?php

    namespace pachno\core\entities\tables;

    use pachno\core\entities\tables\ScopedTable;

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
     * @method \pachno\core\entities\tables\BoardColumns getTable() Retrieves an instance of this table
     * @method \pachno\core\entities\BoardColumn selectById(integer $id) Retrieves an agile board
     *
     * @Table(name="agileboard_columns")
     * @Entity(class="\pachno\core\entities\BoardColumn")
     */
    class BoardColumns extends ScopedTable
    {

        const SCOPE = 'agileboard_columns.scope';

    }
