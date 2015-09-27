<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Students'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="students form large-9 medium-8 columns content">
    <?= $this->Form->create($student) ?>
    <fieldset>
        <legend><?= __('Add Student') ?></legend>
        <?php
            echo $this->Form->input('sid');
            echo $this->Form->input('fam_name');
            echo $this->Form->input('giv_name');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
