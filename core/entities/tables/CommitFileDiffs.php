<?php

    namespace pachno\core\entities\tables;

    /**
     * Commit file diffs table
     *
     * @method static CommitFiles getTable()
     *
     * @Entity(class="\pachno\core\entities\CommitFileDiff")
     * @Table(name="commitfile_diffs")
     */
    class CommitFileDiffs extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 2;

        public const B2DBNAME = 'commitfile_diffs';

        public const ID = 'commitfile_diffs.id';

        public const SCOPE = 'commitfile_diffs.scope';

    }
