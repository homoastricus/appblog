<?php require ('partials/header.php'); ?>
<h1 class="text-dark">Упс... ошибка! :(</h1>
<p class="text-dark">Вы столкнулись с ошибкой работы приложения. Можно вернуться к <a href="/">начальной странице</a>.</p>
<?php if (isset($error)): ?>
    <p class="text-danger"><?php echo $error; ?></p>
<?php endif; ?>
<?php require ('partials/footer.php'); ?>
