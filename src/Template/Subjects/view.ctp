<?php  /* @var \App\Model\Entity\Subject $subject */ ?>
<div id="SubjectsView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="subjects view large-9 medium-8 columns content">
        <h3><?= h($subject->id) ?></h3>
        <table id="SubjectViewTable" class="vertical-table">
            <tr id="title">
                <th><?= __('Title') ?></th>
                <td><?= $subject->title ?></td>
            </tr>
        </table>
    </div>
</div>
