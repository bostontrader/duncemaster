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
                    <th id="actions" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tplans as $tplan): ?>
                <tr>
                    <td><?= $tplan->title ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $tplan->id],['name'=>'TplanView']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $tplan->id],['name'=>'TplanEdit']) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $tplan->id], ['name'=>'TplanDelete','confirm' => __('Are you sure you want to delete # {0}?', $tplan->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>
