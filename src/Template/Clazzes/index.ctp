<div id="ClazzesIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Class'), ['action' => 'add', 'section_id' => $section_id],['id'=>'ClazzAdd']) ?></li>
        </ul>
    </nav>

    <?= $this->element('clazzes_index') ?>

</div>