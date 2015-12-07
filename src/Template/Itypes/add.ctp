<?php
/**
 * @var \App\Model\Entity\Itype $itypes
 */
?>
<div id="ItypesAdd">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="itypes form large-9 medium-8 columns content">
        <?= $this->Form->create($itype,['id'=>'ItypeAddForm']) ?>
        <fieldset>
            <legend><?= __('Add Itype') ?></legend>
            <?php
                echo $this->Form->input('title',['id'=>'ItypeTitle']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
