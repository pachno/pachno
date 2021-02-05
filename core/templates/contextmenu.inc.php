<?php

    use pachno\core\entities\Project;
    use pachno\core\entities\User;
    use pachno\core\framework;
    use pachno\core\framework\Event;
    use pachno\core\framework\Settings;

    /**
     * @var User $pachno_user
     * @var framework\Routing $pachno_routing
     * @var framework\Response $pachno_response
     */

?>
<header>
    <nav class="menu-strip">
        <?= Event::createNew('core', 'header_menu_strip', $pachno_routing->getCurrentRoute())->triggerUntilProcessed()->getReturnValue(); ?>
    </nav>
</header>
