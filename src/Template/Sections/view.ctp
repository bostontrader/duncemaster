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
            <th><?= __('Cohort') ?></th>
            <td><?= $section->has('cohort') ? $this->Html->link($section->cohort->id, ['controller' => 'Cohorts', 'action' => 'view', $section->cohort->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Subject') ?></th>
            <td><?= $section->has('subject') ? $this->Html->link($section->subject->id, ['controller' => 'Subjects', 'action' => 'view', $section->subject->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $section->id ?></td>
        </tr>
        <tr>
            <th><?= __('Weekday') ?></th>
            <td><?= $this->Time->format($section->time,'E') ?></td>
        </tr>
        <tr>
            <th><?= __('Time') ?></th>
            <td><?= $this->Time->format($section->time,'hh:m') ?></tr>
        </tr>
    </table>
</div>
