<?php
/**
 * @var \App\Model\Table\TplanElementsTable $tplan_elements
 */
?>
<div class="tplan_elements index large-9 medium-8 columns content">
    <h3><?= __('TplanElements') ?></h3>
    <table id="TplanElementsTable" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th id="start_thour"><?= __('Start Thour') ?></th>
                <th id="stop_thour"><?= __('Stop Thour') ?></th>
                <th id="col1"><?= __('Col1') ?></th>
                <th id="col2"><?= __('Col2') ?></th>
                <th id="col3"><?= __('Col3') ?></th>
                <th id="col4"><?= __('Col4') ?></th>
                <th id="actions" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tplan_elements as $tplan_element): ?>
            <tr>
                <td><?= $tplan_element->start_thour ?></td>
                <td><?= $tplan_element->stop_thour ?></td>
                <td><?= $tplan_element->col1 ?></td>
                <td><?= $tplan_element->col2 ?></td>
                <td><?= $tplan_element->col3 ?></td>
                <td><?= $tplan_element->col4 ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('Edit'), ['tplan_id'=>$tplan['id'],'action'=>'edit',$tplan_element->id,'_method'=>'GET'],['name'=>'TplanElementEdit']) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
