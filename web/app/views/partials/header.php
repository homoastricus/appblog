<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Простой блог на PHP">
    <title>Простой блог на PHP</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
          rel="stylesheet"/>
</head>
<body class="bg-white">
<div class="container">
    <?php require('nav.php');
    ?>
    <?php
    if (!isset($login) && isset($hide_login_form) && !$hide_login_form) { ?>
        <?php require('login_form.php'); ?>
    <?php } ?>
    <hr>
