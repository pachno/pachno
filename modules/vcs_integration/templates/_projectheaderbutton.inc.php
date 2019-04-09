<?php
    /*
     * Generate link for browser
     */
     
    $link_repo = \pachno\core\framework\Context::getModule('vcs_integration')->getSetting('browser_url_' . \pachno\core\framework\Context::getCurrentProject()->getID());
    
    if (\pachno\core\framework\Context::getModule('vcs_integration')->getSetting('vcs_mode_' . \pachno\core\framework\Context::getCurrentProject()->getID()) != \pachno\modules\vcs_integration\Vcs_integration::MODE_DISABLED)
    {
        echo '<a href="'.$link_repo.'" target="_blank" class="button button-blue">'.image_tag('cfg_icon_vcs_integration.png', array(), false, 'vcs_integration').__('Source code').'</a>';
    }

?>
