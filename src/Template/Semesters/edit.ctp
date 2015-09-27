<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $semester->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $semester->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Semesters'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="semesters form large-9 medium-8 columns content">
    <?= $this->Form->create($semester) ?>
    <fieldset>
        <legend><?= __('Edit Semester') ?></legend>
        <?php
            echo $this->Form->input('year');
            echo $this->Form->input('seq');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
