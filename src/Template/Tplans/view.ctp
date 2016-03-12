<?php
/* @var \App\Model\Entity\Tplan $tplan */
$this->Breadcrumb->makeTrail($tplan->title,$this->Html);
?>
<div id="TplansView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li><?= $this->Html->link(__('Tplan Elements'), ['controller' => 'TplanElements', 'tplan_id' => $tplan->id, '_method'=>'GET'], ['id'=>'TplanTplanElements']) ?></li>
        </ul>
    </nav>
    <div class="tplans view large-9 medium-8 columns content">
        <h3><?= h($tplan->id) ?></h3>
        <table id="TplanViewTable" class="vertical-table">
            <tr id="title">
                <th><?= __('Title') ?></th>
                <td><?= $tplan->title ?></td>
            </tr>
            <tr id="session_cnt">
                <th><?= __('Sessions') ?></th>
                <td><?= $tplan->session_cnt ?></td>
            </tr>
        </table>
    </div>

</div>
