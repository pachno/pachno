<?php

    if (!$project->hasWikiURL()) {
        echo link_tag(make_url('publish_article', array('article_name' => ucfirst($project->getKey()).':MainPage')), fa_image_tag('book').'<span>'.__('Wiki').'</span>', array('class' => 'button secondary'));
    } else {
        echo link_tag($project->getWikiURL(), fa_image_tag('book') . '<span>'.__('Wiki').'</span>', array('target' => 'blank', 'class' => 'button secondary'));
    }
