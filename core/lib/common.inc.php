<?php

    /**
     * Common helper functions
     */

    /**
     * Run the I18n translation function
     *
     * @param string $text the text to translate
     * @param array $replacements [optional] replacements
     *
     * @return string
     */
    function __($text, $replacements = array(), $html_decode = false)
    {
        return \pachno\core\framework\Context::getI18n()->__($text, $replacements, $html_decode);
    }

    /**
     * Template escaping translation function
     *
     * @param string $text the text to translate
     * @param array $replacements [optional] replacements
     *
     * @return string
     */
    function __e($text, $replacements = array())
    {
        return \pachno\core\framework\Context::getI18n()->__e($text, $replacements);
    }

    /**
     * Truncate a string, and optionally add padding dots
     *
     * @param string $text
     * @param integer $length
     *
     * @return string The truncated string
     */
    function pachno_truncateText($text, $length = 300)
    {
        if (mb_strlen($text) > $length)
        {
            $string = wordwrap($text, $length - 3, '|||WORDWRAP|||');
            $text = mb_substr($string, 0, mb_strpos($string, "|||WORDWRAP|||")) . '...';
        }
        return $text;
    }

    /**
     * Returns a random number
     *
     * @return integer
     */
    function pachno_get_activation_number()
    {
        $randomNumber = "";

        for($cc = 1; $cc <= 6; $cc++)
        {
            $rndNo = mt_rand(0,9);
            $randomNumber .= $rndNo;
        }

        return $randomNumber;
    }

    function day_delta($tstamp, $tzoffset)
    {
        $mdy = explode(':', date('m:d:Y', time() + $tzoffset));
        $midnight = mktime(0, 0, 0, $mdy[0], $mdy[1], $mdy[2]);
        return floor(($tstamp - $midnight) / 24 / 60 / 60);
    }

    /**
     * Determine if a string valid regarding a specific syntax (email address, DNS name, IP...)
     *
     * @param string $str the string to be checked
     * @param string $format the referal syntax
     * @param boolean $exact_match [option] set if the string must only contain this syntax (default=true)
     * @param boolean $case_sensitive [option] set if the match is case sensitive (default=false)
     *
     * @return boolean
     */
    function pachno_check_syntax($str, $format, $exact_match=true, $case_sensitive = false)
    {
        // based on RFC 2822
        $ip_regex = '\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b';
        $dns_regex = '(' . $ip_regex . '|(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\]))';
        $addr_regex = '(?:[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$&\'*+\/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@' . $dns_regex;
        $serv_regex = '(ssl:\/\/)?(' . $dns_regex . ')';
        // list of supported character sets based on PHP doc : http://www.php.net/manual/en/function.htmlentities.php
        $charset_regex = '((ISO-?8859-1)|(ISO-?8859-15)|(UTF-8)|((cp|ibm)?866)|((cp|Windows-|win-)+1251)|((cp|Windows-)+1252)|(KOI8-?RU?)|(BIG5)|(950)|(GB2312)|(936)|(BIG5-HKSCS)|(S(hift_)?JIS)|(932)|(EUC-?JP))';

        switch ($format)
        {
            case "IP":
                $regex = $ip_regex;
                break;
            case "DNSNAME":
                $regex = $dns_regex;
                break;
            case "EMAIL":
                $regex = $addr_regex;
                break;
            case "MAILSERVER":
                $regex = $serv_regex;
                break;
            case "CHARSET":
                $regex = $charset_regex;
                break;
        }
        return preg_match("/" . ($exact_match ? '^' : '') . $regex . ($exact_match ? '$' : '') . "/" . ($case_sensitive ? '' : 'i'), $str);
    }

    function pachno_get_breadcrumblinks($type, $project = null)
    {
        return \pachno\core\framework\Context::getResponse()->getPredefinedBreadcrumbLinks($type, $project);
    }

    function pachno_get_pagename($page)
    {
        $links = pachno_get_breadcrumblinks('project_summary', \pachno\core\framework\Context::getCurrentProject());
        return (isset($links[$page]) && $page != 'project_issues') ? $links[$page]['title'] : __('Dashboard');
    }

    function pachno_hex_to_rgb($hex)
    {
        $hex = preg_replace("/[^0-9A-Fa-f]/", '', $hex);
        $rgb = array();
        if (strlen($hex) == 6)
        {
            $color = hexdec($hex);
            $rgb['r'] = 0xFF & ($color >> 0x10);
            $rgb['g'] = 0xFF & ($color >> 0x8);
            $rgb['b'] = 0xFF & $color;
        }
        elseif (strlen($hex) == 3)
        {
            $rgb['r'] = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $rgb['g'] = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $rgb['b'] = hexdec(str_repeat(substr($hex, 2, 1), 2));
        }
        else
        {
            return false; //Invalid hex color code
        }
        return $rgb;
    }

    function pachno_get_userstate_image(\pachno\core\entities\User $user)
    {
        switch (true)
        {
            case $user->getState()->isInMeeting():
                return fa_image_tag('circle', array('class' => 'userstate in-meeting', 'title' => __($user->getState()->getName())));
                break;
            case $user->getState()->isBusy():
                return fa_image_tag('minus-circle', array('class' => 'userstate busy', 'title' => __($user->getState()->getName())));
                break;
            case $user->isOffline():
                return fa_image_tag('times-circle', array('class' => 'userstate offline', 'title' => __($user->getState()->getName())));
                break;
            case $user->getState()->isAbsent():
                return fa_image_tag('circle', array('class' => 'userstate absent', 'title' => __($user->getState()->getName())));
                break;
            case $user->getState()->isUnavailable():
                return fa_image_tag('circle', array('class' => 'userstate unavailable', 'title' => __($user->getState()->getName())), 'far');
                break;
            default:
                return fa_image_tag('check-circle', array('class' => 'userstate online', 'title' => __($user->getState()->getName())));
                break;
        }
    }

    /**
     * Returns a boolean value to determine if a url is a youtube link
     *
     * @param string $url URL
     *
     * @return boolean
     */
    function pachno_youtube_link($url) //Ticket #2308
    {
        $is_youtube = false;

        // check to see if video contains the shortened youtube link
        // if so convert it to the long link
        if (preg_match("/(youtu\.be)|(youtube\.com)/", $url))
        {
            $is_youtube = true;
        }

        return $is_youtube;
    }

    /**
     * Returns a properly formatted youtube link
     *
     * @param string $url URL
     *
     * @return string
     */
    function pachno_youtube_prepare_link($url) //Ticket #2308
    {
        // check to see if video contains the shortened youtube link
        // if so convert it to the long link
        if (preg_match("/youtu\.be/", $url))
        {
            $url = preg_replace("/youtu\.be/", "youtube.com/embed", $url);
        }

        // check to see if the http(s): is included
        // if so remove http: or https: from the url link
        if (preg_match("/http(s)?\:/", $url))
        {
            $url = preg_replace("/http(s)?\:/", "", $url);
        }

        // check to see if the // now appears at the front of the link
        // if not add it
        if (!preg_match("/^\/\//", $url))
        {
            $url = "//" . $url;
        }

        // check to see if video contains the watch param
        // if so convert it to an embedded link
        if (preg_match("/watch\?v\=(.*?)/", $url))
        {
            $url = preg_replace("/watch\?v\=/", "embed/", $url);
        }

        return $url;
    }

    /**
     * Get a subset of the items from the given array with default for not found.
     *
     * @param  array  $array
     * @param  array|string  $keys
     * @param  mixed  $default
     *
     * @return array
     */
    function array_only_with_default($array, $keys, $default = null)
    {
        return array_merge(array_fill_keys($keys, $default), array_intersect_key($array, array_flip((array) $keys)));
    }
