<div  id="UsersLogin" class="users form">
<?= $this->Flash->render('auth') ?>
<?= $this->Form->create(null,['id'=>'UserLoginForm']) ?>
<fieldset>
<legend><?= __('Please enter your username and password') ?></legend>
<?= $this->Form->input('username',['id'=>'UserUsername']) ?>
<?= $this->Form->input('password',['id'=>'UserPassword','type'=>'text']) ?>
</fieldset>
<?= $this->Form->button(__('Login')); ?>
<?= $this->Form->end() ?>
</div>
