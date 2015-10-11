<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
    </ul>
</nav>
<div class="subjects form large-9 medium-8 columns content">
    <?= $this->Form->create($subject,['id'=>'SubjectAddForm']) ?>
    <fieldset>
        <legend><?= __('Add Subject') ?></legend>
        <?php
            echo $this->Form->input('title',['id'=>'SubjectTitle']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
