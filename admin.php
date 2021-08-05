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
    <title>Diplom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
<div class="adm container">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" href="#">Groups</a>
        </li>
    </ul>
    <div class="nav-content active">
        <div class="container">
            <div class="row group-list js_clear_info">
                <?php
                require_once 'database_query.php';
                $all_info = select_all_info();
//                var_export($all_info);
                foreach ($all_info as $key => $value) {
                    ?>

                    <div class="col-12 vot-box my-2 js_find_info" data-group="<?php echo $key ?>">
                        <div class="vot-control">
                            <button type="submit" class="next-stage js_next_stage">Наступний етап</button>
                            <button type="submit" class="refresh js_clear_app">Обнулити</button>
                        </div>
                        <input type="text" class="adm-input js_paste_passing" name="vote" value="<?php echo $value['hide'] ?>" maxlength="3">
                        <label for="vote">% Прохождення</label>
                        <button type="submit" class="refresh js_save_passing">Зберегти</button>
                        <div class="vot-push js_info_message"></div>
                    </div>
                    <div class="col-12 group-item">
                        <?php echo $key ?>
                    </div>
                    <div class="group-result">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Предмет</th>
                                <th scope="col">Бали</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $i = 1;
                                foreach ($value['all_subjects'] as $key2 => $value2) {
                                    ?>
                                    <tr class="winner">
                                        <th scope="row"><?php echo $i ?></th>
                                        <td><?php echo $key2 ?></td>
                                        <td class="ball" data-clear-app="<?php echo $key ?>"><?php echo $value2 ?></td>
                                    </tr>
                                    <?
                                    $i++;
                                }
                            ?>
<!--                            <tr class="second">-->
<!--                                <th scope="row">2</th>-->
<!--                                <td>Subject 2</td>-->
<!--                                <td class="ball">9</td>-->
<!--                            </tr>-->
<!--                            <tr class="loser">-->
<!--                                <th scope="row">3</th>-->
<!--                                <td>Subject 3</td>-->
<!--                                <td class="ball">8</td>-->
<!--                            </tr>-->
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.6.0/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js"></script>
<script src="common.js"></script>
</body>
</html>