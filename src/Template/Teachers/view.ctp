<?php
/**
 * @var \App\Model\Entity\Teacher $teacher
 * @var \App\Model\Table\UsersTable $users
 */
?>

<div id="TeachersView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="teachers view large-9 medium-8 columns content">
        <h3><?= h($teacher->id) ?></h3>
        <table id="TeacherViewTable" class="vertical-table">
            <tr id="fam_name">
                <th><?= __('Family name') ?></th>
                <td><?= $teacher->fam_name ?></td>
            </tr>
            <tr id="giv_name">
                <th><?= __('Given name') ?></th>
                <td><?= $teacher->giv_name ?></td>
            </tr>
            <tr id="username">
                <th><?= __('User') ?></th>
                <td><?= is_null($teacher->user)?'':$teacher->user->username ?></td>
            </tr>
        </table>
    </div>
</div>
