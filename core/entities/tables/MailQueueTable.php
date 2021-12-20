<?php

    namespace pachno\core\entities\tables;

    use b2db\Criterion;
    use b2db\Insertion;
    use b2db\QueryColumnSort;
    use pachno\core\framework;
    use Swift_Message;

    /**
     * @Table(name="mailing_queue")
     */
    class MailQueueTable extends ScopedTable
    {

        public const B2DB_TABLE_VERSION = 1;

        public const B2DBNAME = 'mailing_queue';

        public const ID = 'mailing_queue.id';

        public const MESSAGE = 'mailing_queue.headers';

        public const DATE = 'mailing_queue.date';

        public const SCOPE = 'mailing_queue.scope';

        public const SUBJECT = 'mailing_queue.subject';

        public const FROM = 'mailing_queue.from';

        public const TO = 'mailing_queue.to';

        public const MESSAGE_HTML = 'mailing_queue.part';

        public function addMailToQueue(Swift_Message $mail)
        {
            $insertion = new Insertion();
            $insertion->add(self::SUBJECT, $mail->getSubject());
            $insertion->add(self::FROM, serialize($mail->getFrom()));
            $insertion->add(self::TO, serialize($mail->getTo()));
            $insertion->add(self::MESSAGE, $mail->getBody());
            $insertion->add(self::MESSAGE_HTML, serialize($mail->getChildren()));
            $insertion->add(self::DATE, NOW);
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());

            $res = $this->rawInsert($insertion);

            return $res->getInsertID();
        }

        public function getQueuedMessages($limit = null)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            if ($limit !== null) {
                $query->setLimit($limit);
            }
            $query->addOrderBy(self::DATE, QueryColumnSort::SORT_ASC);

            $messages = [];
            $res = $this->rawSelect($query);

            if ($res) {
                while ($row = $res->getNextRow()) {
                    $message = new Swift_Message();
                    $message->setSubject($row->get(self::SUBJECT));
                    $message->setFrom(unserialize($row->get(self::FROM)));
                    $message->setTo(unserialize($row->get(self::TO)));
                    $message->setBody($row->get(self::MESSAGE));
                    $message->setChildren(unserialize($row->get(self::MESSAGE_HTML)));

                    $messages[$row->get(self::ID)] = $message;
                }
            }

            return $messages;
        }

        public function deleteProcessedMessages($ids)
        {
            $query = $this->getQuery();
            $query->where(self::ID, (array)$ids, Criterion::IN);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $res = $this->rawDelete($query);
        }

        protected function initialize(): void
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addVarchar(self::SUBJECT, 255);
            parent::addVarchar(self::FROM, 255);
            parent::addText(self::TO);
            parent::addText(self::MESSAGE);
            parent::addText(self::MESSAGE_HTML);
            parent::addInteger(self::DATE, 10);
        }

    }
