<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
    </ul>
</nav>
<div class="semesters form large-9 medium-8 columns content">
    <?= $this->Form->create($semester,['id'=>'SemesterEditForm']) ?>
    <fieldset>
        <legend><?= __('Edit Semester') ?></legend>
        <?php
            echo $this->Form->input('year',['id'=>'SemesterYear']);
            echo $this->Form->input('seq',['id'=>'SemesterSeq']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
