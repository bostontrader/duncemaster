<div id="TplanElementsAdd">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="tplan_elements form large-9 medium-8 columns content">
        <?= $this->Form->create($tplan_element,['id'=>'TplanElementAddForm']) ?>
        <fieldset>
            <legend><?= __('Add TplanElement') ?></legend>
            <?php
                echo $this->Form->input('col1',['id'=>'TplanElementCol1']);
                echo $this->Form->input('col2',['id'=>'TplanElementCol2']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
