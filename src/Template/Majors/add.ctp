<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Majors'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="majors form large-9 medium-8 columns content">
    <?= $this->Form->create($major) ?>
    <fieldset>
        <legend><?= __('Add Major') ?></legend>
        <?php
            echo $this->Form->input('title');
            echo $this->Form->input('sdesc');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
