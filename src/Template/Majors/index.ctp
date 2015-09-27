<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Major'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Herds'), ['controller' => 'Herds', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Herd'), ['controller' => 'Herds', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="majors index large-9 medium-8 columns content">
    <h3><?= __('Majors') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('id') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($majors as $major): ?>
            <tr>
                <td><?= $this->Number->format($major->id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $major->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $major->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $major->id], ['confirm' => __('Are you sure you want to delete # {0}?', $major->id)]) ?>
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
