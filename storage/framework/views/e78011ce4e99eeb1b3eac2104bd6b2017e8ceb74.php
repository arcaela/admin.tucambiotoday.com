<?php $__env->startSection('html'); ?>
    <html>
<?php echo $__env->yieldSection(); ?>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/css/app.css">
        <title>TuCambioToday</title>
        <?php echo $__env->yieldPushContent('css-lib'); ?>
        <?php echo $__env->yieldPushContent('head'); ?>
    </head>
    <body>
        <?php echo $__env->yieldPushContent('top'); ?>
        <?php echo $__env->yieldPushContent('body'); ?>
        <?php echo $__env->yieldPushContent('footer'); ?>
        <script type="text/javascript" src="/js/app.js"></script>
        <?php echo $__env->yieldPushContent('js-lib'); ?>
        <?php echo $__env->yieldPushContent('script'); ?>
    </body>
</html><?php /**PATH /homepages/31/d750175303/htdocs/admin/resources/views/base/basic.blade.php ENDPATH**/ ?>