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
        echo "current user = " . $currentUser;
        echo $this->Html->link(
            'Logout',
            '/users/logout',
            ['class' => 'button']
        );
        echo $this->fetch('content');
    } else {
        echo "not logged in";
        echo $this->Html->link(
            'Login',
            '/users/login',
            ['class' => 'button']
        );
    };
    ?>

</body>
</html>
