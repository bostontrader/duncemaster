<div id="SubjectsIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Subject'), ['action' => 'add'],['id'=>'SubjectAdd']) ?></li>
        </ul>
    </nav>
    <div class="subjects index large-9 medium-8 columns content">
        <h3><?= __('Subjects') ?></h3>
        <table id="SubjectsTable" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th id="title" ><?= __('Title') ?></th>
                    <th id="actions" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjects as $subject): ?>
                <tr>
                    <td><?= $subject->title ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $subject->id],['name'=>'SubjectView']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $subject->id],['name'=>'SubjectEdit']) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $subject->id], ['name'=>'SubjectDelete','confirm' => __('Are you sure you want to delete # {0}?', $subject->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
