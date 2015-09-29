<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Semester'), ['action' => 'edit', $semester->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Semester'), ['action' => 'delete', $semester->id], ['confirm' => __('Are you sure you want to delete # {0}?', $semester->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Semesters'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Semester'), ['action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="semesters view large-9 medium-8 columns content">
    <h3><?= h($semester->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $semester->id ?></td>
        </tr>
        <tr>
            <th><?= __('Year') ?></th>
            <td><?= $semester->year ?></td>
        </tr>
        <tr>
            <th><?= __('Seq') ?></th>
            <td><?= $semester->seq ?></td>
        </tr>
    </table>
</div>
