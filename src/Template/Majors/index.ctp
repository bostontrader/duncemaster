<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Major'), ['action' => 'add']) ?></li>
    </ul>
</nav>
<div class="majors index large-9 medium-8 columns content">
    <h3><?= __('Majors') ?></h3>
    <table id = "majors" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th id="id"><?= __('id') ?></th>
                <th id="title"><?= __('Title') ?></th>
                <th id="sdesc"><?= __('SDesc') ?></th>
                <th id="actions" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($majors as $major): ?>
            <tr>
                <td><?= $this->Number->format($major->id) ?></td>
                <td><?= $major->title ?></td>
                <td><?= $major->sdesc ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $major->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $major->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $major->id], ['confirm' => __('Are you sure you want to delete # {0}?', $major->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>
