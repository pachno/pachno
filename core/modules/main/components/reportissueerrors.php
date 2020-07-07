<?php

    use \pachno\core\entities;
    use pachno\core\entities\Article;
    use pachno\core\entities\Build;
    use pachno\core\entities\Category;
    use pachno\core\entities\Client;
    use pachno\core\entities\Component;
    use pachno\core\entities\CustomDatatype;
    use pachno\core\entities\CustomDatatypeOption;
    use pachno\core\entities\Datatype;
    use pachno\core\entities\Edition;
    use pachno\core\entities\Issue;
    use pachno\core\entities\Issuetype;
    use pachno\core\entities\Milestone;
    use pachno\core\entities\Priority;
    use pachno\core\entities\Project;
    use pachno\core\entities\Reproducability;
    use pachno\core\entities\Resolution;
    use pachno\core\entities\Severity;
    use pachno\core\entities\Status;
    use pachno\core\entities\Team;
    use pachno\core\framework\Context;
    use pachno\core\framework\Event;

    /**
     * @var string[][] $errors
     * @var string[][] $permission_errors
     */

?>
<div class="message-box type-error">
    <?= fa_image_tag('exclamation-triangle'); ?>
    <span class="message">
        <?php foreach ($errors as $key => $error): ?>
            <?php if (is_array($error)): ?>
                <?php foreach ($error as $suberror): ?>
                    <?= $suberror; ?>
                <?php endforeach; ?>
            <?php elseif (is_bool($error)): ?>
                <?php if ($key == 'title' || in_array($key, Datatype::getAvailableFields(true)) || in_array($key, ['pain_bug_type', 'pain_likelihood', 'pain_effect'])): ?>
                    <?php

                        switch ($key)
                        {
                            case 'title':
                                echo __('You have to specify a title');
                                break;
                            case 'description':
                                echo __('You have to enter a description in the "%description" field', ['%description' => __('Description')]);
                                break;
                            case 'shortname':
                                echo __('You have to enter a label in the "%issue_label" field', ['%issue_label' => __('Issue label')]);
                                break;
                            case 'reproduction_steps':
                                echo __('You have to enter something in the "%steps_to_reproduce" field', ['%steps_to_reproduce' => __('Steps to reproduce')]);
                                break;
                            case 'edition':
                                echo __("Please specify a valid edition");
                                break;
                            case 'build':
                                echo __("Please specify a valid version / release");
                                break;
                            case 'component':
                                echo __("Please specify a valid component");
                                break;
                            case 'category':
                                echo __("Please specify a valid category");
                                break;
                            case 'status':
                                echo __("Please specify a valid status");
                                break;
                            case 'priority':
                                echo __("Please specify a valid priority");
                                break;
                            case 'reproducability':
                                echo __("Please specify a valid reproducability");
                                break;
                            case 'severity':
                                echo __("Please specify a valid severity");
                                break;
                            case 'resolution':
                                echo __("Please specify a valid resolution");
                                break;
                            case 'milestone':
                                echo __("Please specify a valid milestone");
                                break;
                            case 'estimated_time':
                                echo __("Please enter a valid estimate");
                                break;
                            case 'spent_time':
                                echo __("Please enter time already spent working on this issue");
                                break;
                            case 'percent_complete':
                                echo __("Please enter how many percent complete the issue already is");
                                break;
                            case 'pain_bug_type':
                                echo __("Please enter a valid triaged bug type");
                                break;
                            case 'pain_likelihood':
                                echo __("Please enter a valid triaged likelihood");
                                break;
                            case 'pain_effect':
                                echo __("Please enter a valid triaged effect");
                                break;
                            default:
                                echo __("Please triage the reported issue, so the user pain score can be properly calculated");
                                break;
                        }

                    ?>
                <?php elseif (CustomDatatype::doesKeyExist($key)): ?>
                    <?= __('Required field "%field_name" is missing or invalid', array('%field_name' => CustomDatatype::getByKey($key)->getDescription())); ?>
                <?php else:

                    $event = new Event('core', 'reportissue.validationerror', $key);
                    $event->setReturnValue($key);
                    $event->triggerUntilProcessed();
                    echo __('A validation error occured: %error', array('%error' => $event->getReturnValue()));

                ?>
                <?php endif; ?>
            <?php else: ?>
                <?= $error; ?>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php foreach ($permission_errors as $key => $p_error): ?>
            <?php if (is_array($p_error)): ?>
                <?php foreach ($p_error as $p_suberror): ?>
                    <?= $p_suberror; ?>
                <?php endforeach; ?>
            <?php elseif (is_bool($p_error)): ?>
                <?php if (in_array($key, Datatype::getAvailableFields(true))): ?>
                    <?php

                        switch ($key)
                        {
                            case 'description':
                                echo __("You don't have access to enter a description");
                                break;
                            case 'shortname':
                                echo __("You don't have access to enter an issue label");
                                break;
                            case 'reproduction_steps':
                                echo __("You don't have access to enter steps to reproduce");
                                break;
                            case 'edition':
                                echo __("You don't have access to add edition information");
                                break;
                            case 'build':
                                echo __("You don't have access to enter release information");
                                break;
                            case 'component':
                                echo __("You don't have access to enter component information");
                                break;
                            case 'category':
                                echo __("You don't have access to specify a category");
                                break;
                            case 'status':
                                echo __("You don't have access to specify a status");
                                break;
                            case 'priority':
                                echo __("You don't have access to specify a priority");
                                break;
                            case 'reproducability':
                                echo __("You don't have access to specify reproducability");
                                break;
                            case 'severity':
                                echo __("You don't have access to specify a severity");
                                break;
                            case 'resolution':
                                echo __("You don't have access to specify a resolution");
                                break;
                            case 'estimated_time':
                                echo __("You don't have access to estimate the issue");
                                break;
                            case 'spent_time':
                                echo __("You don't have access to specify time already spent working on the issue");
                                break;
                            case 'percent_complete':
                                echo __("You don't have access to specify how many percent complete the issue is");
                                break;
                        }

                    ?>
                <?php else: ?>
                    <?= __('You don\'t have access to enter "%field_name"', array('%field_name' => CustomDatatype::getByKey($key)->getDescription())); ?>
                <?php endif; ?>
            <?php else: ?>
                <?= $p_error; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </span>
</div>
