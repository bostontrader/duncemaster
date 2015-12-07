<?php
/**
 * @var \App\Model\Entity\User $user
 * @var \App\Model\Table\UsersTable $users
 */
?>

<div id="UsersIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New User'), ['action' => 'add'],['id'=>'UserAdd']) ?></li>
        </ul>
    </nav>
    <div class="users index large-9 medium-8 columns content">
        <h3><?= __('Users') ?></h3>
        <table id="UsersTable" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th id="username"><?= __('username') ?></th>
                    <th id="actions" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user->username ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $user->id],['name'=>'UserView']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $user->id],['name'=>'UserEdit']) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $user->id], ['name'=>'UserDelete','confirm' => __('Are you sure you want to delete # {0}?', $user->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
