<?php  /* @var \App\Model\Entity\Semester $semester */ ?>

<div id="SemestersAdd">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="semesters form large-9 medium-8 columns content">
        <?= $this->Form->create($semester,['id'=>'SemesterAddForm']) ?>
        <fieldset>
            <legend><?= __('Add Semester') ?></legend>
            <?php
                echo $this->Form->input('year',['id'=>'SemesterYear', 'type'=>'text']);
                echo $this->Form->input('seq',['id'=>'SemesterSeq', 'type'=>'text']);
                echo $this->Form->input('firstday',['id'=>'SemesterFirstday', 'type'=>'text']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
