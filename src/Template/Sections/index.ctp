<?php /* @var \Cake\ORM\Query $sections */ ?>
<div id="SectionsIndex">
    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>
            <li><?= $this->Html->link(__('New Section'), ['action' => 'add'],['id'=>'SectionAdd']) ?></li>
        </ul>
    </nav>
    <div class="sections index large-9 medium-8 columns content">
        <h3><?= __('Sections') ?></h3>
        <table id="SectionsTable" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th id="semester"><?= __('semester') ?></th>
                    <th id="seq"><?= __('seq') ?></th>
                    <th id="cohort"><?= __('cohort') ?></th>
                    <th id="subject"><?= __('subject') ?></th>
                    <th id="tplan"><?= __('tplan') ?></th>
                    <th id="weekday"><?= __('weekday') ?></th>
                    <th id="start_time"><?= __('start_time') ?></th>
                    <th id="thours"><?= __('T Hours') ?></th>
                    <th id="actions" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sections as $section): ?>
                <tr>
                    <td><?= $section->semester->nickname ?></td>
                    <td><?= $section->seq ?></td>
                    <td><?= $section->cohort->nickname ?></td>
                    <td><?= $section->subject->title ?></td>
                    <td><?= $section->tplan->title ?></td>
                    <td><?= $section->weekday ?></td>
                    <td><?= $section->start_time ?></td>
                    <td><?= $section->thours ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('Classes'), ['controller' => 'clazzes', 'action' => 'index', 'section_id' => $section['id']],['name'=>'SectionClazzes']) ?>
                        <?= $this->Html->link(__('View'), ['action' => 'view', $section->id],['name'=>'SectionView']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $section->id],['name'=>'SectionEdit']) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $section->id], ['name'=>'SectionDelete','confirm' => __('Are you sure you want to delete # {0}?', $section->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>