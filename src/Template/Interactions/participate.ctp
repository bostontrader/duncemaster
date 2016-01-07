<?php
/**
 * @var array $participate
 * @var array $participationResults
 */
?>
<div id="InteractionsParticipate">
    <div class="interactions index large-9 medium-8 columns content">
        <h3><?= __('Interactions') ?></h3>
        <?= $this->Form->create(null,['id'=>'InteractionParticipateForm']) ?>

        <table id="InteractionsTable" cellpadding="0" cellspacing="0">
            <thead>
            <tr>
                <th id="sort"><?= __('Sort') ?></th>
                <th id="sid"><?= __('SID') ?></th>
                <th id="fam_name"><?= __('Fam Name') ?></th>
                <th id="giv_name"><?= __('Giv Name') ?></th>
                <th id="phonetic_name"><?= __('Phonetic Name') ?></th>
                <th id="participate"><?= __('Participate') ?></th>
            </tr>
            </thead>
            <tbody>
                <?php foreach ($participationResults as $participate): ?>
                    <tr>
                        <td><?= $participate['sort'] ?></td>
                        <td><?= $participate['sid'] ?></td>
                        <td><?= $participate['fam_name'] ?></td>
                        <td><?= $participate['giv_name'] ?></td>
                        <td><?= $participate['phonetic_name'] ?></td>
                        <td><?= $this->Form->input('participate.' . $participate['student_id'],['value'=>$participate['participate']]); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?= $this->Form->button(__('Submit')) ?>
                <?= $this->Form->end() ?>
            </tbody>
        </table>
    </div>
</div>
