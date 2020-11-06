<?php

    namespace pachno\core\helpers;

    use EditorJS\EditorJS;
    use pachno\core\entities\User;
    use pachno\core\framework;

    /**
     * Text parser class, markdown syntax
     *
     * @package pachno
     * @subpackage main
     */
    class TextParserEditorJS implements ContentParser
    {
        const BLOCK_PARAGRAPH = 'paragraph';

        /**
         * An array of mentioned users
         *
         * @var array|User
         */
        protected $mentions = [];

        protected $options = [];

        protected $toc = [];

        /**
         * @var EditorJS
         */
        protected $parser;

        public function __construct($content, $options)
        {
            $configuration_file = PACHNO_CONFIGURATION_PATH . 'editorjs.config.json';
            if (empty($content)) {
                $fixtures_path = PACHNO_CORE_PATH . 'modules' . DS . 'publish' . DS . 'fixtures' . DS;
                $content = file_get_contents($fixtures_path . 'empty.json');
            }
            $parser = new EditorJS($content, file_get_contents($configuration_file));
            $this->parser = $parser;
        }

        public function mapBlockToMarkup($block)
        {
            $content = [];
            switch ($block['type']) {
                case 'paragraph':
                    foreach ($block['data'] as $content_data) {
                        $content[] = "<p>{$content_data}</p>";
                    }
                    break;
                case 'list':
                    $tag = ($block['data']['style'] == 'ordered') ? 'ol' : 'ul';

                    $content[] = "<{$tag}>";
                    foreach ($block['data']['items'] as $list_item) {
                        $content[] = "<li>{$list_item}</li>";
                    }
                    $content[] = "</{$tag}>";
                    break;
                case 'header':
                    $level = $block['data']['level'];
                    $text = $block['data']['text'];
                    $toc_id = 'article_toc_' . (count($this->toc) + 1);

                    if ($level <= 2) {
                        $this->toc[] = ['level' => 1, 'content' => $text, 'id' => $toc_id];
                    }
                    $content[] = "<h{$level} id='{$toc_id}' name='{$toc_id}'>{$text}</h{$level}>";
                    break;
                case 'quote':
                    $content[] = "<blockquote class='block align-{$block['data']['alignment']}'><span class='quote'>{$block['data']['text']}</span><span class='author'>{$block['data']['caption']}</span></blockquote>";
                    break;
                case 'code':
                    $code = $block['data']['code'];

                    $content[] = "<code class='block'>{$code}</code>";
                    break;
                case 'link':
                    $url = $block['data']['link'];

                    $content[] = "<a href='{$url}' target='_blank'>{$url}</a>";
                    break;
                case 'delimiter':
                    $content[] = "<div class='separator'></div>";
                    break;
                case 'warning':
                    $title = $block['data']['title'];
                    $message = $block['data']['message'];

                    $content[] = "<div class='message-box type-warning'>";
                    $content[] = "<span class='message'><span class='title'>" . fa_image_tag('info-circle', ['class' => 'icon']) . "<span>{$title}</span></span>";
                    $content[] = "<span>{$message}</span>";
                    $content[] = "</div>";
                    break;
                default:
                    framework\Context::getDebugger()->watch('block', $block);
                    throw new \Exception('Invalid editorjs content type');
            }

            return implode("\n", $content);
        }

        public function getContent()
        {
            $this->toc = [];
            $content = array_map([$this, 'mapBlockToMarkup'], $this->parser->getBlocks());

            return implode("\n", $content);
        }

        public function getMentions()
        {
            // TODO: Implement getMentions() method.
        }

        public function hasMentions()
        {
            // TODO: Implement hasMentions() method.
        }

        public function getTableOfContents()
        {
            return $this->toc;
        }
    }
