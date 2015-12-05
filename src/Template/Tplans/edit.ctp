<?php
/**
 * @var \App\Model\Entity\Tplan $tplan
 */
?>
<div id="TplansEdit">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li><?= $this->Html->link(__('New TplanElement'), ['action' => 'add'],['id'=>'TplanElementAdd']) ?></li>
        </ul>
    </nav>
    <div class="tplans form large-9 medium-8 columns content">
        <?= $this->Form->create($tplan,['id'=>'TplanEditForm']) ?>
        <fieldset>
            <legend><?= __('Edit Tplan') ?></legend>
            <?php
                echo $this->Form->input('title',['id'=>'TplanTitle']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>

    <?= $this->element('tplan_elements_index') ?>

</div>
