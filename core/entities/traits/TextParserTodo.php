<?php

    namespace pachno\core\entities\traits;

    use pachno\core\framework\Context;

    /**
     * Trait for looking up files that are not linked
     *
     * @package pachno
     * @subpackage traits
     */
    trait TextParserTodo
    {

        protected $todo_regex = '(\[(?P<closed>x)?\] )(?P<text>.*?)';

        protected $todos = [];

        public function getTodos()
        {
            return $this->todos;
        }

        protected function _parse_todo($matches)
        {
            Context::loadLibrary('ui');
            if (!isset($matches)) return '';

            $is_closed = (isset($matches['closed']) && $matches['closed'] != '');
            $this->todos[] = [
                'closed' => $is_closed,
                'text' => $matches['text']
            ];
            $image = ($is_closed) ? 'check-square' : 'square';

            return '<br>' . fa_image_tag($image, ['class' => 'todo-checkbox']) . $this->_parse_line($matches['text'], $this->options);
        }

        protected function getTodoRegex()
        {
            return $this->todo_regex;
        }

    }
