<!DOCTYPE html>
<html>
<head></head>
<body>
    <header>
        <div class="header-image">
        </div>
    </header>

    <nav class="large-3 medium-4 columns" id="actions-sidebar">
        <ul class="side-nav">
            <li class="heading"><?= __('Actions') ?></li>

                <?php if(is_null($currentUser)) { ?>
                <?php } else if($isAdmin) { ?>
                    <li><?= $this->Html->link(__('Classes'),       ['controller' => 'Clazzes'])       ?></li>
                    <li><?= $this->Html->link(__('Cohorts'),       ['controller' => 'Cohorts'])       ?></li>
                    <li><?= $this->Html->link(__('Interactions'),  ['controller' => 'Interactions'])  ?></li>
                    <li><?= $this->Html->link(__('Itypes'),        ['controller' => 'Itypes'])        ?></li>
                    <li><?= $this->Html->link(__('Majors'),        ['controller' => 'Majors'])        ?></li>
                    <li><?= $this->Html->link(__('Roles'),         ['controller' => 'Roles'])         ?></li>
                    <li><?= $this->Html->link(__('Sections'),      ['controller' => 'Sections'])      ?></li>
                    <li><?= $this->Html->link(__('Semesters'),     ['controller' => 'Semesters'])     ?></li>
                    <li><?= $this->Html->link(__('Students'),      ['controller' => 'Students'])      ?></li>
                    <li><?= $this->Html->link(__('Subjects'),      ['controller' => 'Subjects'])      ?></li>
                    <li><?= $this->Html->link(__('Teachers'),      ['controller' => 'Teachers'])      ?></li>
                    <li><?= $this->Html->link(__('Tplans'),        ['controller' => 'Tplans'])        ?></li>
                    <li><?= $this->Html->link(__('TplanElements'), ['controller' => 'TplanElements']) ?></li>
                    <li><?= $this->Html->link(__('Users'),         ['controller' => 'Users'])         ?></li>
                <?php } else if($isTeacher) {?>
                    <li><?= $this->Html->link(__('Sections'),      ['controller' => 'Sections'])      ?></li>
                <?php } ?>
        </ul>
    </nav>

    <footer>
    </footer>
</body>
</html>
