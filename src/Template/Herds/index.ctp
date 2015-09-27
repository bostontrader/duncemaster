<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Herd'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Majors'), ['controller' => 'Majors', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Major'), ['controller' => 'Majors', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Sections'), ['controller' => 'Sections', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Section'), ['controller' => 'Sections', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="herds index large-9 medium-8 columns content">
    <h3><?= __('Herds') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('start_year') ?></th>
                <th><?= $this->Paginator->sort('major_id') ?></th>
                <th><?= $this->Paginator->sort('seq') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($herds as $herd): ?>
            <tr>
                <td><?= $this->Number->format($herd->id) ?></td>
                <td><?= $this->Number->format($herd->start_year) ?></td>
                <td><?= $herd->has('major') ? $this->Html->link($herd->major->id, ['controller' => 'Majors', 'action' => 'view', $herd->major->id]) : '' ?></td>
                <td><?= $this->Number->format($herd->seq) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $herd->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $herd->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $herd->id], ['confirm' => __('Are you sure you want to delete # {0}?', $herd->id)]) ?>
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
