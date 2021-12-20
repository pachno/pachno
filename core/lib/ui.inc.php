<?php

    use pachno\core\framework\Action,
        pachno\core\framework\ActionComponent,
        pachno\core\framework\Settings;

    /**
     * UI functions
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 2.0
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package pachno
     */

    /**
     * Returns an <img> tag with a specified image
     *
     * @param string $image image source
     * @param array $params [optional] html parameters
     * @param boolean $notheme [optional] whether this is a themed image or a top level path
     * @param string $module whether this is a module image or in the core image set
     * @param boolean $relative whether the path is relative or absolute
     *
     * @return string
     */
    function image_tag($image, $params = array(), $notheme = false, $module = 'core', $relative = true)
    {
        if ( ! \pachno\core\framework\Context::getRequest()->isAjaxCall()) {
            $params['src'] = image_url(\pachno\core\framework\Context::getWebroot() . 'images/0.png', true);
            $params['data-src'] = image_url($image, $notheme, $module, $relative);
        }
        else {
            $params['src'] = image_url($image, $notheme, $module, $relative);
        }
        if (!isset($params['alt']))
            $params['alt'] = $image;

        return "<img " . parseHTMLoptions($params) . '>';
    }

    function fa_image_tag($image, $params = [], $mode = 'fas')
    {
        if (!isset($params['class']))
            $params['class'] = [];
        elseif (!is_array($params['class']))
            $params['class'] = [$params['class']];

        $params['class'][] = $mode;
        $params['class'][] = 'fa-'.$image;

        return '<i ' . parseHTMLoptions($params) . '></i>';
    }

    /**
     * UI function to build an image icon with hover tooltip text.
     * Used in the config module initially.
     *
     * @param $tooltipText
     * @param string $image
     * @return string
     */
    function config_explanation($tooltipText, $image = 'question-circle', $image_style = 'far')
    {
        return sprintf('<span class="config-explanation tooltip-container">
                %s
                <span class="tooltip from-above rightie">%s</span>
            </span>',
            fa_image_tag($image, ['style' => 'cursor: pointer;'], $image_style),
            $tooltipText
        );
    }

    /**
     * Returns the URL to a specified image
     *
     * @param string $image image source
     * @param bool $notheme [optional] whether this is a themed image or a top level path
     *
     * @return string
     */
    function image_url($image, $notheme = false, $module = 'core', $relative = true)
    {
        $image_src = '';
        if ($notheme)
        {
            $image_src = $image;
        }
        else
        {
            $image_src = \pachno\core\framework\Context::getWebroot() . "images/";

            if ($module != 'core')
                $image_src .= "modules/{$module}/";

            $image_src .= $image;
        }
        return ($relative) ? $image_src : \pachno\core\framework\Context::getUrlHost() . $image_src;
    }

    /**
     * Returns an <a> tag linking to a specified url
     *
     * @param string $url link target
     * @param string $link_text the text displayed in the tag
     * @param array $params [optional] html parameters
     *
     * @return string
     */
    function link_tag($url, $link_text = null, $params = array())
    {
        $params['href'] = $url;
        if ($link_text === null) $link_text = $url;
        return "<a " . parseHTMLoptions($params) . ">{$link_text}</a>";
    }

    /**
     * Returns an <iframe> tag linking to a specified url
     *
     * @param string $url link target
     * @param string $width width of the frame
     * @param string $height height of the frame
     *
     * @return string
     */
    function iframe_tag($url, $width = 500, $height = 400) //Ticket #2308
    {
        if ($url == null) return;

        return '<iframe width="'.$width.'" height="'.$height.'" src="'.$url.'" frameborder="0" allowfullscreen></iframe>';
    }


    /**
     * Returns an <object> tag linking to a specified url
     *
     * @param string $url link target
     * @param string $width width of the frame
     * @param string $height height of the frame
     *
     * @return string
     */
    function object_tag($url, $width = 500, $height = 400) //Ticket #2308
    {
        if ($url == null) return;

        return '<object width="'.$width.'" height="'.$height.'">
                <param name="movie" value="'.$url.'?hl=en_US&amp;version=3"></param>
                <param name="allowFullScreen" value="true"></param>
                <param name="allowscriptaccess" value="always"></param>
                <embed src="'.$url.'?hl=en_US&amp;version=3" type="application/x-shockwave-flash" width="'.$width.'" height="'.$height.'" allowscriptaccess="always" allowfullscreen="true"></embed>
                </object>';
    }

    /**
     * Returns a csrf_token hidden input tag to use in forms
     *
     * @return string
     */
    function csrf_tag()
    {
        return '<input type="hidden" name="csrf_token" value="' . \pachno\core\framework\Context::getCsrfToken() . '">';
    }

    /**
     * Return a javascript link tag
     *
     * @see link_tag()
     *
     * @param string $link_text the text displayed in the tag
     * @param array $params [optional] html parameters
     *
     * @return string
     */
    function javascript_link_tag($link_text, $params = array())
    {
        return link_tag('javascript:void(0);', $link_text, $params);
    }

    /**
     * Returns an <input type="image"> tag
     *
     * @param string $image image source
     * @param array $params [optional] html parameters
     * @param bool $notheme [optional] whether this is a themed image or a top level path
     *
     * @return string
     */
    function image_submit_tag($image, $params = array(), $notheme = false)
    {
        $params['src'] = (!$notheme) ? \pachno\core\framework\Context::getWebroot() . 'images/' . $image : $image;
        return '<input type="image" ' . parseHTMLoptions($params) . ' />';
    }

    /**
     * Includes a component with specified parameters
     *
     * @param string    $component    name of component to load, or module/component to load
     * @param array     $params      key => value pairs of parameters for the template
     */
    function include_component($component, $params = array())
    {
        return ActionComponent::includeComponent($component, $params);
    }

    /**
     * Return a rendered component with specified parameters
     *
     * @param string    $component    name of component to load, or module/component to load
     * @param array     $params      key => value pairs of parameters for the template
     */
    function get_component_html($component, $params = [])
    {
        return Action::returnComponentHTML($component, $params);
    }

    /**
     * Generate a url based on a route
     *
     * @param string    $name     The route key
     * @param array     $params    key => value pairs of route parameters
     * @param bool        $relative [optional] Whether to generate a full url or relative
     *
     * @return string
     */
    function make_url($name, $params = [], $relative = true)
    {
        return \pachno\core\framework\Context::getRouting()->generate($name, $params, $relative);
    }

    /**
     * Returns a string with html options based on an array
     *
     * @param array    $options an array of options
     *
     * @return string
     */
    function parseHTMLoptions($options)
    {
        $option_strings = [];
        if (!is_array($options))
        {
            throw new \Exception('Invalid HTML options. Must be an array with key => value pairs corresponding to html attributes');
        }
        foreach ($options as $key => $val)
        {
            if (is_array($val)) $val = join(' ', $val);
            $option_strings[$key] = "{$key}=\"{$val}\"";
        }
        return implode(' ', array_values($option_strings));
    }
