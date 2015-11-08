<div id="RolesView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="roles view large-9 medium-8 columns content">
        <h3><?= h($role->id) ?></h3>
        <table id="RoleViewTable" class="vertical-table">
            <tr id="title">
                <th><?= __('Title') ?></th>
                <td><?= $role->title ?></td>
            </tr>
        </table>
    </div>
</div>
