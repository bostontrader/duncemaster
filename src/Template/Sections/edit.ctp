<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $section->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $section->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Sections'), ['action' => 'index']) ?></li>

    </ul>
</nav>
<div class="sections form large-9 medium-8 columns content">
    <?= $this->Form->create($section) ?>
    <fieldset>
        <legend><?= __('Edit Section') ?></legend>
        <?php
            echo $this->Form->input('cohort_id', ['options' => $cohorts]);
            echo $this->Form->input('subject_id', ['options' => $subjects]);
            echo $this->Form->input('weekday');
            echo $this->Form->input('time');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
