<?php
/**
 * @var \App\Model\Entity\Interaction $interaction
 * @var \App\Model\Table\ClazzesTable $clazzes
 * @var \App\Model\Table\StudentsTable $students
 * @var \App\Model\Table\ItypesTable $itypes
 */
?>
<div id="InteractionsEdit">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="interactions form large-9 medium-8 columns content">
        <?= $this->Form->create($interaction,['id'=>'InteractionEditForm']) ?>
        <fieldset>
            <legend><?= __('Edit Interaction') ?></legend>
            <?php
            echo $this->Form->input('clazz_id', ['id'=>'InteractionClazzId','options' => $clazzes, 'empty' => '(none selected)']);
            echo $this->Form->input('student_id', ['id'=>'InteractionStudentId','options' => $students, 'empty' => '(none selected)']);
            echo $this->Form->input('itype_id', ['id'=>'InteractionItypeId','options' => $itypes, 'empty' => '(none selected)']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>