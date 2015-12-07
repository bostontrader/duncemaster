<?php
/**
 * @var \App\Model\Entity\Clazz $clazz
 * @var \App\Model\Table\SectionsTable $sections
 */
?><div id="ClazzesEdit">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
        </ul>
    </nav>
    <div class="clazzes form large-9 medium-8 columns content">
        <?= $this->Form->create($clazz, ['id'=>'ClazzEditForm']) ?>
        <fieldset>
            <legend><?= __('Edit Class') ?></legend>
            <?php
                echo $this->Form->input('section_id', ['id'=>'ClazzSectionId', 'options' => $sections, 'empty' => '(none selected)']);
                echo $this->Form->input('event_datetime', ['id'=>'ClazzDatetime','type'=>'text']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>