<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
    </ul>
</nav>
<div class="students view large-9 medium-8 columns content">
    <h3><?= h($student->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Id') ?></th>
            <td id="id"><?= $student->id ?></td>
        </tr>
        <tr>
            <th><?= __('SID') ?></th>
            <td id="sid"><?= $student->id ?></td>
        </tr>
        <tr>
            <th><?= __('Family name') ?></th>
            <td id="fam_name"><?= $student->id ?></td>
        </tr>
        <tr>
            <th><?= __('Given name') ?></th>
            <td id="giv_name"><?= $student->id ?></td>
        </tr>
        <tr>
            <th><?= __('Major') ?></th>
            <td id="cohort_title"><?= $student->has('cohort') ? $this->Html->link($student->cohort->title, ['controller' => 'Cohorts', 'action' => 'view', $student->cohort->id]) : '' ?></td>
        </tr>
    </table>

</div>
