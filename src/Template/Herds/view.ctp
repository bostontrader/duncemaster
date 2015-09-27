<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Herd'), ['action' => 'edit', $herd->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Herd'), ['action' => 'delete', $herd->id], ['confirm' => __('Are you sure you want to delete # {0}?', $herd->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Herds'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Herd'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Majors'), ['controller' => 'Majors', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Major'), ['controller' => 'Majors', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Sections'), ['controller' => 'Sections', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Section'), ['controller' => 'Sections', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="herds view large-9 medium-8 columns content">
    <h3><?= h($herd->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Major') ?></th>
            <td><?= $herd->has('major') ? $this->Html->link($herd->major->id, ['controller' => 'Majors', 'action' => 'view', $herd->major->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($herd->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Start Year') ?></th>
            <td><?= $this->Number->format($herd->start_year) ?></td>
        </tr>
        <tr>
            <th><?= __('Seq') ?></th>
            <td><?= $this->Number->format($herd->seq) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Sections') ?></h4>
        <?php if (!empty($herd->sections)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Herd Id') ?></th>
                <th><?= __('Subject Id') ?></th>
                <th><?= __('Weekday') ?></th>
                <th><?= __('Time') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($herd->sections as $sections): ?>
            <tr>
                <td><?= h($sections->id) ?></td>
                <td><?= h($sections->herd_id) ?></td>
                <td><?= h($sections->subject_id) ?></td>
                <td><?= h($sections->weekday) ?></td>
                <td><?= h($sections->time) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Sections', 'action' => 'view', $sections->id]) ?>

                    <?= $this->Html->link(__('Edit'), ['controller' => 'Sections', 'action' => 'edit', $sections->id]) ?>

                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Sections', 'action' => 'delete', $sections->id], ['confirm' => __('Are you sure you want to delete # {0}?', $sections->id)]) ?>

                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
</div>
