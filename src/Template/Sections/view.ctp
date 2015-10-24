<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Section'), ['action' => 'edit', $section->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Section'), ['action' => 'delete', $section->id], ['confirm' => __('Are you sure you want to delete # {0}?', $section->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Sections'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Section'), ['action' => 'add']) ?> </li>
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
            <td id="cohort"><?= $section->has('cohort') ? $this->Html->link($section->cohort->id, ['controller' => 'Cohorts', 'action' => 'view', $section->cohort->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Subject') ?></th>
            <td id="subject"><?= $section->has('subject') ? $this->Html->link($section->subject->id, ['controller' => 'Subjects', 'action' => 'view', $section->subject->title]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Semester') ?></th>
            <td id="semester"><?= $section->has('semester') ? $this->Html->link($section->semester->id, ['controller' => 'Semesters', 'action' => 'view', $section->semester->nickname]) : '' ?></td>
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
