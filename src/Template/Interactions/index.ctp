<?php
/**
 * @var \App\Model\Entity\Interaction $interaction
 * @var \App\Model\Table\InteractionsTable $interactions
 */
?>
<div id="InteractionsIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Interaction'), ['action' => 'add'],['id'=>'InteractionAdd']) ?></li>
        </ul>
    </nav>
    <div class="interactions index large-9 medium-8 columns content">
        <h3><?= __('Interactions') ?></h3>
        <table id="InteractionsTable" cellpadding="0" cellspacing="0">
            <thead>
            <tr>
                <th id="clazz"><?= __('class') ?></th>
                <th id="student"><?= __('student') ?></th>
                <th id="itype"><?= __('itype') ?></th>
                <th id="actions" class="actions"><?= __('Actions') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php $c=count($interactions); foreach ($interactions as $interaction): ?>
                <tr>
                    <td><?= $interaction->clazz->nickname ?></td>
                    <td><?= $interaction->student->fullname ?></td>
                    <td><?= $interaction->itype->title ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $interaction->id],['name'=>'InteractionView']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $interaction->id],['name'=>'InteractionEdit']) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $interaction->id], ['name'=>'InteractionDelete','confirm' => __('Are you sure you want to delete # {0}?', $interaction->id)]) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>