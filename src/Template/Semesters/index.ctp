<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Semester'), ['action' => 'add']) ?></li>
    </ul>
</nav>
<div class="semesters index large-9 medium-8 columns content">
    <h3><?= __('Semesters') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('year') ?></th>
                <th><?= $this->Paginator->sort('seq') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($semesters as $semester): ?>
            <tr>
                <td><?= $this->Number->format($semester->id) ?></td>
                <td><?= $this->Number->format($semester->year) ?></td>
                <td><?= $this->Number->format($semester->seq) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $semester->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $semester->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $semester->id], ['confirm' => __('Are you sure you want to delete # {0}?', $semester->id)]) ?>
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
