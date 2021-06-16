<?php

/**
 * @var string $field
 * @var mixed[] $info
 * @var \pachno\core\entities\Issue $issue
 */

use pachno\core\entities\DatatypeBase;
use pachno\core\helpers\TextParser;

?>
<li id="<?= $field; ?>_field" class="issue-field <?php if (!$info['visible']): ?> hidden<?php endif; ?>">
    <div id="<?php echo $field; ?>_content" class="<?php if (isset($info['extra_classes'])) echo $info['extra_classes']; ?> value fancy-dropdown-container">
        <div class="fancy-dropdown" data-default-label="<?= __('Not determined'); ?>">
            <label><?php echo $info['title']; ?></label>
            <span class="value" data-dynamic-field-value data-field="<?= $field; ?>" data-issue-id="<?= $issue->getId(); ?>">
                <?php
                switch ($info['type'])
                {
                    case DatatypeBase::INPUT_TEXTAREA_SMALL:
                        var_dump($info);
                        break;
                        ?>
                        <span id="<?= $field; ?>_name"<?php if (!$info['value']): ?> style="display: none;"<?php endif; ?>>
                                                    <?= TextParser::parseText($info['name'], false, null, array('headers' => false)); ?>
                                                </span>
                        <span class="no-value" id="no_<?= $field; ?>"<?php if ($info['value']): ?> style="display: none;"<?php endif; ?>>
                                                    <?= __('Not determined'); ?>
                                                </span>
                        <?php
                        break;
                    case DatatypeBase::USER_CHOICE:
                        ?>
                        <span id="<?= $field; ?>_name"<?php if (!$info['value']): ?> style="display: none;"<?php endif; ?>>
                                                    <?= include_component('main/userdropdown', array('user' => $info['value'])); ?>
                                                </span>
                        <span class="no-value" id="no_<?= $field; ?>"<?php if ($info['value']): ?> style="display: none;"<?php endif; ?>>
                                                    <?= __('Not determined'); ?>
                                                </span>
                        <?php
                        break;
                    case DatatypeBase::TEAM_CHOICE:
                        ?>
                        <span id="<?= $field; ?>_name"<?php if (!$info['value']): ?> style="display: none;"<?php endif; ?>>
                                                    <?= include_component('main/teamdropdown', array('team' => $info['identifiable'])); ?>
                                                </span>
                        <span class="no-value" id="no_<?= $field; ?>"<?php if ($info['value']): ?> style="display: none;"<?php endif; ?>>
                                                    <?= __('Not determined'); ?>
                                                </span>
                        <?php
                        break;
                    case DatatypeBase::EDITIONS_CHOICE:
                    case DatatypeBase::COMPONENTS_CHOICE:
                    case DatatypeBase::RELEASES_CHOICE:
                    case DatatypeBase::MILESTONE_CHOICE:
                    case DatatypeBase::CLIENT_CHOICE:
                        ?>
                        <span id="<?= $field; ?>_name"<?php if (!$info['value']): ?> style="display: none;"<?php endif; ?>>
                                                    <?= (isset($info['name'])) ? $info['name'] : __('Unknown'); ?>
                                                </span>
                    <span class="no-value" id="no_<?= $field; ?>"<?php if ($info['value']): ?> style="display: none;"<?php endif; ?>>
                        <?= __('Not determined'); ?>
                        </span><?php
                        break;
                    case DatatypeBase::STATUS_CHOICE:
                        $status = null;
                        $value = null;
                        $color = '#FFF';
                        try
                        {
                            $status = new Status($info['name']);
                            $value = $status->getName();
                            $color = $status->getColor();
                        }
                        catch (\Exception $e) { }
                        ?><span id="<?= $field; ?>_name"<?php if (!$info['value']): ?> style="display: none;"<?php endif; ?>><div class="status-badge" style="background-color: <?= $color; ?>;"><span><?= __($value); ?></span></div></span><span class="no-value" id="no_<?= $field; ?>"<?php if ($info['value']): ?> style="display: none;"<?php endif; ?>><?= __('Not determined'); ?></span><?php
                        break;
                    case DatatypeBase::DATE_PICKER:
                    case DatatypeBase::DATETIME_PICKER:
                        $pachno_response->addJavascript('calendarview');
                        if (!isset($info['name'])) {
                            $value = __('Not set');
                        } elseif (is_numeric($info['name'])) {
                            $value = ($info['name']) ? date('Y-m-d' . ($info['type'] == DatatypeBase::DATETIME_PICKER ? ' H:i' : ''), $info['name']) : __('Not set');
                        } else {
                            $value = $info['name'];
                        }
                        ?><span id="<?= $field; ?>_name"<?php if (!$info['value']): ?> style="display: none;"<?php endif; ?>><?= $value; ?></span><span id="<?= $field; ?>_new_name" style="display: none;"><?= (int) $value; ?></span><span class="no-value" id="no_<?= $field; ?>"<?php if ($info['value']): ?> style="display: none;"<?php endif; ?>><?= __('Not set'); ?></span><?php
                        break;
                    default:
                        if (!isset($info['name'])) {
                            var_dump($info);
                        } else {
                            ?><span id="<?= $field; ?>_name"<?php if (!$info['value']): ?> style="display: none;"<?php endif; ?>><?= (filter_var($info['name'], FILTER_VALIDATE_URL) !== false) ? link_tag($info['name'], $info['name']) : $info['name']; ?></span><span class="no-value" id="no_<?= $field; ?>"<?php if ($info['value']): ?> style="display: none;"<?php endif; ?>><?= __('Not determined'); ?></span><?php
                            break;
                        }
                }
                ?>
            </span>
            <?php if ($issue->isUpdateable() && $issue->canEditCustomFields($field) && $info['editable']): ?>
                <?php echo fa_image_tag('angle-down', ['class' => 'expander']); ?>
                <div class="dropdown-container">
                    <?php if ($info['type'] == DatatypeBase::USER_CHOICE): ?>
                        <?php include_component('main/identifiableselector', array(
                            'html_id'             => $field.'_change',
                            'header'             => __('Select a user'),
                            'callback'             => "Pachno.Issues.Field.set('" . make_url('edit_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, 'identifiable_type' => 'user', $field.'_value' => '%identifiable_value')) . "', '".$field."');",
                            'clear_link_text'    => __('Clear currently selected user'),
                            'base_id'            => $field,
                            'include_teams'        => false,
                            'absolute'            => true)); ?>
                    <?php elseif ($info['type'] == DatatypeBase::TEAM_CHOICE): ?>
                        <?php include_component('main/identifiableselector', array(
                            'html_id'             => $field.'_change',
                            'header'             => __('Select a team'),
                            'callback'             => "Pachno.Issues.Field.set('" . make_url('edit_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, 'identifiable_type' => 'team', $field.'_value' => '%identifiable_value')) . "', '".$field."');",
                            'clear_link_text'    => __('Clear currently selected team'),
                            'base_id'            => $field,
                            'include_teams'        => true,
                            'include_users'        => false,
                            'absolute'            => true)); ?>
                    <?php elseif ($info['type'] == DatatypeBase::CLIENT_CHOICE): ?>
                        <?php include_component('main/identifiableselector', array(
                            'html_id'             => $field.'_change',
                            'header'             => __('Select a client'),
                            'callback'             => "Pachno.Issues.Field.set('" . make_url('edit_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, 'identifiable_type' => 'client', $field.'_value' => '%identifiable_value')) . "', '".$field."');",
                            'clear_link_text'    => __('Clear currently selected client'),
                            'base_id'            => $field,
                            'include_clients'    => true,
                            'include_teams'        => false,
                            'include_users'        => false,
                            'absolute'            => true)); ?>
                    <?php else: ?>
                        <div class="list-mode" id="<?= $field; ?>_change">
                            <div class="header"><?= $info['change_header']; ?></div>
                            <?php if (array_key_exists('choices', $info) && is_array($info['choices'])): ?>
                                <a href="javascript:void(0);" class="list-item" onclick="Pachno.Issues.Field.set('<?= make_url('edit_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => "")); ?>', '<?= $field; ?>');">
                                    <span class="name"><?= $info['clear']; ?></span>
                                </a>
                                <div class="list-item separator"></div>
                                <?php foreach ($info['choices'] ?: array() as $choice): ?>
                                <a href="javascript:void(0);" class="list-item" onclick="Pachno.Issues.Field.set('<?= make_url('edit_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?= $field; ?>');">
                                    <span class="icon"><?= fa_image_tag('list'); ?></span>
                                    <span class="name"><?= __($choice->getName()); ?></span>
                                </a>
                            <?php endforeach; ?>
                            <?php elseif ($info['type'] == DatatypeBase::DATE_PICKER || $info['type'] == DatatypeBase::DATETIME_PICKER): ?>
                                <a href="javascript:void(0);" class="list-item" onclick="Pachno.Issues.Field.set('<?= make_url('edit_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => "")); ?>', '<?= $field; ?>');">
                                    <span class="name"><?= $info['clear']; ?></span>
                                </a>
                                <div class="list-item separator"></div>
                                <div class="list-item" id="customfield_<?= $field; ?>_calendar_container" style="padding: 0;"></div>
                            <?php if ($info['type'] == DatatypeBase::DATETIME_PICKER): ?>
                                <form id="customfield_<?= $field; ?>_form" method="post" class="list-item" accept-charset="<?= Context::getI18n()->getCharset(); ?>" action="" onsubmit="Pachno.Issues.Field.set('<?= make_url('edit_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)); ?>', '<?= $field; ?>', 'customfield_<?= $field; ?>');return false;">
                                    <div class="header"><?= __('Time'); ?></div>
                                    <input type="text" id="customfield_<?= $field; ?>_hour" value="00" style="width: 20px; font-size: 0.9em; text-align: center;">&nbsp;:&nbsp;
                                    <input type="text" id="customfield_<?= $field; ?>_minute" value="00" style="width: 20px; font-size: 0.9em; text-align: center;">
                                    <input type="hidden" name="<?= $field; ?>_value" value="<?= (int) $info['name'] - I18n::getTimezoneOffset(); ?>" id="<?= $field; ?>_value" />
                                    <input type="submit" class="button secondary" value="<?= __('Update'); ?>">
                                </form>
                            <?php endif; ?>
                                <script type="text/javascript">
                                    //require(['domReady', 'pachno/index', 'calendarview'], function (domReady, pachno_index_js, Calendar) {
                                    //    domReady(function () {
                                    //        Calendar.setup({
                                    //            dateField: '<?//= $field; ?>//_new_name',
                                    //            parentElement: 'customfield_<?//= $field; ?>//_calendar_container',
                                    //            valueCallback: function(element, date) {
                                    //                <?php //if ($info['type'] == CustomDatatype::DATETIME_PICKER): ?>
                                    //                var value = date.setUTCHours(parseInt($('#customfield_<?//= $field; ?>//_hour').value));
                                    //                var date  = new Date(value);
                                    //                var value = Math.floor(date.setUTCMinutes(parseInt($('#customfield_<?//= $field; ?>//_minute').value)) / 1000);
                                    //                <?php //else: ?>
                                    //                var value = Math.floor(date.getTime() / 1000);
                                    //                Pachno.Issues.Field.set('<?//= make_url('edit_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)); ?>//?<?//= $field; ?>//_value='+value, '<?//= $field; ?>//');
                                    //                <?php //endif; ?>
                                    //                $('#<?//= $field; ?>//_value').value = value;
                                    //            }
                                    //        });
                                    //        <?php //if ($info['type'] == CustomDatatype::DATETIME_PICKER): ?>
                                    //        var date = new Date(parseInt($('#<?//= $field; ?>//_value').value) * 1000);
                                    //        $('#customfield_<?//= $field; ?>//_hour').value = date.getUTCHours();
                                    //        $('#customfield_<?//= $field; ?>//_minute').value = date.getUTCMinutes();
                                    //        Event.observe($('#customfield_<?//= $field; ?>//_hour'), 'change', function (event) {
                                    //            var value = parseInt($('#<?//= $field; ?>//_value').value);
                                    //            var hours = parseInt(this.value);
                                    //            if (value <= 0 || hours < 0 || hours > 24) return;
                                    //            var date = new Date(value * 1000);
                                    //            $('#<?//= $field; ?>//_value').value = date.setUTCHours(parseInt(this.value)) / 1000;
                                    //        });
                                    //        Event.observe($('#customfield_<?//= $field; ?>//_minute'), 'change', function (event) {
                                    //            var value = parseInt($('#<?//= $field; ?>//_value').value);
                                    //            var minutes = parseInt(this.value);
                                    //            if (value <= 0 || minutes < 0 || minutes > 60) return;
                                    //            var date = new Date(value * 1000);
                                    //            $('#<?//= $field; ?>//_value').value = date.setUTCMinutes(parseInt(this.value)) / 1000;
                                    //        });
                                    //        <?php //endif; ?>
                                    //    });
                                    //});
                                </script>
                            <?php else: ?>
                                <a href="javascript:void(0);" class="list-item" onclick="Pachno.Issues.Field.set('<?= make_url('edit_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => "")); ?>', '<?= $field; ?>');">
                                    <span class="name"><?= $info['clear']; ?></span>
                                </a>
                                <div class="list-item separator"></div>
                                <?php

                            switch ($info['type'])
                            {
                            case DatatypeBase::EDITIONS_CHOICE:
                                ?>
                                <?php foreach ($issue->getProject()->getEditions() as $choice): ?>
                                <a href="javascript:void(0);" class="list-item" onclick="Pachno.Issues.Field.set('<?= make_url('edit_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?= $field; ?>');">
                                    <span class="icon"><?= fa_image_tag('window-restore'); ?></span>
                                    <span class="name"><?= __($choice->getName()); ?></span>
                                </a>
                            <?php endforeach; ?>
                                <?php
                                break;
                            case DatatypeBase::MILESTONE_CHOICE:
                                ?>
                                <?php foreach ($issue->getProject()->getMilestonesForIssues() as $choice): ?>
                                <a href="javascript:void(0);" class="list-item" onclick="Pachno.Issues.Field.set('<?= make_url('edit_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?= $field; ?>');">
                                    <span class="icon"><?= fa_image_tag('chart-line'); ?></span>
                                    <span class="name"><?= __($choice->getName()); ?></span>
                                </a>
                            <?php endforeach; ?>
                                <?php
                                break;
                            case DatatypeBase::STATUS_CHOICE:
                                ?>
                                <?php foreach (Status::getAll() as $choice): ?>
                                <a href="javascript:void(0);" class="list-item" onclick="Pachno.Issues.Field.set('<?= make_url('edit_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?= $field; ?>');">
                                                <span class="status-badge" style="background-color: <?= ($choice instanceof Status) ? $choice->getColor() : '#FFF'; ?>;">
                                                    <span id="status_content">&nbsp;&nbsp;</span>
                                                </span>
                                    <?= __($choice->getName()); ?>
                                </a>
                            <?php endforeach; ?>
                                <?php
                                break;
                            case DatatypeBase::COMPONENTS_CHOICE:
                                ?>
                                <?php foreach ($issue->getProject()->getComponents() as $choice): ?>
                                <a href="javascript:void(0);" class="list-item" onclick="Pachno.Issues.Field.set('<?= make_url('edit_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?= $field; ?>');">
                                    <span class="icon"><?= fa_image_tag('cube'); ?></span>
                                    <span class="name"><?= __($choice->getName()); ?></span>
                                </a>
                            <?php endforeach; ?>
                                <?php
                                break;
                            case DatatypeBase::RELEASES_CHOICE:
                                ?>
                                <?php foreach ($issue->getProject()->getBuilds() as $choice): ?>
                                <a href="javascript:void(0);" class="list-item" onclick="Pachno.Issues.Field.set('<?= make_url('edit_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?= $field; ?>');">
                                    <span class="icon"><?= fa_image_tag('compact-dist'); ?></span>
                                    <span class="name"><?= __($choice->getName()); ?></span>
                                </a>
                            <?php endforeach; ?>
                                <?php
                                break;
                                case DatatypeBase::INPUT_TEXT:
                                    var_dump($field);
                                    var_dump($info);
                                    /*?>
                                    <div class="list-item">
                                        <form id="<?= $field; ?>_form" action="<?= make_url('edit_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)); ?>" method="post" onSubmit="Pachno.Issues.Field.set('<?= make_url('edit_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)) ?>', '<?= $field; ?>', '<?= $field; ?>'); return false;">
                                            <input type="text" name="<?= $field; ?>_value" value="<?= $info['name'] ?>" /><?= __('%save or %cancel', array('%save' => '<input type="submit" value="'.__('Save').'">', '%cancel' => '<a href="#" onclick="$(\''.$field.'_change\').hide(); return false;">'.__('cancel').'</a>')); ?>
                                        </form>
                                    </div>
                                    <?php*/
                                    break;
                                case DatatypeBase::INPUT_TEXTAREA_SMALL:
                                    var_dump($field);
                                    var_dump($info);
                                    /*?>
                                    <div class="list-item">
                                        <form id="<?= $field; ?>_form" action="<?= make_url('edit_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)); ?>" method="post" onSubmit="Pachno.Issues.Field.set('<?= make_url('edit_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)) ?>', '<?= $field; ?>', '<?= $field; ?>'); return false;">
                                            <?php include_component('main/textarea', array('area_name' => $field.'_value', 'target_type' => 'issue', 'target_id' => $issue->getID(), 'area_id' => $field.'_value', 'height' => '100px', 'width' => '100%', 'value' => $info['name'])); ?>
                                            <br><?= __('%save or %cancel', array('%save' => '<input type="submit" value="'.__('Save').'">', '%cancel' => '<a href="#" onclick="$(\''.$field.'_change\').hide(); return false;">'.__('cancel').'</a>')); ?>
                                        </form>
                                    </div>
                                    <?php*/
                                    break;
                            }

                            endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</li>
