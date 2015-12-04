<?php  /* @var \App\Model\Entity\Major $major */ ?>

<div id="MajorsView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="majors view large-9 medium-8 columns content">
        <h3><?= h($major->id) ?></h3>
        <table id="MajorViewTable" class="vertical-table">
            <tr id="title">
                <th><?= __('Title') ?></th>
                <td><?= $major->title ?></td>
            </tr>
            <tr id="sdesc">
                <th><?= __('SDesc') ?></th>
                <td><?= $major->sdesc ?></td>
            </tr>
        </table>
    </div>
</div>
