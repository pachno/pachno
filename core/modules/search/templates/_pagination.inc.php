<?php

    $more_pages_button = false;

?>
<div class="issue-paginator paginator">
    <div class="prev-buttons">
        <?php if ($currentpage > 2): ?>
            <button class="button secondary" title="<?php echo __('First page'); ?>" onclick="Pachno.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', 0, this);"><?php echo fa_image_tag('spinner', ['class' => 'indicator fa-spin']); ?><?= fa_image_tag('angle-double-left'); ?></button>
        <?php else: ?>
            <button class="button secondary" disabled><?= fa_image_tag('angle-double-left'); ?></button>
        <?php endif; ?>
        <?php if ($currentpage > 1): ?>
            <button class="button secondary" title="<?php echo __('Previous page'); ?>" onclick="Pachno.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', <?php echo ($currentpage - 2) * $ipp; ?>, this);"><?php echo fa_image_tag('spinner', ['class' => 'indicator fa-spin']); ?><?= fa_image_tag('angle-left'); ?></button>
        <?php else: ?>
            <button class="button secondary" disabled><?= fa_image_tag('angle-left'); ?></button>
        <?php endif; ?>
    </div>
    <div class="page-buttons">
        <?php for ($cc = 1; $cc <= 5 && $cc < $pagecount; $cc++): ?>
            <?php if ($cc == $currentpage): ?>
                <button class="button secondary highlight"><?php echo $currentpage; ?></button>
            <?php else: ?>
                <button class="button secondary" onclick="Pachno.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', <?php echo $ipp * ($cc - 1); ?>, this);"><?php echo fa_image_tag('spinner', ['class' => 'indicator fa-spin']); ?><?php echo $cc; ?></button>
            <?php endif; ?>
        <?php endfor; ?>
        <?php if ($currentpage > 10): ?>
            <div class="dropper-container">
                <a href="javascript:void(0);" class="button icon secondary dropper"><?= fa_image_tag('ellipsis-h'); ?></a>
                <div class="dropdown-container from-bottom from-center">
                    <div class="list-mode">
                        <?php for ($cc = 6; $cc < $currentpage - 5; $cc++): ?>
                            <a class="list-item" onclick="Pachno.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', <?php echo $ipp * ($cc - 1); ?>, this);">
                                <?php echo fa_image_tag('spinner', ['class' => 'indicator fa-spin']); ?>
                                <span class="name"><?php echo __('Page %number', ['%number' => $cc]); ?></span>
                            </a>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php for ($cc = $currentpage - 5; $cc > 5 && $cc < $currentpage; $cc++): ?>
            <button class="button secondary" onclick="Pachno.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', <?php echo $ipp * ($cc - 1); ?>, this);"><?php echo fa_image_tag('spinner', ['class' => 'indicator fa-spin']); ?><?php echo $cc; ?></button>
        <?php endfor; ?>
        <?php if ($currentpage > 10): ?>
            <button class="button secondary highlight"><?php echo $currentpage; ?></button>
            <?php for ($cc = $currentpage + 1; $cc <= $currentpage + 5 && $cc < $pagecount - 5; $cc++): ?>
                <button class="button secondary" onclick="Pachno.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', <?php echo $ipp * ($cc - 1); ?>, this);"><?php echo fa_image_tag('spinner', ['class' => 'indicator fa-spin']); ?><?php echo $cc; ?></button>
            <?php endfor; ?>
        <?php endif; ?>
        <?php if ($currentpage + 6 < $pagecount - 5 && $pagecount > 21): ?>
            <div class="dropper-container">
                <a href="javascript:void(0);" class="button icon secondary dropper"><?= fa_image_tag('ellipsis-h'); ?></a>
                <div class="dropdown-container from-bottom from-center">
                    <div class="list-mode">
                        <?php for ($cc = $currentpage + 5; $cc < $pagecount - 5; $cc++): ?>
                            <a class="list-item" onclick="Pachno.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', <?php echo $ipp * ($cc - 1); ?>, this);">
                                <?php echo fa_image_tag('spinner', ['class' => 'indicator fa-spin']); ?>
                                <span class="name"><?php echo __('Page %number', ['%number' => $cc]); ?></span>
                            </a>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php for ($cc = $pagecount - 5; $cc <= $pagecount; $cc++): ?>
            <?php if ($cc != $currentpage && $cc > $currentpage): ?>
                <button class="button secondary" onclick="Pachno.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', <?php echo $ipp * ($cc - 1); ?>, this);"><?php echo fa_image_tag('spinner', ['class' => 'indicator fa-spin']); ?><?php echo $cc; ?></button>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
    <div class="next-buttons">
        <?php if ($currentpage < $pagecount): ?>
            <button class="button secondary" title="<?php echo __('Next page'); ?>" onclick="Pachno.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', <?php echo $currentpage * $ipp; ?>, this);"><?php echo fa_image_tag('spinner', ['class' => 'indicator fa-spin']); ?><?= fa_image_tag('angle-right'); ?></button>
        <?php else: ?>
            <button class="button secondary" disabled><?= fa_image_tag('angle-right'); ?></button>
        <?php endif; ?>
        <?php if ($currentpage < $pagecount - 1): ?>
            <button class="button secondary" title="<?php echo __('Last page'); ?>" onclick="Pachno.Search.toPage('<?php echo $route; ?>', '<?php echo $parameters; ?>', <?php echo ($pagecount - 1) * $ipp; ?>, this);"><?php echo fa_image_tag('spinner', ['class' => 'indicator fa-spin']); ?><?= fa_image_tag('angle-double-right'); ?></button>
        <?php else: ?>
            <button class="button secondary" disabled><?= fa_image_tag('angle-double-right'); ?></button>
        <?php endif; ?>
    </div>
</div>
