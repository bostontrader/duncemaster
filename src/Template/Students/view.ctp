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
            <tr id="cohort_nickname">
                <th><?= __('Cohort') ?></th>
                <td><?= $student->cohort->nickname ?></td>
            </tr>
        </table>

        <?= $this->Form->create(null, ['id'=>'StudentViewGradeForm','type'=>'get']) ?>
        <fieldset>
            <legend><?= __('My Grade') ?></legend>
            <?php
            echo $this->Form->input('section_id', ['id'=>'StudentViewSectionId', 'options' => $sections_list, 'val'=>2, 'empty' => '(none selected)']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>

        <h3><?= __('Scoring') ?></h3>
        <table id="StudentScoreTable" class="vertical-table">
            <tr>
                <th><?= __('Total classes for this section') ?></th>
                <td><?= 666 ?></td>
            </tr>
            <tr>
                <th><?= __('Total attendance, this semester') ?></th>
                <td><?= 666 ?></td>
            </tr>
            <tr>
                <th><?= __('Excused absences, this semester') ?></th>
                <td><?= 666 ?></td>
            </tr>
            <tr>
                <th><?= __('Ejected from class, this semester') ?></th>
                <td><?= 666 ?></td>
            </tr>
        </table>
    </div>
</div>
