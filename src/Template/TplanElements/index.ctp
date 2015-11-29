<div id="TplanElementsIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New TplanElement'), ['action' => 'add'],['id'=>'TplanElementAdd']) ?></li>
        </ul>
    </nav>

    <?= $this->element('tplan_elements_index') ?>

</div>
