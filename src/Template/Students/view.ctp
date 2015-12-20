<?php
/**
 * @var array $grade
 * @var int $section_id
 * @var \Cake\ORM\Query $sections_list
 * @var \App\Model\Entity\Student $student
 */
?>
<div id="StudentsView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="students view large-9 medium-8 columns content">
        <h3><?= h($student->id) ?></h3>
        <table id="StudentViewTable" class="vertical-table">
            <tr id="sid">
                <th><?= __('SID') ?></th>
                <td><?= $student->sid ?></td>
            </tr>
            <tr id="fam_name">
                <th><?= __('Family name') ?></th>
                <td><?= $student->fam_name ?></td>
            </tr>
            <tr  id="giv_name">
                <th><?= __('Given name') ?></th>
                <td><?= $student->giv_name ?></td>
            </tr>
            <tr  id="phonetic_name">
                <th><?= __('Phonetic name') ?></th>
                <td><?= $student->phonetic_name ?></td>
            </tr>
            <tr id="cohort_nickname">
                <th><?= __('Cohort') ?></th>
                <td><?= $student->cohort->nickname ?></td>
            </tr>
            <tr id="username">
                <th><?= __('User') ?></th>
                <td><?= is_null($student->user)?'':$student->user->username ?></td>
            </tr>
        </table>

        <?=
            $this->Form->create(null, ['action'=>'/view/'.$student->id,'id'=>'StudentViewGradeForm','type'=>'get'])
        ?>
        <fieldset>
            <legend><?= __('My Grade') ?></legend>
            <?=
                $this->Form->input('section_id', ['id'=>'StudentViewSectionId', 'options' => $sections_list, 'val'=>$section_id, 'empty' => '(none selected)']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>

        <?php if(!is_null($section_id)) { ?>
        <h3><?= __('Scoring') ?></h3>
        <table id="StudentGradingTable" class="vertical-table">
            <tr>
                <th><?= __('Total classes for this section') ?></th>
                <td><?= $grade['clazzCnt'] ?></td>
            </tr>
            <tr>
                <th><?= __('Total attendance, this semester') ?></th>
                <td><?= $grade['attendCnt'] ?></td>
            </tr>
            <tr>
                <th><?= __('Excused absences, this semester') ?></th>
                <td><?= $grade['excusedAbsenceCnt'] ?></td>
            </tr>
            <tr>
                <th><?= __('Ejected from class, this semester') ?></th>
                <td><?= $grade['ejectedFromClassCnt'] ?></td>
            </tr>
            <tr>
                <th><?= __('Left class early, this semester') ?></th>
                <td><?= $grade['leftClassCnt'] ?></td>
            </tr>
            <tr>
                <th><?= __('Score- Attendance, this semester') ?></th>
                <td><?= $grade['scoreAttendance'] ?></td>
            </tr>
        </table>
        <?php } ?>
    </div>
</div>
