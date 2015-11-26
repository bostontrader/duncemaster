<div id="ItypesEdit">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="itypes form large-9 medium-8 columns content">
        <?= $this->Form->create($itype,['id'=>'ItypeEditForm']) ?>
        <fieldset>
            <legend><?= __('Edit Itype') ?></legend>
            <?php
                echo $this->Form->input('title',['id'=>'ItypeTitle']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
