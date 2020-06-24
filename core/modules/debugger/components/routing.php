<?php

    use pachno\core\framework\Context;

    /**
     * @var \pachno\core\framework\Routing $pachno_routing
     * @var \pachno\core\framework\routing\Route $routing
     */

?>
<li id="debug_routes">
    <h1>Routes (<?php echo count($pachno_routing->getRoutes()); ?>)</h1>
    <ul>
        <?php foreach ($pachno_routing->getRoutes() as $route_name => $route): ?>
            <li <?php if ($routing instanceof \pachno\core\framework\routing\Route && $routing->getName() == $route_name) echo 'class="selected"'; ?>>
                <span class="badge csrf <?php echo ($route->isCsrfProtected()) ? 'enabled' : ''; ?>">CSRF</span>
                <span class="badge routename"><?php echo $route_name; ?></span>
                <span class="badge url"><?php echo $route->getUrl(); ?></span>
                <span class="badge method">\pachno\<?php echo (Context::isInternalModule($route->getModuleName())) ? "core\\" : ''; ?><span class="badge modulename"><?php echo $route->getModuleName(); ?></span>\<?php echo $route->getModuleAction(); ?>()</span>
                <?php if ($route->isOverridden()): ?>
                    <span class="badge csrf enabled">Overridden</span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</li>
