<div id="CohortsAdd">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="cohorts form large-9 medium-8 columns content">
        <?= $this->Form->create($cohort,['id'=>'CohortAddForm']) ?>
        <fieldset>
            <legend><?= __('Add Cohort') ?></legend>
            <?php
                echo $this->Form->input('start_year',['id'=>'CohortStartYear','type'=>'text']);
                echo $this->Form->input('seq',['id'=>'CohortSeq','type'=>'text']);
                echo $this->Form->input('major_id', ['id'=>'CohortMajorId', 'options' => $majors, 'empty' => '(none selected)']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
