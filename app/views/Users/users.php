<?php require(VIEW_DIR . '/partials/header.php'); ?>
<h1 class="text-dark">Список пользователей</h1>
<ul>
    <?php foreach ($users as $user) : ?>
        <li class="text-dark"><?php echo $user->name; ?></li>
    <?php endforeach; ?>
</ul>

<?php echo paginate('users', $page, $limit, $count); ?>

<?php require(VIEW_DIR . '/partials/footer.php'); ?>
