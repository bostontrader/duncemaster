<?php
/**

 */
?>
<div id="InteractionsAttend">
    <div class="interactions index large-9 medium-8 columns content">
        <h3><?= __('Interactions') ?></h3>
        <?= $this->Form->create(null,['id'=>'InteractionAttendForm']) ?>

        <table id="InteractionsTable" cellpadding="0" cellspacing="0">
            <thead>
            <tr>
                <th id="sid"><?= __('SID') ?></th>
                <th id="fam_name"><?= __('Fam Name') ?></th>
                <th id="giv_name"><?= __('Giv Name') ?></th>
                <th id="phonetic_name"><?= __('Phonetic Name') ?></th>
                <th id="attend"><?= __('Attend') ?></th>
            </tr>
            </thead>
            <tbody>
                <?php foreach ($studentsResults as $student): ?>
                    <tr>
                    <td><?= $student['sid'] ?></td>
                    <td><?= $student['fam_name'] ?></td>
                    <td><?= $student['giv_name'] ?></td>
                    <td><?= $student['phonetic_name'] ?></td>
                    <td><?= $this->Form->input('attend.'.$student['student_id'],['type'=>'checkbox']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?= $this->Form->button(__('Submit')) ?>
                <?= $this->Form->end() ?>
            </tbody>
        </table>
    </div>
</div>