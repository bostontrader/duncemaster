<?php
/**
 * @var \App\Model\Entity\Teacher $teacher
 * @var \App\Model\Table\TeachersTable $teachers
 */
?>

<div id="TeachersIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Teacher'), ['action' => 'add'],['id'=>'TeacherAdd']) ?></li>
    </ul>
</nav>
<div class="teachers index large-9 medium-8 columns content">
    <h3><?= __('Teachers') ?></h3>
    <table id="TeachersTable" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th id="fam_name"><?= __('family name') ?></th>
                <th id="giv_name"><?= __('given name') ?></th>
                <th id="username"><?= __('username') ?></th>
                <th id="actions" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($teachers as $teacher): ?>
            <tr>
                <td><?= $teacher->fam_name ?></td>
                <td><?= $teacher->giv_name ?></td>
                <td><?= is_null($teacher->user)?'':$teacher->user->username ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $teacher->id],['name'=>'TeacherView']) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $teacher->id],['name'=>'TeacherEdit']) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $teacher->id], ['name'=>'TeacherDelete','confirm' => __('Are you sure you want to delete # {0}?', $teacher->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>
