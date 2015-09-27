<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Interaction'), ['action' => 'edit', $interaction->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Interaction'), ['action' => 'delete', $interaction->id], ['confirm' => __('Are you sure you want to delete # {0}?', $interaction->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Interactions'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Interaction'), ['action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="interactions view large-9 medium-8 columns content">
    <h3><?= h($interaction->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($interaction->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Class Id') ?></th>
            <td><?= $this->Number->format($interaction->class_id) ?></td>
        </tr>
        <tr>
            <th><?= __('Student Id') ?></th>
            <td><?= $this->Number->format($interaction->student_id) ?></td>
        </tr>
    </table>
</div>
