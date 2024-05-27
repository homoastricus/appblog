<?php require('partials/header.php'); ?>

<h1 class="text-dark">Обновить статью <b><?php echo $article[0]->name ?></b>:</h1>

<div class="row">
    <div class="col-3"></div>
    <div class="col-6">
        <h4 class="text-dark">Новая статья</h4>
        <div class="form-group">
            <form method="POST" action="/article/update/<?php echo $article[0]->id ?>">
                <div class="mb-3">
                    <input minlength="3" maxlength="255" required="required" id="article_name" class="form-control"
                           name="name" placeholder="название статьи" value="<?php echo $article[0]->name ?>"/>
                </div>
                <div class="mb-3">
                    <textarea minlength="3" required="required" id="article_text" class="form-control"
                              name="text" placeholder="текст статьи"><?php echo $article[0]->text ?></textarea>

                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>

            </form>
        </div>
    </div>
    <div class="col-3"></div>
</div>

<?php require('partials/footer.php'); ?>