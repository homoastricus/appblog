<?php require(VIEW_DIR .'/partials/header.php'); ?>

<hr>
<div class="form-group">

    <form method="POST" action="/user/create" class="">
        <div class="row">

            <div class="col-md-3 col-sm-6 col-sm-12 mb-2">
                <h6 class="text-muted">Регистрация</h6>
            </div>

            <div class="col-md-3 col-sm-6 col-sm-12 mb-2">
                <input minlength="3" maxlength="255" required="required" id="user_name" class="form-control"
                       name="name" placeholder="ваше имя" value=""/>
            </div>
            <div class="col-md-3 col-sm-6 col-sm-12 mb-2">
                <input type="password" minlength="6" maxlength="255" required="required" id="password"
                       class="form-control"
                       name="password" placeholder="ваш пароль" value=""/>
            </div>
            <div class="col-md-3 col-sm-6 col-sm-12 mb-2">
                <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
            </div>
        </div>
    </form>
</div>

<?php require(VIEW_DIR .'/partials/footer.php'); ?>
