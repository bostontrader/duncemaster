<?php /* @var \App\Model\Entity\Tplan $tplan */ ?>
<div id="TplansView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="tplans view large-9 medium-8 columns content">
        <h3><?= h($tplan->id) ?></h3>
        <table id="TplanViewTable" class="vertical-table">
            <tr id="title">
                <th><?= __('Title') ?></th>
                <td><?= $tplan->title ?></td>
            </tr>
        </table>
    </div>

    <?= $this->element('tplan_elements_index') ?>

</div>
