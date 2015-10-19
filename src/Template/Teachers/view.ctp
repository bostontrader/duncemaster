<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
    </ul>
</nav>
<div class="teachers view large-9 medium-8 columns content">
    <h3><?= h($teacher->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Id') ?></th>
            <td id="id""><?= $teacher->id ?></td>
        </tr>
        <tr>
            <th><?= __('Given name') ?></th>
            <td id="giv_name""><?= $teacher->giv_name ?></td>
        </tr>
    </table>

</div>
