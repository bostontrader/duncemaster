<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Semesters'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="semesters form large-9 medium-8 columns content">
    <?= $this->Form->create($semester) ?>
    <fieldset>
        <legend><?= __('Add Semester') ?></legend>
        <?php
            echo $this->Form->input('year');
            echo $this->Form->input('seq');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
