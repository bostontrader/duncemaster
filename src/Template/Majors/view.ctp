<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
    </ul>
</nav>
<div class="majors view large-9 medium-8 columns content">
    <h3><?= h($major->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Id') ?></th>
            <td id="id"><?= $major->id ?></td>
        </tr>
        <tr>
            <th><?= __('Title') ?></th>
            <td id="title"><?= $major->title ?></td>
        </tr>
        <tr>
            <th><?= __('SDesc') ?></th>
            <td id="sdesc"><?= $major->sdesc ?></td>
        </tr>
    </table>

    <div class="related">
        <h4><?= __('Related Cohorts') ?></h4>
        <?php if (!empty($major->cohorts)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Start Year') ?></th>
                <th><?= __('Major Id') ?></th>
                <th><?= __('Seq') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($major->cohorts as $cohorts): ?>
            <tr>
                <td><?= h($cohorts->id) ?></td>
                <td><?= h($cohorts->start_year) ?></td>
                <td><?= h($cohorts->major_id) ?></td>
                <td><?= h($cohorts->seq) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Cohorts', 'action' => 'view', $cohorts->id]) ?>

                    <?= $this->Html->link(__('Edit'), ['controller' => 'Cohorts', 'action' => 'edit', $cohorts->id]) ?>

                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Cohorts', 'action' => 'delete', $cohorts->id], ['confirm' => __('Are you sure you want to delete # {0}?', $cohorts->id)]) ?>

                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
</div>
