<?php
/**
 * @var \App\Model\Entity\Section $section
 * @var \App\Model\Table\CohortsTable $cohorts
 * @var \App\Model\Table\SemestersTable $semesters
 * @var \App\Model\Table\SubjectsTable $subjects
 * @var \App\Model\Table\TeachersTable $teachers
 * @var \App\Model\Table\TplansTable $tplans
 */
?>

<div id="SectionsEdit">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li><?= $this->Html->link(
                __('New Class'),
                ['controller' => 'clazzes', 'action' => 'add', 'section_id' => $section['id']],
                ['id'=>'ClazzAdd']
                ) ?>
            </li>
        </ul>
    </nav>
    <div class="sections form large-9 medium-8 columns content">
        <?= $this->Form->create($section,['id'=>'SectionEditForm']) ?>
        <fieldset>
            <legend><?= __('Edit Section') ?></legend>
            <?php
            echo $this->Form->input('cohort_id', ['id'=>'SectionCohortId', 'options' => $cohorts, 'empty' => '(none selected)']);
            echo $this->Form->input('subject_id', ['id'=>'SectionSubjectId', 'options' => $subjects, 'empty' => '(none selected)']);
            echo $this->Form->input('semester_id', ['id'=>'SectionSemesterId', 'options' => $semesters, 'empty' => '(none selected)']);
            echo $this->Form->input('teacher_id', ['id'=>'SectionTeacherId', 'options' => $teachers, 'empty' => '(none selected)']);
            echo $this->Form->input('seq',['id'=>'SectionSeq','type'=>'text']);
            echo $this->Form->input('tplan_id', ['id'=>'SectionTplanId', 'options' => $tplans, 'empty' => '(none selected)']);
            echo $this->Form->input('weekday',['id'=>'SectionWeekday']);
            echo $this->Form->input('start_time',['id'=>'SectionStartTime','type'=>'text']);
            echo $this->Form->input('thours',['id'=>'SectionTHours','type'=>'text']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>

</div>
