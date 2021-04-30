<?php

    namespace pachno\core\entities\tables;

    use b2db\QueryColumnSort;

    /**
     * Issue tags table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     * @subpackage tables
     */

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

        const SCOPE = 'issue_tags.scope';

    }
