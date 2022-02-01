<?php
    
    use pachno\core\entities\AgileBoard;
    
    /**
     * @var AgileBoard $board
     */

?>
<?php foreach ($board->getColumns() as $column): ?>
    <?php include_component('agile/boardcolumnheader', compact('column')); ?>
<?php endforeach; ?>
<?php include_component('agile/addboardcolumnheader', ['board' => $board]); ?>
