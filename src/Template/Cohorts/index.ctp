<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Cohort'), ['action' => 'add']) ?></li>
    </ul>
</nav>
<div class="cohorts index large-9 medium-8 columns content">
    <h3><?= __('Cohorts') ?></h3>
    <table id="cohorts" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th id="id"><?= __('id') ?></th>
                <th id="start_year"><?= __('Start year') ?></th>
                <th id="major"><?= __('Major') ?></th>
                <th id="seq"><?= __('Seq') ?></th>
                <th id="nickname"><?= __('Nickname') ?></th>
                <th id="actions" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cohorts as $cohort): ?>
            <tr>
                <td><?= $cohort->id ?></td>
                <td><?= $cohort->start_year ?></td>
                <td><?= $cohort->has('major') ? $this->Html->link($cohort->major->sdesc, ['controller' => 'Majors', 'action' => 'view', $cohort->major->id]) : '' ?></td>
                <td><?= $cohort->seq ?></td>
                <?php $n = $cohort->nickname; ?>
                <td><?= $n ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $cohort->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $cohort->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $cohort->id], ['confirm' => __('Are you sure you want to delete # {0}?', $cohort->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>
