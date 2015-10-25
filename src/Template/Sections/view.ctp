<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
    </ul>
</nav>
<div class="sections view large-9 medium-8 columns content">
    <h3><?= h($section->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Id') ?></th>
            <td id="id"><?= $section->id ?></td>
        </tr>
        <tr>
            <th><?= __('Cohort') ?></th>
            <td id="cohort"><?= $section->has('cohort') ? $this->Html->link($section->cohort->nickname, ['controller' => 'Cohorts', 'action' => 'view', $section->cohort->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Subject') ?></th>
            <td id="subject"><?= $section->has('subject') ? $this->Html->link($section->subject->title, ['controller' => 'Subjects', 'action' => 'view', $section->subject->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Semester') ?></th>
            <td id="semester"><?= $section->has('semester') ? $this->Html->link($section->semester->nickname, ['controller' => 'Semesters', 'action' => 'view', $section->semester->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Weekday') ?></th>
            <td id="weekday"><?= $section->weekday ?></td>
        </tr>
        <tr>
            <th><?= __('Start time') ?></th>
            <td id="start_time"><?= $section->start_time ?></tr>
        </tr>
        <tr>
            <th><?= __('Teaching hours') ?></th>
            <td id="thours"><?= $section->thours ?></tr>
        </tr>
    </table>
</div>
