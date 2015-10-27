<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
    </ul>
</nav>
<div class="sections view large-9 medium-8 columns content">
    <h3><?= h($section->id) ?></h3>
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
        <tr id="weekday">
            <th><?= __('Weekday') ?></th>
            <td><?= $section->weekday ?></td>
        </tr>
        <tr id="start_time">
            <th><?= __('Start time') ?></th>
            <td><?= $section->start_time ?></tr>
        </tr>
        <tr id="thours">
            <th><?= __('Teaching hours') ?></th>
            <td><?= $section->thours ?></tr>
        </tr>
    </table>
</div>
