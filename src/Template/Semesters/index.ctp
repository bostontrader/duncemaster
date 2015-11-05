<div id="SemestersIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Semester'), ['action' => 'add'],['id'=>'SemesterAdd']) ?></li>
        </ul>
    </nav>
    <div class="semesters index large-9 medium-8 columns content">
        <h3><?= __('Semesters') ?></h3>
        <table id="SemestersTable" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th id="year"><?= __('year') ?></th>
                    <th id="seq"><?= __('seq') ?></th>
                    <th id="firstday"><?= __('firstday') ?></th>
                    <th id="actions" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($semesters as $semester): ?>
                <tr>
                    <td><?= $semester->year ?></td>
                    <td><?= $semester->seq ?></td>
                    <td><?= $semester->firstday ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $semester->id],['name'=>'SemesterView']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $semester->id],['name'=>'SemesterEdit']) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $semester->id], ['name'=>'SemesterDelete','confirm' => __('Are you sure you want to delete # {0}?', $semester->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
