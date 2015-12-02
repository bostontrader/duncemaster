<div id="TplanElementsView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="tplan_elements view large-9 medium-8 columns content">
        <h3><?= h($tplan_element->id) ?></h3>
        <table id="TplanElementViewTable" class="vertical-table">
            <tr id="tplan_title">
                <th><?= __('Tplan') ?></th>
                <td><?= $tplan_element->tplan->title ?></td>
            </tr>
            <tr id="col1">
                <th><?= __('Col1') ?></th>
                <td><?= $tplan_element->col1 ?></td>
            </tr>
            <tr id="col2">
                <th><?= __('Col2') ?></th>
                <td><?= $tplan_element->col2 ?></td>
            </tr>
        </table>
    </div>
</div>
