<?php

    namespace pachno\core\entities\tables;

    use b2db\QueryColumnSort;

    /**
     * @Table(name="mailing_incoming_email_account")
     * @Entity(class="\pachno\core\entities\IncomingEmailAccount")
     */
    class IncomingEmailAccounts extends ScopedTable
    {

        public const B2DBNAME = 'mailing_incoming_email_account';

        public const ID = 'mailing_incoming_email_account.id';

        public const NAME = 'mailing_incoming_email_account.name';

        public const USERNAME = 'mailing_incoming_email_account.username';

        public const PASSWORD = 'mailing_incoming_email_account.password';

        public const SERVER = 'mailing_incoming_email_account.server';

        public const PORT = 'mailing_incoming_email_account.port';

        public const FOLDER = 'mailing_incoming_email_account.folder';

        public const SERVER_TYPE = 'mailing_incoming_email_account.server_type';

        public const SSL = 'mailing_incoming_email_account.ssl';

        public const PREFER_HTML = 'mailing_incoming_email_account.prefer_html';

        public const KEEP_EMAIL = 'mailing_incoming_email_account.keep_email';

        public const PROJECT = 'mailing_incoming_email_account.project';

        public const ISSUETYPE = 'mailing_incoming_email_account.issuetype';

        public const NUM_LAST_FETCHED = 'mailing_incoming_email_account.num_last_fetched';

        public const TIME_LAST_FETCHED = 'mailing_incoming_email_account.time_last_fetched';

        public const SCOPE = 'mailing_incoming_email_account.scope';

        public function getAll()
        {
            $query = $this->getQuery();
            $query->addOrderBy('mailing_incoming_email_account.project', QueryColumnSort::SORT_ASC);

            return $this->select($query);
        }

        public function getAllByProjectID($project_id)
        {
            $query = $this->getQuery();
            $query->where(self::PROJECT, $project_id);

            return $this->select($query);
        }

    }
