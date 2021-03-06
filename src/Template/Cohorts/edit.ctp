<?php
/**
 * @var \App\Model\Entity\Cohort $cohort
 * @var \App\Model\Table\MajorsTable $majors
 */
?>
<div id="CohortsEdit">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="cohorts form large-9 medium-8 columns content">
        <?= $this->Form->create($cohort,['id'=>'CohortEditForm']) ?>
        <fieldset>
            <legend><?= __('Edit Cohort') ?></legend>
            <?php
                echo $this->Form->input('start_year',['id'=>'CohortStartYear','type'=>'text']);
                echo $this->Form->input('seq',['id'=>'CohortSeq','type'=>'text']);
                echo $this->Form->input('major_id', ['id'=>'CohortMajorId', 'options' => $majors]);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
