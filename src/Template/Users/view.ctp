<?php
/**
 * @var \App\Model\Entity\User $user
 */
?>

<div id="UsersView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="users view large-9 medium-8 columns content">
        <h3><?= h($user->id) ?></h3>
        <table id="UserViewTable" class="vertical-table">
            <tr id="username">
                <th><?= __('Username') ?></th>
                <td><?= $user->username ?></td>
            </tr>
        </table>
    </div>
</div>
