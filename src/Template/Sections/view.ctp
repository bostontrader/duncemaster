<?php /* @var \App\Model\Entity\Section $section */ ?>
<div id="SectionsView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="sections view large-9 medium-8 columns content">
        <table id="SectionViewTable" class="vertical-table">
            <tr id="cohort">
                <th><?= __('Cohort') ?></th>
                <td><?= $section->cohort->nickname ?></td>
            </tr>
            <tr id="subject">
                <th><?= __('Subject') ?></th>
                <td><?= $section->subject->title ?></td>
            </tr>
            <tr id="semester">
                <th><?= __('Semester') ?></th>
                <td><?= $section->semester->nickname ?></td>
            </tr>
            <tr id="teacher">
                <th><?= __('Teacher') ?></th>
                <td><?= $section->teacher->fam_name ?></td>
            </tr>
            <tr id="seq">
                <th><?= __('Seq') ?></th>
                <td><?= $section->seq ?></td>
            </tr>
            <tr id="tplan">
                <th><?= __('Tplan') ?></th>
                <td><?= $section->tplan->title ?></td>
            </tr>
            <tr id="weekday">
                <th><?= __('Weekday') ?></th>
                <td><?= $section->weekday ?></td>
            </tr>
            <tr id="start_time">
                <th><?= __('Start time') ?></th>
                <td><?= $section->start_time ?></td>
            </tr>
            <tr id="thours">
                <th><?= __('Teaching hours') ?></th>
                <td><?= $section->thours ?></td>
            </tr>
        </table>
    </div>

    <?= $this->element('clazzes_index') ?>

</div>