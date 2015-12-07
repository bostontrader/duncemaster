<?php
/**
 * @var \App\Model\Entity\Student $student
 * @var \App\Model\Table\CohortsTable $cohorts
 * @var \App\Model\Table\UsersTable $users
 */
?>

<div id="StudentsAdd">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="students form large-9 medium-8 columns content">
        <?= $this->Form->create($student,['id'=>'StudentAddForm']) ?>
        <fieldset>
            <legend><?= __('Add Student') ?></legend>
            <?php
                echo $this->Form->input('sid',['id'=>'StudentSid']);
                echo $this->Form->input('fam_name',['id'=>'StudentFamName']);
                echo $this->Form->input('giv_name',['id'=>'StudentGivName']);
                echo $this->Form->input('cohort_id', ['id'=>'StudentCohortId', 'options' => $cohorts, 'empty' => '(none selected)']);
                echo $this->Form->input('user_id', ['id'=>'StudentUserId', 'options' => $users, 'empty' => '(none selected)']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
