<?php
/**
 * @var array $clazzes
 */
?>

<div class="clazzes index large-9 medium-8 columns content">
    <h3><?= __('Classes') ?></h3>
    <table id="ClazzesTable" cellpadding="0" cellspacing="0">
        <thead>
        <tr>
            <th id="event_datetime"><?= __('datetime') ?></th>
            <th id="comments"><?= __('comments') ?></th>
            <th id="attend"><?= __('attend') ?></th>
            <th id="participate"><?= __('participate') ?></th>
            <th id="actions" class="actions"><?= __('Actions') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($clazzes as $clazz): ?>
            <tr>
                <td><?= $clazz['event_datetime'] ?></td>
                <td><?= $clazz['comments'] ?></td>
                <td><?= $clazz['attend_cnt'] ?></td>
                <td><?= $clazz['participate_cnt'] ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('Attendance'), ['controller' => 'interactions', 'action' => 'attend', 'clazz_id' => $clazz['id'], 'section_id' => $section_id],['name'=>'ClazzAttend']) ?>
                    <?= $this->Html->link(__('Participation'), ['controller' => 'interactions', 'action' => 'participate', 'clazz_id' => $clazz['id'], 'section_id' => $section_id],['name'=>'ClazzParticipate']) ?>
                    <?= $this->Html->link(__('View'), ['action' => 'view', $clazz['id']],['name'=>'ClazzView']) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $clazz['id']],['name'=>'ClazzEdit']) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $clazz['id']], ['name'=>'ClazzDelete','confirm' => __('Are you sure you want to delete # {0}?', $clazz['id'])]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
