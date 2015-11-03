<div id="interactionsIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Interaction'), ['action' => 'add'],['id'=>'interactionAdd']) ?></li>
        </ul>
    </nav>
    <div class="interactions index large-9 medium-8 columns content">
        <h3><?= __('Interacctions') ?></h3>
        <table id="interactionsTable" cellpadding="0" cellspacing="0">
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
                    <td><?= $interaction->clazz->nickname ?></td>
                    <td><?= $interaction->student->fullname ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $interaction->id],['name'=>'interactionView']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $interaction->id],['name'=>'interactionEdit']) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $interaction->id], ['name'=>'interactionDelete','confirm' => __('Are you sure you want to delete # {0}?', $interaction->id)]) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>