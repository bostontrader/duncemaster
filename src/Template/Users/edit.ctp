<?php
/**
 * @var \App\Model\Entity\User $user
 * @var \App\Model\Table\RolesTable $roles
 */
?>

<div id="UsersEdit">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="users form large-9 medium-8 columns content">
        <?= $this->Form->create($user,['id'=>'UserEditForm']) ?>
        <fieldset>
            <legend><?= __('Edit User') ?></legend>
            <?php
                echo $this->Form->input('username',['id'=>'UserUsername']);
                echo $this->Form->input('password',['id'=>'UserPassword', 'type'=>'text']);
                echo $this->Form->input('roles._ids', ['id'=>'UserRoles', 'options' => $roles, 'multiple'=>true]);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
