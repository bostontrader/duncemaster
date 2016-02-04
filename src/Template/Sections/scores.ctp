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
        </ul>
    </nav>
    <div class="sections form large-9 medium-8 columns content">
        <?= $this->Form->create(null) ?>
        <fieldset>
            <legend><?= __('Enter Tourist School Credentials') ?></legend>
            <?php
            echo $this->Form->input('username');
            echo $this->Form->input('password');
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>

</div>
