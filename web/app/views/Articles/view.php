<?php require(VIEW_DIR . '/partials/header.php'); ?>
<h5 class="text-muted">Просмотр статьи <?php echo $article[0]->name ?> </h5>

<div class="row">
    <div class="col-lg-9 justify-content-between d-flex">
        <?php echo $article[0]->text; ?>
    </div>
    <div class="col-lg-3">
        <div class="mt-2 mb-2 text-muted">создана <?php echo $article[0]->created ?></div>
        <button class="btn-sm like mt-2 mb-2
        <?php if($isLiked){
            echo 'text-danger';
        } else {
            echo 'text-muted';
        }?>
        " data-id="<?php echo $article[0]->id ?>"><i class="fa fa-thumbs-up fa-2x"></i></button>
    </div>
</div>


<?php require(VIEW_DIR . '/partials/footer.php'); ?>
