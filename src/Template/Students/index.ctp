<?php
/**
 * @var \App\Model\Entity\Student $student
 * @var \Cake\ORM\Query $students
 */
?>
<div id="StudentsIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Student'), ['action' => 'add'],['id'=>'StudentAdd']) ?></li>
        </ul>
    </nav>
    <div class="students index large-9 medium-8 columns content">
        <h3><?= __('Students') ?></h3>
        <table id="StudentsTable" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th id="sid"><?= __('Student ID') ?></th>
                    <th id="fullname"><?= __('Name') ?></th>
                    <th id="phonetic_name"><?= __('Phonetic Name') ?></th>
                    <th id="cohort_nickname"><?= __('Cohort') ?></th>
                    <th id="username"><?= __('username') ?></th>
                    <th id="actions" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= $student->sid ?></td>
                    <td><?= $student->fam_name . $student->giv_name ?></td>
                    <td><?= $student->phonetic_name ?></td>
                    <td><?= $student->cohort->nickname ?></td>
                    <td><?= is_null($student->user)?'':$student->user->username ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $student->id],['name'=>'StudentView']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $student->id],['name'=>'StudentEdit']) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $student->id], ['name'=>'StudentDelete','confirm' => __('Are you sure you want to delete # {0}?', $student->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>
