<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
    </ul>
</nav>
<div class="semesters view large-9 medium-8 columns content">
    <h3><?= h($semester->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Id') ?></th>
            <td id="id"><?= $semester->id ?></td>
        </tr>
        <tr>
            <th><?= __('Year') ?></th>
            <td id="year"><?= $semester->year ?></td>
        </tr>
        <tr>
            <th><?= __('Seq') ?></th>
            <td id="seq"><?= $semester->seq ?></td>
        </tr>
    </table>
</div>
