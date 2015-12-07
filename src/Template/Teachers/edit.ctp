<?php
/**
 * @var \App\Model\Entity\Teacher $teacher
 * @var \App\Model\Table\UsersTable $users
 */
?>

<div id="TeachersEdit">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="teachers form large-9 medium-8 columns content">
        <?= $this->Form->create($teacher,['id'=>'TeacherEditForm']) ?>
        <fieldset>
            <legend><?= __('Edit Teacher') ?></legend>
            <?php
                echo $this->Form->input('fam_name',['id'=>'TeacherFamName']);
                echo $this->Form->input('giv_name',['id'=>'TeacherGivName']);
                echo $this->Form->input('user_id', ['id'=>'TeacherUserId', 'options' => $users, 'empty' => '(none selected)']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
