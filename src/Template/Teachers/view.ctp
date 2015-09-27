<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Teacher'), ['action' => 'edit', $teacher->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Teacher'), ['action' => 'delete', $teacher->id], ['confirm' => __('Are you sure you want to delete # {0}?', $teacher->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Teachers'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Teacher'), ['action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="teachers view large-9 medium-8 columns content">
    <h3><?= h($teacher->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($teacher->id) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Giv Name') ?></h4>
        <?= $this->Text->autoParagraph(h($teacher->giv_name)); ?>
    </div>
</div>
