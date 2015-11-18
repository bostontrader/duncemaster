<div id="SemestersView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="semesters view large-9 medium-8 columns content">
        <h3><?= h($semester->id) ?></h3>
        <table id="SemesterViewTable" class="vertical-table">
            <tr id="year">
                <th><?= __('Year') ?></th>
                <td><?= $semester->year ?></td>
            </tr>
            <tr id="seq">
                <th><?= __('Seq') ?></th>
                <td><?= $semester->seq ?></td>
            </tr>
            <tr id="firstday">
                <th><?= __('First day') ?></th>
                <td><?= $semester->firstday ?></td>
            </tr>
        </table>
    </div>
</div>
