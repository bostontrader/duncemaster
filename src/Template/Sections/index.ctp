<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Section'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Herds'), ['controller' => 'Herds', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Herd'), ['controller' => 'Herds', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Subjects'), ['controller' => 'Subjects', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Subject'), ['controller' => 'Subjects', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="sections index large-9 medium-8 columns content">
    <h3><?= __('Sections') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('herd_id') ?></th>
                <th><?= $this->Paginator->sort('subject_id') ?></th>
                <th><?= $this->Paginator->sort('weekday') ?></th>
                <th><?= $this->Paginator->sort('time') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sections as $section): ?>
            <tr>
                <td><?= $this->Number->format($section->id) ?></td>
                <td><?= $section->has('herd') ? $this->Html->link($section->herd->id, ['controller' => 'Herds', 'action' => 'view', $section->herd->id]) : '' ?></td>
                <td><?= $section->has('subject') ? $this->Html->link($section->subject->id, ['controller' => 'Subjects', 'action' => 'view', $section->subject->id]) : '' ?></td>
                <td><?= $this->Number->format($section->weekday) ?></td>
                <td><?= h($section->time) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $section->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $section->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $section->id], ['confirm' => __('Are you sure you want to delete # {0}?', $section->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>
</div>
