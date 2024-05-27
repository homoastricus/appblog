<?php require(VIEW_DIR . '/partials/header.php');

echo "данные взяты из " . $source;?>
<ul>
    <?php foreach ($articles as $article) : ?>
        <li class="text-dark">
        <a href="/article/<?php echo $article->id ?>"><?php echo $article->name; ?></a> -
            создана пользователем <a href="/user/<?php echo $article->owner_id ?>" class="text-primary">
                <?php echo $user_ids[$article->owner_id] ?></a>
            <?php if ($currentUserId == $article->owner_id){?>
            -  <a href="/article/delete/<?php echo $article->id ?>?page=<?php echo $page; ?>"
               class="text-primary">удалить</a></li> <?php }?>
    <?php endforeach; ?>
</ul>

<?php echo paginate('articles', $page, $limit, $count); ?>

<?php
if(isset($login)){?>

<div class="row">
    <div class="col-3"></div>
    <div class="col-6">
        <h4 class="text-dark">Новая статья</h4>
        <div class="form-group">
            <form method="post" action="/article/create" enctype="multipart/form-data">
                <div class="mb-3">
                    <input minlength="3" maxlength="255" required="required" id="article_name" class="form-control"
                           name="name" placeholder="название статьи"/>
                </div>
                <div class="mb-3">
                    <textarea minlength="3" required="required" id="article_text" class="form-control"
                              name="text" placeholder="текст статьи"></textarea>

                </div>
                <div class="mb-3">
                    <input class="form-control" name="image" type="file" value=""/>

                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Добавить</button>
                </div>

            </form>
        </div>
    </div>
    <div class="col-3"></div>
</div>
<?php }?>
<?php require(VIEW_DIR . '/partials/footer.php'); ?>