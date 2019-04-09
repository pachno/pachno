<?php
                    if (\pachno\core\framework\Settings::hasMaintenanceMessage())
                    {
                        echo '<div class="offline_msg">'.\pachno\core\helpers\TextParser::parseText(\pachno\core\framework\Settings::getMaintenanceMessage()).'</div>';
                    }
                    else
                    {
                        ?>
                        <div class="offline_msg">
                            <div class="generic_offline rounded_box red borderless">
                                <?php echo __('This site has been temporarily disabled for maintenance. Please try again later.'); ?>
                            </div>
                        </div>
                        <?php
                    }
