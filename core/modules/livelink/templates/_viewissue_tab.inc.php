                <li id="tab_vcs_checkins"><?php echo javascript_link_tag(image_tag('viewissue_tab_checkins.png', array(), false, 'livelink') . '<span>'.__('Code checkins (%count)', array('%count' => '</span><span id="viewissue_vcs_checkins_count" class="tab_count">'.$count.'</span>')), array('onclick' => "Pachno.Main.Helpers.tabSwitcher('tab_vcs_checkins', 'viewissue_menu');")).'</span>'; ?></li>
