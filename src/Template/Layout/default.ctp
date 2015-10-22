<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DunceMaster</title>

    <? // If you are using the CSS version, only link these 2 files, you may add app.css to use for your overrides if you like ?>
    <?= $this->Html->css('normalize.css'); ?>
    <?= $this->Html->css('foundation.css'); ?>
    <!--
    <script src="js/vendor/modernizr.js"></script> -->

</head>
<body>

    <?php if($currentUser) {
        $userMsg   = "current user = " . $currentUser;
        $loginLink = $this->Html->link(
            'Logout',
            '/users/logout',
            ['class' => 'button']
        );

    } else {
        $userMsg   = "not logged in";
        $loginLink = $this->Html->link(
            __('Login'),
            '/users/login',
            ['class' => 'button']
        );
    }
    ?>


    <nav class="top-bar" data-topbar role="navigation">
        <ul class="title-area">
            <li class="name">
                <h1><a href="#">DunceMaster</a></h1>
            </li>
        </ul>

        <section class="top-bar-section">
            <!-- Right Nav Section -->
            <ul class="right">
                <li><a href="#"><?= $userMsg ?></a></li>
                <li><a href="#"><?= $loginLink ?></a></li>
                <li><a href="#"><?= $this->Html->image("us_flag.gif", ['width' => 28, 'height' => 20, 'url' => ['controller' => 'I18n', 'action' => 'eng']]); ?></a></li>
                <li><a href="#"><?= $this->Html->image("chinese_flag.gif", ['width' => 28, 'height' => 20, 'url' => ['controller' => 'I18n', 'action' => 'chi']]); ?></a></li>
                <li><a href="#"><?= $this->Html->image("pinyin_flag.gif", ['width' => 28, 'height' => 20, 'url' => ['controller' => 'I18n', 'action' => 'pin']]); ?></a></li>

            </ul>

        </section>
    </nav>

    <?php if($currentUser) {
        echo $this->fetch('content');
    }?>

</body>
</html>
