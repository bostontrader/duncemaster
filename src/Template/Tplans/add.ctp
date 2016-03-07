<?php
/**
 * @var \App\Model\Entity\Tplan $tplan
 */
?>

<div id="TplansAdd">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="tplans form large-9 medium-8 columns content">
        <?= $this->Form->create($tplan,['id'=>'TplanAddForm']) ?>
        <fieldset>
            <legend><?= __('Add Tplan') ?></legend>
            <?php
                echo $this->Form->input('title',['id'=>'TplanTitle']);
                echo $this->Form->input('session_cnt',['id'=>'TplanSessionCnt']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
