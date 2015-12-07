<?php
/**
 * @var \App\Model\Entity\Cohort $cohort
 * @var \App\Model\Table\CohortsTable $cohorts
 */
?>
<div id="CohortsIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Cohort'), ['action' => 'add'],['id'=>'CohortAdd']) ?></li>
        </ul>
    </nav>
    <div class="cohorts index large-9 medium-8 columns content">
        <h3><?= __('Cohorts') ?></h3>
        <table id="CohortsTable" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th id="start_year"><?= __('Start year') ?></th>
                    <th id="major"><?= __('Major') ?></th>
                    <th id="seq"><?= __('Seq') ?></th>
                    <th id="nickname"><?= __('Nickname') ?></th>
                    <th id="actions" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cohorts as $cohort): ?>
                <tr>
                    <td><?= $cohort->start_year ?></td>
                    <td><?= $cohort->major->sdesc ?></td>
                    <td><?= $cohort->seq ?></td>
                    <?php $n = $cohort->nickname; ?>
                    <td><?= $n ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $cohort->id],['name'=>'CohortView']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $cohort->id],['name'=>'CohortEdit']) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $cohort->id], ['name'=>'CohortDelete','confirm' => __('Are you sure you want to delete # {0}?', $cohort->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
