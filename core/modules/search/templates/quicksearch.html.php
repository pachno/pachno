<ul>
    <?php if (!$search_object->hasQuickfoundIssues()): ?>
        <li class="searchterm"><?php echo $searchterm; ?><br><span class="informal"><?php echo __('Press "Enter" twice to find issues matching your query'); ?></span></li>
    <?php endif; ?>
    <?php \pachno\core\framework\Event::createNew('core', 'quicksearch_dropdown_firstitems', $searchterm)->trigger(); ?>
    <?php if ($num_projects > 0): ?>
        <li class="header disabled"><?php echo __('%num project(s) found', array('%num' => $num_projects)); ?></li>
        <?php $cc = 0; ?>
        <?php foreach ($found_projects as $project): ?>
            <?php $cc++; ?>
            <?php if ($project instanceof \pachno\core\entities\Project): ?>
                <li class="<?php if ($cc == count($found_projects) && $num_projects == count($found_projects)): ?> last<?php endif; ?>">
                    <div class="link_container"><?php echo image_tag($project->getIconName(), array('alt' => ' ')); ?><?php echo $project->getName(); ?></div>
                    <span class="hidden url"><?php echo make_url('project_dashboard', array('project_key' => $project->getKey())); ?></span>
                </li>
            <?php endif; ?>
            <?php if ($cc == 10) break; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <li class="header disabled"><?php echo __('%num issue(s) found', array('%num' => $search_object->getTotalNumberOfIssues())); ?></li>
    <?php $cc = 0; ?>
    <?php if ($search_object->getTotalNumberOfIssues() > 0): ?>
        <?php foreach ($search_object->getIssues() as $issue): ?>
            <?php if ($issue instanceof \pachno\core\entities\Issue): ?>
                <li class="issue_<?php echo ($issue->isOpen()) ? 'open' : 'closed'; ?>"><div class="link_container"><?php echo image_tag($issue->getIssueType()->getFontAwesomeIcon(), ['class' => 'informal']); ?><a href="<?php echo $issue->getUrl(); ?>"><?php echo __('Issue %issue_no - %title', array('%issue_no' => $issue->getFormattedIssueNo(true), '%title' => $issue->getTitle())); ?></a></div><span class="informal"><?php if ($issue->isClosed()): ?>[<?php echo mb_strtoupper(__('Closed')); ?>] <?php endif; ?><?php echo __('Last updated %updated_at', array('%updated_at' => \pachno\core\framework\Context::getI18n()->formatTime($issue->getLastUpdatedTime(), 6))); ?></span><span class="informal url"><?php echo $issue->getUrl(); ?></span><div class="informal extra"><?php echo __('Status: %status', array('%status' => '<span>'.$issue->getStatus()->getName().'</span>')); ?></div><?php if ($issue->isResolutionVisible()): ?><div class="informal extra"><?php echo __('Resolution: %resolution', array('%resolution' => '<span>'.(($issue->getResolution() instanceof \pachno\core\entities\Resolution) ? $issue->getResolution()->getName() : '<span class="faded_out">'.__('Not determined').'</span>').'</span>')); ?></div><?php endif; ?><div class="informal extra attached"><?php echo fa_image_tag('comment'); ?><span class="num_attachments"><?php echo $issue->countComments(); ?></span><?php echo fa_image_tag('paperclip'); ?><span class="num_attachments"><?php echo $issue->getNumberOfFiles(); ?></span></div></li>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if (!$search_object->hasQuickfoundIssues() && $search_object->getTotalNumberOfIssues() > $search_object->getNumberOfIssues()): ?>
            <li class="find_more_issues last">
                <span class="informal"><?php echo __('See %num more issues ...', array('%num' => $search_object->getTotalNumberOfIssues() - $search_object->getNumberOfIssues())); ?></span>
                <div class="hidden url"><?php echo (\pachno\core\framework\Context::isProjectContext()) ? make_url('project_issues', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey())) : make_url('search'); ?>?fs[text][v]=<?php echo $searchterm; ?>&fs[text][o]=<?php echo urlencode('='); ?></div>
            </li>
        <?php endif; ?>
    <?php else: ?>
        <li class="disabled no_issues_found">
            <?php echo __('No issues found matching your query'); ?>
        </li>
    <?php endif; ?>
    <?php if ($pachno_user->canAccessConfigurationPage()): ?>
        <?php if ($num_users > 0): ?>
            <li class="header disabled"><?php echo __('%num user(s) found', array('%num' => $num_users)); ?></li>
            <?php $cc = 0; ?>
            <?php foreach ($found_users as $user): ?>
                <?php $cc++; ?>
                <?php if ($user instanceof \pachno\core\entities\User): ?>
                    <li class="quicksearch_user_item <?php if ($cc == count($found_users) && $num_users == count($found_users)): ?> last<?php endif; ?>">
                        <?php echo pachno_get_userstate_image($user) . image_tag($user->getAvatarURL(), array('alt' => ' ', 'class' => 'avatar', 'style' => "width: 12px; height: 12px;"), true); ?>
                        <?php echo $user->getNameWithUsername(); ?>
                        <span class="hidden backdrop"><span class="backdrop_url"><?php echo make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $user->getID())); ?></span></span>
                    </li>
                <?php endif; ?>
                <?php if ($cc == 10) break; ?>
            <?php endforeach; ?>
            <?php if ($num_users - $cc > 0): ?>
                <li class="find_more_issues last">
                    <span class="informal"><?php echo __('See %num more users ...', array('%num' => $num_users - $cc)); ?></span>
                    <div class="hidden url"><?php echo make_url('configure_users').'?finduser='.$searchterm; ?></div>
                </li>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($num_teams > 0): ?>
            <li class="header disabled"><?php echo __('%num team(s) found', array('%num' => $num_teams)); ?></li>
            <?php $cc = 0; ?>
            <?php foreach ($found_teams as $team): ?>
                <?php $cc++; ?>
                <?php if ($team instanceof \pachno\core\entities\Team): ?>
                    <li class="<?php if ($cc == count($found_teams) && $num_teams == count($found_teams)): ?> last<?php endif; ?>">
                        <?php echo image_tag('icon_team.png', array('alt' => ' ', 'style' => "width: 12px; height: 12px; float: left; margin-right: 5px;")); ?>
                        <?php echo $team->getName(); ?>
                        <span class="hidden url"><?php echo make_url('team_dashboard', array('team_id' => $team->getID())); ?></span>
                    </li>
                <?php endif; ?>
                <?php if ($cc == 10) break; ?>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if ($num_clients > 0): ?>
            <li class="header disabled"><?php echo __('%num client(s) found', array('%num' => $num_clients)); ?></li>
            <?php $cc = 0; ?>
            <?php foreach ($found_clients as $client): ?>
                <?php $cc++; ?>
                <?php if ($client instanceof \pachno\core\entities\Client): ?>
                    <li class="<?php if ($cc == count($found_clients) && $num_clients == count($found_clients)): ?> last<?php endif; ?>">
                        <?php echo image_tag('icon_client.png', array('alt' => ' ', 'style' => "width: 12px; height: 12px; float: left; margin-right: 5px;")); ?>
                        <?php echo $client->getName(); ?>
                        <span class="hidden url"><?php echo make_url('client_dashboard', array('client_id' => $client->getID())); ?></span>
                    </li>
                <?php endif; ?>
                <?php if ($cc == 10) break; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
    <?php \pachno\core\framework\Event::createNew('core', 'quicksearch_dropdown_founditems', $searchterm)->trigger(); ?>
</ul>
