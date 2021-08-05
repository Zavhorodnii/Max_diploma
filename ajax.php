<?php
require_once ('database_query.php');

if($_POST['action'] === 'register')
    register();
if($_POST['action'] === 'login')
    login();
if($_POST['action'] === 'logout')
    logout();
if($_POST['action'] === 'appraisals')
    appraisals();
if($_POST['action'] === 'next_stage')
    next_stage();
if($_POST['action'] === 'clear_app')
    clear_app();
if($_POST['action'] === 'save_passing')
    save_passing();

function generateCode($length=6) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
    $code = "";
    $clen = strlen($chars) - 1;
    while (strlen($code) < $length) {
        $code .= $chars[mt_rand(0,$clen)];
    }
    return $code;
}

function register(){

    if(!isset($_POST['full_name']) || !isset($_POST['password']) || !isset($_POST['student_id']) || !isset($_POST['group'])) {
        echo json_encode(array(
            'result' => 'not found some info',
        ));
        die();
    }

    $full_name = $_POST['full_name'];
    $password = $_POST['password'];
    $student_id = $_POST['student_id'];
    $group = $_POST['group'];

    if($group === 'administrator') {
        echo json_encode(array(
            'result'    => 'group not found',
        ));
        die();
    }

    $res = select_group($group);
    $row = $res->fetch_assoc();
    $group_id = $row['id'];

    if($group_id === null) {
        echo json_encode(array(
            'result'    => 'group not found',
        ));
        die();
    }

    $res = check_student($student_id);

    if($res->fetch_assoc() !== null) {
        echo json_encode(array(
            'result'    => 'student exist',
        ));
        die();
    }


    $res = add_student($full_name, $password, $student_id, $group_id);

    if(!$res) {
        echo json_encode(array(
            'result' => 'error add user' . $res,
        ));
        die();
    }
//    while ($row = $res->fetch_assoc()) {
//        echo " id = " . $row['id'] . "\n";
//    }

    $result = array(
        'result'    => 'ok',
    );

//    header("Location: index.html");;

    echo json_encode($result);
    die();
}

function login(){
    if(!isset($_POST['full_name']) || !isset($_POST['password']) || !isset($_POST['student_id'])) {
        echo json_encode(array(
            'result' => 'not found some info',
        ));
        die();
    }
    $full_name = $_POST['full_name'];
    $password = $_POST['password'];
    $student_id = $_POST['student_id'];

    $res = db_log_in($full_name, $password, $student_id);

    if($res->num_rows < 1) {
        echo json_encode(array(
            'result' => 'error login',
        ));
        die();
    }

    $row = $res->fetch_assoc();

    $start = check_block_student($row['id']);
    if($start->fetch_assoc()['vote'] === '0') {
        echo json_encode(array(
            'result'    => 'error vote',
        ));
        die();
    }

    $hash = md5(generateCode(10));

    db_update_hash($student_id, $hash);

    setcookie("id", $row['id'], time()+60*60*24*30, "/");
    setcookie("hash", $hash, time()+60*60*24*30, "/", null, null, true);
    $log = 'ok';
    if($row['id'] == '1')
        $log = 'admin';

    $result = array(
        'result'    => $log,
    );
    echo json_encode($result);
    die();
}

function logout(){
    setcookie("id", "", time() - 3600*24*30*12, "/");
    setcookie("hash", "", time() - 3600*24*30*12, "/",null,null,true); // httponly !!!
    echo json_encode( array(
        'result'    => 'ok',
    ));
    die();
}

function appraisals(){
    if(!$_POST['appraisals']){
        echo json_encode(array(
            'result' => 'error appraisals',
        ));
        die();
    }

    $res = check_block_student($_COOKIE['id']);

    if($res->fetch_assoc()['vote'] === '0') {
        setcookie("id", "", time() - 3600*24*30*12, "/");
        setcookie("hash", "", time() - 3600*24*30*12, "/",null,null,true); // httponly !!!
        echo json_encode(array(
            'result'    => 'error vote',
        ));
        die();
    }


    $appraisals = $_POST['appraisals'];
    $json_app = json_decode($appraisals, true);

    foreach($json_app as $key => $val) {
        $res = update_student_select($key, $val);
        if($res !== 'ok') {
            echo json_encode(array(
                'result' => $res,
            ));
            die();
        }
    }

    $res = block_student($_COOKIE['id']);

    if(!$res) {
        echo json_encode(array(
            'result'    => 'error block',
        ));
        die();
    }

    setcookie("id", "", time() - 3600*24*30*12, "/");
    setcookie("hash", "", time() - 3600*24*30*12, "/",null,null,true); // httponly !!!

    echo json_encode(array(
        'result' => 'ok',
    ));

    die();
}

function next_stage(){
    if(!$_POST['group']) {
        echo json_encode(array(
            'result' => 'error appraisals',
        ));
        die();
    }
    if(!allow_vote($_POST['group'])){
        echo json_encode(array(
            'result' => 'error vote',
        ));
        die();
    }
    echo json_encode(array(
        'result' => 'ok',
    ));
    die();
}

function clear_app(){
    if(!$_POST['group']) {
        echo json_encode(array(
            'result' => 'error appraisals',
        ));
        die();
    }
    if(!clear_app_student($_POST['group'])){
        echo json_encode(array(
            'result' => 'error vote',
        ));
        die();
    }
    echo json_encode(array(
        'result' => 'ok',
    ));
    die();
}

function save_passing(){
    if(!$_POST['group'] || $_POST['save'] == null) {
        echo json_encode(array(
            'result' => 'error same info',
        ));
        die();
    }
    if(intval($_POST['save']) < 0 || intval($_POST['save']) > 100){
        echo json_encode(array(
            'result' => 'error same info',
        ));
        die();
    }

    if(!save_passing_group($_POST['group'], $_POST['save'])){
        echo json_encode(array(
            'result' => 'error update',
        ));
        die();
    }
    echo json_encode(array(
        'result' => 'ok',
    ));
    die();
}