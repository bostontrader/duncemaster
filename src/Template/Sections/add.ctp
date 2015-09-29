<nav class="large-3 medium-4 columns" id="actions-sidebar">

</nav>
<div class="sections form large-9 medium-8 columns content">
    <?= $this->Form->create($section) ?>
    <fieldset>
        <legend><?= __('Add Section') ?></legend>
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
