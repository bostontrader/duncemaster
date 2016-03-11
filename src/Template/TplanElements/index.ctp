<div id="TplanElementsIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New TplanElement'), ['tplan_id'=>$tplan['id'],'action'=>'add','_method'=>'GET'],['id'=>'TplanElementAdd']) ?></li>
        </ul>
    </nav>

    <?= $this->element('tplan_elements_index') ?>

</div>
