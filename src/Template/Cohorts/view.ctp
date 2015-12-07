<?php
/**
 * @var \App\Model\Entity\Cohort $cohort
 */
?>
<div id="CohortsView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="cohorts view large-9 medium-8 columns content">
        <h3><?= h($cohort->id) ?></h3>
        <table id="CohortViewTable" class="vertical-table">
            <tr id="major_title">
                <th><?= __('Major') ?></th>
                <td><?= $cohort->major->title ?></td>
            </tr>
            <tr id="start_year">
                <th><?= __('Start Year') ?></th>
                <td><?= $cohort->start_year ?></td>
            </tr>
            <tr id="seq">
                <th><?= __('Seq') ?></th>
                <td><?= $cohort->seq ?></td>
            </tr>
            <tr id="nickname">
                <th><?= __('Nickname') ?></th>
                <td><?= $cohort->nickname ?></td>
            </tr>
        </table>
    </div>
</div>
