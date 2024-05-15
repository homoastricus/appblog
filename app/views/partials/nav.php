<nav class="navbar navbar-expand-md">
    <div class="container-fluid">
        <button class="navbar-toggler ms-auto ms-md-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbar">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="/">Главная (статьи)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/users">Пользователи</a>
                </li>
                <?php if (!isset($login)) { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/register">Регистрация</a>
                    </li>
                <?php } else { ?>

                    <a class="nav-link" href="/user/logout">Выход</a> (<span class="text-muted">авторизованы как <b><?= $login ?></b></span>)
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>