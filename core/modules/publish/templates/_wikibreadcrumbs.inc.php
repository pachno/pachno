<?php

    $article_name = (isset($article_name)) ? $article_name : '';
    if (!\pachno\core\framework\Context::isProjectContext() || (\pachno\core\framework\Context::isProjectContext() && mb_strtolower($article_name) != mb_strtolower(\pachno\core\framework\Context::getCurrentProject()->getKey() . ':mainpage')))
    {
        if (\pachno\core\framework\Context::isProjectContext())
        {
            $pachno_response->addBreadcrumb(\pachno\core\framework\Context::getModule('publish')->getMenuTitle(), make_url('publish_article', array('article_name' => \pachno\core\framework\Context::getCurrentProject()->getKey() . ':MainPage')));
        }
        else
        {
            $pachno_response->addBreadcrumb(\pachno\core\framework\Context::getModule('publish')->getMenuTitle(), make_url('publish_article', array('article_name' => 'MainPage')));
        }
        $items = explode(':', $article_name);
        $bcpath = array_shift($items);
        if (mb_strtolower($bcpath) == 'category')
        {
            $pachno_response->addBreadcrumb(__('Categories'));
            if (\pachno\core\framework\Context::isProjectContext())
            {
                $bcpath .= ":".array_shift($items);
            }
        }
        elseif (!\pachno\core\framework\Context::isProjectContext() && mb_strtolower($bcpath) != 'mainpage')
        {
            $pachno_response->addBreadcrumb($bcpath, make_url('publish_article', array('article_name' => $bcpath)));
        }
        foreach ($items as $bc_name)
        {
            $bcpath .= ":".$bc_name;
            $pachno_response->addBreadcrumb($bc_name, make_url('publish_article', array('article_name' => $bcpath)));
        }
    }
    else
    {
        $pachno_response->addBreadcrumb(\pachno\core\framework\Context::getModule('publish')->getMenuTitle(), make_url('publish_article', array('article_name' => \pachno\core\framework\Context::getCurrentProject()->getKey() . ':MainPage')));
    }
