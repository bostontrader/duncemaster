<?php
/**
 * @var \App\Model\Entity\Tplan $tplan
 * @var \App\Model\Table\TplansTable $tplans
 */
?>
<div id="TplansIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Tplan'), ['action' => 'add'],['id'=>'TplanAdd']) ?></li>
        </ul>
    </nav>
    <div class="tplans index large-9 medium-8 columns content">
        <h3><?= __('Tplans') ?></h3>
        <table id="TplansTable" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th id="title"><?= __('Title') ?></th>
                    <th id="session_cnt"><?= __('Sessions') ?></th>
                    <th id="actions" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tplans as $tplan): ?>
                <tr>
                    <td><?= $tplan->title ?></td>
                    <td><?= $tplan->session_cnt ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('PDF'), ['action' => 'pdf', $tplan->id],['name'=>'TplanPDF']) ?>
                        <?= $this->Html->link(__('View'), ['action' => 'view', 'id'=>$tplan->id, '_method'=>'GET'],['name'=>'TPlanView']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit',$tplan->id],['name'=>'TplanEdit']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>
