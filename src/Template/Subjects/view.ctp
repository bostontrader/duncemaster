<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
    </ul>
</nav>
<div class="subjects view large-9 medium-8 columns content">
    <h3><?= h($subject->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Id') ?></th>
            <td id="id"><?= $subject->id ?></td>
        </tr>
        <th>
            <?= __('Title') ?></th>
            <td id="title"><?= $subject->title ?></td>
        </tr>
    </table>

<!--
    <div class="related">
        <h4><?= __('Related Sections') ?></h4>
        <?php if (!empty($subject->sections)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Cohort Id') ?></th>
                <th><?= __('Subject Id') ?></th>
                <th><?= __('Weekday') ?></th>
                <th><?= __('Time') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($subject->sections as $sections): ?>
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
    -->
</div>
