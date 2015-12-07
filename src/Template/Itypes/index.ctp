<?php
/**
 * @var \App\Model\Entity\Itype $itype
 * @var \App\Model\Table\ItypesTable $itypes
 */
?>
<div id="ItypesIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Itype'), ['action' => 'add'],['id'=>'ItypeAdd']) ?></li>
        </ul>
    </nav>
    <div class="itypes index large-9 medium-8 columns content">
        <h3><?= __('Itypes') ?></h3>
        <table id="itypes" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th id="title"><?= __('Title') ?></th>
                    <th id="actions" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itypes as $itype): ?>
                <tr>
                    <td><?= $itype->title ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $itype->id],['name'=>'ItypeView']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $itype->id],['name'=>'ItypeEdit']) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $itype->id], ['name'=>'ItypeDelete', 'confirm' => __('Are you sure you want to delete # {0}?', $itype->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
