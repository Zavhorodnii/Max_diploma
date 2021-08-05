<?php
if (isset($_COOKIE['id']) and isset($_COOKIE['hash']))
{
    require_once ('database_query.php');
    $query = cookie_check($_COOKIE['id']);
    $row = $query->fetch_assoc();

    if($row['hash'] !== $_COOKIE['hash'])
    {
        setcookie("id", "", time() - 3600*24*30*12, "/");
        setcookie("hash", "", time() - 3600*24*30*12, "/", null, null, true); // httponly !!!
        header("Location: /"); exit();
    }
}
else
{
    header("Location: /"); exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Проведення тестування</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="header">
    <div class="container d-flex align-items-center">
        <div>
            ЧНУ-ФКН-Кафедра ІІС-ОП 122 Комп'ютерні науки (Бакалавр) - Система вибору студентами освітніх компонентів на основі групового ранжування
        </div>
        <button type="submit" class="btn-logout js_logout">Вихід</button>
    </div>
</div>
<div class="container title-stage">
    <h1><?= "Добрий день, ".$row['full_name']."!" ?></h1>
    <div class="content">Будь ласка, оберіть предмети зі списка від найбажанішого предмета до гіршого</div>
</div>

<div class="container subject">
    <?php
    $all_subj = select_all_subject($_COOKIE['id']);

    foreach ($all_subj as $subj){
        ?>
        <div class="row subject-line" data-id_groups_subjects="<?php echo $subj[0] ?>">
            <div class="col-10 subject-item"><?php echo $subj[1] ?></div>
            <div class="col-2 subject-item ball"></div>
        </div>
        <?
    }
    ?>
    <span class="error_message"></span>
    <button class="reg-btn btn-submit btn-stage d-block mx-auto mt-3 js_appraisals">Відправити результати</button>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.6.0/gsap.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="common.js"></script>
</body>
</html>