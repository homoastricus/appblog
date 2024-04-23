<?php require('partials/header.php'); ?>
<h1 class="<?php echo theme('text-white-30', 'text-dark') ?>">Добавить статью:</h1>
<div class="form-group">
	<form method="POST" action="/articles">
        <input class="form-control <?php echo theme('bg-dark text-white-75','bg-white') ?>" name="name"/>
	    <button class="form-control <?php echo theme('bg-dark text-white-75','bg-white') ?>" type="submit">Добавить</button>
	</form>
</div>
<ul>
    <?php foreach ($articles as $article) : ?>
        <li class="<?php echo theme('text-white-75', 'text-dark') ?>"><?php echo $article->name; ?> -
            <a href="/user/<?php echo $article->id ?>" class="<?php echo theme('text-light', 'text-primary') ?>">U</a> - <a href="/user/delete/<?php echo $article->id ?>?page=<?php echo $page; ?>" class="<?php echo theme('text-light', 'text-primary') ?>">X</a></li>
    <?php endforeach; ?>
</ul>

<?php echo paginate('articles', $page, $limit, $count); ?>

<?php require ('partials/footer.php'); ?>
