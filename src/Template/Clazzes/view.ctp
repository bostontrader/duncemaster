<div id="ClazzesView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="sections view large-9 medium-8 columns content">
        <table id="ClazzViewTable" class="vertical-table">
            <tr id="section">
                <th><?= __('Section') ?></th>
                <td><?= $clazz->section->nickname ?></td>
            </tr>
            <tr id="week">
                <th><?= __('Week') ?></th>
                <td><?= $clazz->week ?></tr>
            </tr>
            <tr id="event_datetime">
                <th><?= __('Datetime') ?></th>
                <td><?= $clazz->event_datetime ?></tr>
            </tr>
        </table>
    </div>
</div>