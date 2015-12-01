<div class="tplan_elements index large-9 medium-8 columns content">
    <h3><?= __('TplanElements') ?></h3>
    <table id="TplanElementsTable" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th id="tplan_id"><?= __('Tplan') ?></th>
                <th id="col1"><?= __('Col1') ?></th>
                <th id="col2"><?= __('Col2') ?></th>
                <th id="actions" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tplan_elements as $tplan_element): ?>
            <tr>
                <td><?= $tplan_element->tplan->title ?></td>
                <td><?= $tplan_element->col1 ?></td>
                <td><?= $tplan_element->col2 ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $tplan_element->id],['name'=>'TplanElementView']) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $tplan_element->id],['name'=>'TplanElementEdit']) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $tplan_element->id], ['name'=>'TplanElementDelete','confirm' => __('Are you sure you want to delete # {0}?', $tplan_element->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
