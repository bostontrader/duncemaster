<div id="InteractionsView">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
        </ul>
    </nav>
    <div class="interactions view large-9 medium-8 columns content">
        <table id="InteractionViewTable" class="vertical-table">
            <tr id="clazz">
                <th><?= __('Class') ?></th>
                <td><?= $interaction->clazz->nickname ?></td>
            </tr>
            <tr id="student">
                <th><?= __('Student') ?></th>
                <td><?= $interaction->student->fullname ?></td>
            </tr>
        </table>
    </div>
</div>
