<?php
/**
 * @var \App\Model\Entity\Student $student
 * @var \App\Model\Table\StudentsTable $students
 */
?>
<div id="InteractionsAttend">
    <div class="interactions index large-9 medium-8 columns content">
        <h3><?= __('Interactions') ?></h3>
        <table id="InteractionsTable" cellpadding="0" cellspacing="0">
            <thead>
            <tr>
                <th id="fam_name"><?= __('Fam Name') ?></th>
                <th id="giv_name"><?= __('Giv Name') ?></th>
                <th id="section_id"><?= __('Section Id') ?></th>
            </tr>
            </thead>
            <tbody>
            <?= $this->Form->create(null,['id'=>'InteractionAttendForm']) ?>
            <td><?= $this->Form->input('quote.cat'); ?></td>
            <td><?= $this->Form->input('quote.dog'); ?></td>

            <?php foreach ($studentsResults as $student): ?>
                    <tr>
                    <td><?= $student['sid'] ?></td>
                    <td><?= $student['giv_name'] ?></td>
                    <td><?= $student['fam_name'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
            </tbody>
        </table>
    </div>
</div>