<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
    </ul>
</nav>
<div class="cohorts view large-9 medium-8 columns content">
    <h3><?= h($cohort->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Major') ?></th>
            <td id="major_title"><?= $cohort->has('major') ? $this->Html->link($cohort->major->title, ['controller' => 'Majors', 'action' => 'view', $cohort->major->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td id="id"><?= $cohort->id ?></td>
        </tr>
        <tr>
            <th><?= __('Start Year') ?></th>
            <td id="start_year"><?= $cohort->start_year ?></td>
        </tr>
        <tr>
            <th><?= __('Seq') ?></th>
            <td id="seq"><?= $cohort->seq ?></td>
        </tr>

        <tr>
            <th><?= __('Nickname') ?></th>
            <td id="nickname"><?= $cohort->nickname ?></td>
        </tr>
    </table>

    <div class="related">
        <h4><?= __('Related Sections') ?></h4>
        <?php if (!empty($cohort->sections)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Cohort Id') ?></th>
                <th><?= __('Subject Id') ?></th>
                <th><?= __('Weekday') ?></th>
                <th><?= __('Time') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($cohort->sections as $sections): ?>
            <tr>
                <td><?= h($sections->id) ?></td>
                <td><?= h($sections->cohort_id) ?></td>
                <td><?= h($sections->subject_id) ?></td>
                <td><?= h($sections->weekday) ?></td>
                <td><?= h($sections->time) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Sections', 'action' => 'view', $sections->id]) ?>

                    <?= $this->Html->link(__('Edit'), ['controller' => 'Sections', 'action' => 'edit', $sections->id]) ?>

                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Sections', 'action' => 'delete', $sections->id], ['confirm' => __('Are you sure you want to delete # {0}?', $sections->id)]) ?>

                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>

    <div class="related">
        <h4><?= __('Related Students') ?></h4>
        <?php if (!empty($cohort->students)): ?>
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <th><?= __('id') ?></th>
                    <th><?= __('Student ID') ?></th>
                    <th><?= __('Name') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
                <?php foreach ($cohort->students as $student): ?>
                    <tr>
                        <td><?= $student->id ?></td>
                        <td><?= $student->sid ?></td>
                        <td><?= $student->fam_name . $student->giv_name ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('View'), ['controller' => 'Sections', 'action' => 'view', $sections->id]) ?>

                            <?= $this->Html->link(__('Edit'), ['controller' => 'Sections', 'action' => 'edit', $sections->id]) ?>

                            <?= $this->Form->postLink(__('Delete'), ['controller' => 'Sections', 'action' => 'delete', $sections->id], ['confirm' => __('Are you sure you want to delete # {0}?', $sections->id)]) ?>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>

</div>
