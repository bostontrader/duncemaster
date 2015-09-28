<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $cohort->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $cohort->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Cohorts'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Majors'), ['controller' => 'Majors', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Major'), ['controller' => 'Majors', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Sections'), ['controller' => 'Sections', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Section'), ['controller' => 'Sections', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="cohorts form large-9 medium-8 columns content">
    <?= $this->Form->create($cohort) ?>
    <fieldset>
        <legend><?= __('Edit Cohort') ?></legend>
        <?php
            echo $this->Form->input('start_year');
            echo $this->Form->input('major_id', ['options' => $majors]);
            echo $this->Form->input('seq');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
