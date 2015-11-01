<div id="sectionsIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Section'), ['action' => 'add'],['id'=>'sectionAdd']) ?></li>
        </ul>
    </nav>
    <div class="sections index large-9 medium-8 columns content">
        <h3><?= __('Sections') ?></h3>
        <table id="sectionsTable" cellpadding="0" cellspacing="0">
            <thead>
            <tr>
                <th id="clazz"><?= __('class') ?></th>
                <th id="student"><?= __('student') ?></th>
                <th id="actions" class="actions"><?= __('Actions') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($interactions as $interaction): ?>
                <tr>
                    <td><?= $interaction->class->nickname ?></td>
                    <td><?= $interaction->student->title ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $section->id],['name'=>'sectionView']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $section->id],['name'=>'sectionEdit']) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $section->id], ['name'=>'sectionDelete','confirm' => __('Are you sure you want to delete # {0}?', $section->id)]) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>