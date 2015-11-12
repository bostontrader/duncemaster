<div id="UsersAdd">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="users form large-9 medium-8 columns content">
        <?= $this->Form->create($user,['id'=>'UserAddForm']) ?>
        <fieldset>
            <legend><?= __('Add User') ?></legend>
            <?php
                echo $this->Form->input('username',['id'=>'UserUsername']);
                echo $this->Form->input('password',['id'=>'UserPassword','type'=>'text']);
                echo $this->Form->input('roles._ids', ['id'=>'UserRoles', 'options' => $roles, 'multiple'=>true, 'empty' => '(none selected)']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
