<?php  /* @var \App\Model\Entity\Role $role */ ?>

<div id="RolesAdd">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="roles form large-9 medium-8 columns content">
        <?= $this->Form->create($role,['id'=>'RoleAddForm']) ?>
        <fieldset>
            <legend><?= __('Add Role') ?></legend>
            <?php
                echo $this->Form->input('title',['id'=>'RoleTitle']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
