<div id="TeachersView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="teachers view large-9 medium-8 columns content">
        <h3><?= h($teacher->id) ?></h3>
        <table id="TeacherViewTable" class="vertical-table">
            <tr id="giv_name">
                <th><?= __('Given name') ?></th>
                <td><?= $teacher->giv_name ?></td>
            </tr>
        </table>
    </div>
</div>
