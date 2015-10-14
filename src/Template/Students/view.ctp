<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
    </ul>
</nav>
<div class="students view large-9 medium-8 columns content">
    <h3><?= h($student->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($student->id) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Sid') ?></h4>
        <?= $this->Text->autoParagraph(h($student->sid)); ?>
    </div>
    <div class="row">
        <h4><?= __('Fam Name') ?></h4>
        <?= $this->Text->autoParagraph(h($student->fam_name)); ?>
    </div>
    <div class="row">
        <h4><?= __('Giv Name') ?></h4>
        <?= $this->Text->autoParagraph(h($student->giv_name)); ?>
    </div>
</div>
