<?php

    namespace pachno\core\helpers;

    use pachno\core\helpers\EditorJS\EditorJS;
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
        public const BLOCK_PARAGRAPH = 'paragraph';

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

        public function mapBlockToMarkup($block, $index)
        {
            $content = [];
            switch ($block['type']) {
                case 'paragraph':
                    foreach ($block['data'] as $content_data) {
                        $text = nl2br($content_data);
                        $content[] = "<p>{$text}</p>";
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
                case 'checklist':
                    $content[] = '<div class="checklist list-mode" data-index="' . $index . '">';
                    foreach ($block['data']['items'] as $list_index => $list_item) {
                        $checked = ($list_item['checked']) ? 'checked' : '';
                        $content[] = "<input type=\"checkbox\" class=\"fancy-checkbox trigger-toggle-checklist\" id=\"article-checklist-item-{$index}-{$list_index}\" data-index=\"{$list_index}\" {$checked}><label for=\"article-checklist-item-{$index}-{$list_index}\" class=\"list-item\"><span class=\"icon\">" . fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far') . "</span><span>{$list_item['text']}</span></label>";
                    }
                    $content[] = "</div>";
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

                    $content[] = "<pre><code class='block'>{$code}</code></pre>";
                    break;
                case 'codeBlock':
                    $code = $block['data']['text'];
                    $language = $block['data']['language'];

                    $content[] = "<pre class='{$language}'><code class='block'>{$code}</code></pre>";
                    break;
                case 'link':
                    $url = $block['data']['link'];

                    $content[] = "<a href='{$url}' target='_blank'>{$url}</a>";
                    break;
                case 'image':
                    $url = $block['data']['file']['url'];
                    $stretched = ($block['data']['stretched'] == true) ? 'stretched' : '';
                    $withBorder = ($block['data']['withBorder'] == true) ? 'with-border' : '';
                    $withBackground = ($block['data']['withBackground'] == true) ? 'with-background' : '';

                    $content[] = "<div class='image-container {$stretched} {$withBackground} {$withBorder}'><img src='{$url}'></div>";
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
                case 'alert':
                    $type = $block['data']['type'];
                    $message = $block['data']['message'];

                    $content[] = "<div class='message-box cdx-alert-{$type}'>";
                    $content[] = "<span class='message'>{$message}</span>";
                    $content[] = "</div>";
                    break;
                case 'table':
                    $content[] = "<table>";
                    foreach ($block['data']['content'] as $row) {
                        $content[] = "<tr>";
                        foreach ($row as $column) {
                            $content[] = "<td><span>{$column}</span></td>";
                        }
                        $content[] = "</tr>";
                    }
                    $content[] = "</table>";
                    break;
                default:
                    framework\Context::getDebugger()->watch('block', $block);
                    throw new \Exception('Unsupported editorjs block type "' . $block['type'] . '"');
            }

            return implode("\n", $content);
        }

        public function getContent()
        {
            $this->toc = [];
            $blocks = $this->parser->getBlocks();
            $content = array_map([$this, 'mapBlockToMarkup'], $blocks, array_keys($blocks));

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
