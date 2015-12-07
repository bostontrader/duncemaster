<?php /* @var \App\Model\Entity\Role $role */ ?>
<?php /* @var \Cake\ORM\Query $roles */ ?>

<div id="RolesIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Role'), ['action' => 'add'],['id'=>'RoleAdd']) ?></li>
        </ul>
    </nav>
    <div class="roles index large-9 medium-8 columns content">
        <h3><?= __('Roles') ?></h3>
        <table id="RolesTable" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th id="title"><?= __('Title') ?></th>
                    <th id="actions" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roles as $role): ?>
                <tr>
                    <td><?= $role->title ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $role->id],['name'=>'RoleView']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $role->id],['name'=>'RoleEdit']) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $role->id], ['name'=>'RoleDelete','confirm' => __('Are you sure you want to delete # {0}?', $role->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>
