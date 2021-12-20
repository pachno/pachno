<?php

    namespace pachno\core\entities\tables;

    /**
     * Issue tags table
     *
     * @package pachno
     * @subpackage tables
     *
     * @Table(name="issue_tags")
     * @Entity(class="\pachno\core\entities\IssueTag")
     */
    class IssueTags extends ScopedTable
    {

        public const SCOPE = 'issue_tags.scope';

    }
