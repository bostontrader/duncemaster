<div id="ClazzesIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Class'), ['action' => 'add'],['id'=>'ClazzAdd']) ?></li>
        </ul>
    </nav>
    <div class="clazzes index large-9 medium-8 columns content">
        <h3><?= __('Classes') ?></h3>
        <table id="ClazzesTable" cellpadding="0" cellspacing="0">
            <thead>
            <tr>
                <th id="section"><?= __('section') ?></th>
                <th id="week"><?= __('week') ?></th>
                <th id="event_datetime"><?= __('datetime') ?></th>
                <th id="actions" class="actions"><?= __('Actions') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($clazzes as $clazz): ?>
                <tr>
                    <td><?= $clazz->section->nickname ?></td>
                    <td><?= $clazz->week ?></td>
                    <td><?= $clazz->event_datetime ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $clazz->id],['name'=>'ClazzView']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $clazz->id],['name'=>'ClazzEdit']) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $clazz->id], ['name'=>'ClazzDelete','confirm' => __('Are you sure you want to delete # {0}?', $clazz->id)]) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>