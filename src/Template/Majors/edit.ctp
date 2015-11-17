<div id="MajorsEdit">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="majors form large-9 medium-8 columns content">
        <?= $this->Form->create($major,['id'=>'MajorEditForm']) ?>
        <fieldset>
            <legend><?= __('Edit Major') ?></legend>
            <?php
                echo $this->Form->input('title',['id'=>'MajorTitle']);
                echo $this->Form->input('sdesc',['id'=>'MajorSDesc']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
