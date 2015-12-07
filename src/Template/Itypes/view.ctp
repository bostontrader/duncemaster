<?php
/**
 * @var \App\Model\Entity\Itype $itypes
 */
?>
<div id="ItypesView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="itypes view large-9 medium-8 columns content">
        <h3><?= h($itype->id) ?></h3>
        <table id="ItypeViewTable" class="vertical-table">
            <tr id="title">
                <th><?= __('Title') ?></th>
                <td><?= $itype->title ?></td>
            </tr>
        </table>
    </div>
</div>
