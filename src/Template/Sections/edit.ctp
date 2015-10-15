<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
    </ul>
</nav>
<div class="sections form large-9 medium-8 columns content">
    <?= $this->Form->create($section,['id'=>'SectionEditForm']) ?>
    <fieldset>
        <legend><?= __('Edit Section') ?></legend>
        <?php
            //echo $this->Form->input('cohort_id', ['options' => $cohorts]);
            //echo $this->Form->input('subject_id', ['options' => $subjects]);
            echo $this->Form->input('weekday',['id'=>'SectionWeekday']);
            echo $this->Form->input('time');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
