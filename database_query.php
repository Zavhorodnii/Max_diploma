<?php

$connection_db = null;

function connect_to_db(){
    global $connection_db;
    $connection_db = mysqli_connect('localhost', 'root', 'root');
    if($connection_db){
        return 'connection success';
    } else{
        return 'error ' . mysqli_connect_error();
    }
}

function create_db(){
    global $connection_db;
    $sql = 'CREATE DATABASE IF NOT EXISTS statistics';
    $result = mysqli_query($connection_db, $sql);
    if($result) {
        $connection_db = new mysqli('localhost', 'root', 'root', 'statistics');
        return 'create success';
    }
    else
        return 'error';
}

function db_create_table(){
    global $connection_db;

    $sql = 'CREATE TABLE IF NOT EXISTS `groups` (
        `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `group` VARCHAR (20) NOT NULL,
        `hide` INT (3) NOT NULL DEFAULT 0
    )';
    $result = $connection_db->query($sql);
    if(!$result)
        return 'error create groups table';

    $sql = 'CREATE TABLE IF NOT EXISTS `students` ( 
        `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `full_name` VARCHAR(70) NOT NULL,
        `group` INT NOT NULL,
        `hash` VARCHAR(32) NOT NULL DEFAULT "",
        `password` VARCHAR(20) NOT NULL,
        `student_id` VARCHAR(40) NOT NULL UNIQUE,
        FOREIGN KEY (`group`)  REFERENCES `groups` (`Id`),
        `vote` INT (1) NOT NULL DEFAULT 0
    )';
    $result = $connection_db->query($sql);

    $sql = 'INSERT groups(`group`) 
            VALUES (\'administrator\')';
    $connection_db->query($sql);
    echo $connection_db->error;
    $sql = 'INSERT students(`full_name`, `password`, `student_id`, `group`, `hash`, `vote`) 
            VALUES (\'administrator\', \'55555\', \'administrator\', \'1\', \'' . md5(md5(trim('administrator'))). '\', 1)';
    $connection_db->query($sql);
    echo $connection_db->error;

    if(!$result)
        return 'error create student table ' . $connection_db->error;;

    $sql = 'CREATE TABLE IF NOT EXISTS `subjects` (
        `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `subject` VARCHAR (100) NOT NULL
    )';
    $result = $connection_db->query($sql);
    if(!$result)
        return 'error create course subject';

    $sql = 'CREATE TABLE IF NOT EXISTS `groups_subjects` (
        `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `group_id` INT NOT NULL,
        `subject_id` INT NOT NULL,
        `appraisal` INT NOT NULL DEFAULT 0,
        FOREIGN KEY (`group_id`)  REFERENCES `groups` (`Id`),
        FOREIGN KEY (`subject_id`)  REFERENCES `subjects` (`Id`)
    )';
    $result = $connection_db->query($sql);
    if(!$result)
        return 'error create groups_subjects ' . $result->error;
    return 'success create all table';
}

function select_group($group){
    $connection_db = new mysqli('localhost', 'root', 'root', 'statistics');
    $sql = 'SELECT id FROM `groups` 
            WHERE `group` LIKE \'' . $group . '\'';
    $result = $connection_db->query($sql);
    if(!$result)
        return 'errors ' . $result->error;
    return $result;
}

function check_student($student_id){
    $connection_db = new mysqli('localhost', 'root', 'root', 'statistics');
    $sql = 'SELECT id FROM `students` 
            WHERE `student_id` LIKE \'' . $student_id . '\'';
    return $connection_db->query($sql);
}

function add_student($full_name, $password, $student_id, $group){

    $hash = md5(md5(trim($password)));

    $connection_db = new mysqli('localhost', 'root', 'root', 'statistics');
    $sql = 'INSERT students(`full_name`, `password`, `student_id`, `group`, `hash`) 
            VALUES (\'' . $full_name . '\', \'' . $password . '\', \'' . $student_id . '\', \'' . $group. '\', \'' . $hash. '\')';
    return $connection_db->query($sql);
}

function db_log_in($full_name, $password, $student_id){
    $connection_db = new mysqli('localhost', 'root', 'root', 'statistics');
    $sql = 'SELECT `id` FROM `students` 
            WHERE `full_name` LIKE \'' . $full_name . '\' AND `password` LIKE \'' . $password . '\' AND `student_id` LIKE \'' . $student_id . '\'';
    return $connection_db->query($sql);
}

function db_update_hash($student_id, $hash){
    $connection_db = new mysqli('localhost', 'root', 'root', 'statistics');
    $sql = 'UPDATE `students` 
            SET `hash`=\'' . $hash . '\' WHERE `student_id` LIKE \'' . $student_id . '\'';
    return $connection_db->query($sql);
}

function cookie_check($id){
    $connection_db = new mysqli('localhost', 'root', 'root', 'statistics');
    $sql = 'SELECT * FROM `students` 
            WHERE `id` LIKE \'' . $id . '\'';
    return $connection_db->query($sql);
}

function select_all_subject($student_id){
    $connection_db = new mysqli('localhost', 'root', 'root', 'statistics');
    $sql = 'SELECT `group` FROM `students` 
            WHERE `id` LIKE \'' . $student_id . '\'';

    $subj = $connection_db->query($sql);
    $row = $subj->fetch_assoc();

    $sql = 'SELECT `hide` FROM `groups`
            WHERE `id` LIKE \'' . $row['group'] . '\'';

    $groups = $connection_db->query($sql);
    $groups_hide = $groups->fetch_assoc();

    $sql = 'SELECT `subject_id`, `id` FROM `groups_subjects` 
            WHERE `group_id` LIKE \'' . $row['group'] . '\' ORDER BY `appraisal` DESC';

    $res = $connection_db->query($sql);

    $all_subjects = array();
    $all_rows = $res->num_rows;

    $skip = ceil($all_rows * intval($groups_hide['hide']) / 100);
    $skip = $all_rows - $skip;

    while ($row = $res->fetch_assoc()) {
        if($skip == 0)
            break;
        $id_subj = array();
        $sql = 'SELECT `subject` FROM `subjects` 
            WHERE `id` LIKE \'' . $row['subject_id'] . '\'';

        $result = $connection_db->query($sql);
        $row_ = $result->fetch_assoc();
        $id_subj[] = $row['id'];
        $id_subj[] = $row_['subject'];
        $all_subjects[] = $id_subj;
        $skip--;
    }
    return $all_subjects;
}

function check_block_student($student_id){
    $connection_db = new mysqli('localhost', 'root', 'root', 'statistics');

    $sql = 'SELECT `vote` FROM `students` 
            WHERE `id` LIKE \'' . $student_id . '\'';

    return $connection_db->query($sql);
}

function update_student_select($id, $app){
    $connection_db = new mysqli('localhost', 'root', 'root', 'statistics');

    $sql = 'SELECT `appraisal`, `id` FROM `groups_subjects` 
            WHERE `id` LIKE \'' . $id . '\'';

    $result = $connection_db->query($sql);
    $app = intval($result->fetch_assoc()['appraisal']) + intval($app);
    $sql = 'UPDATE `groups_subjects` 
            SET `appraisal`=' . $app . ' WHERE `id` LIKE ' . $id;

    $result = $connection_db->query($sql);
    if(!$result)
        return 'errors ' . $connection_db->error;
    return 'ok';
}

function block_student($student_id){
    $connection_db = new mysqli('localhost', 'root', 'root', 'statistics');

    $sql = 'UPDATE `students` 
            SET `vote`=' . 0 . ' WHERE `id` LIKE ' . $student_id;

    return $connection_db->query($sql);
}

function select_all_info(){
    $connection_db = new mysqli('localhost', 'root', 'root', 'statistics');
    $all_group_info = array();
    $sql = 'SELECT * FROM `groups`';
    $groups = $connection_db->query($sql);
    $skip_admin_table = true;
    while ($row = $groups->fetch_assoc()) {
        if($skip_admin_table){
            $skip_admin_table = false;
            continue;
        }
        $all_subjects = array();
        $sql = 'SELECT * FROM `groups_subjects` 
            WHERE `group_id` LIKE \'' . $row['id'] . '\' ORDER BY `appraisal` DESC';
        $groups_subjects = $connection_db->query($sql);
        while ($subject_name = $groups_subjects->fetch_assoc()) {
            $sql = 'SELECT `subject` FROM `subjects` 
            WHERE `id` LIKE \'' . $subject_name['subject_id'] . '\'';

            $subjects = $connection_db->query($sql);
            $subjects = $subjects->fetch_assoc();
            $all_subjects[$subjects['subject']] = $subject_name['appraisal'];
        }
        $all_group_info[$row['group']] = array(
            "hide" => $row['hide'],
            "all_subjects" => $all_subjects
        );
    }
    return $all_group_info;
}

function allow_vote($group){
    $connection_db = new mysqli('localhost', 'root', 'root', 'statistics');
    $sql = 'SELECT `id` FROM `groups` 
            WHERE `group` LIKE \'' . $group . '\'';

    $id_subjects = $connection_db->query($sql);
    $id_subjects = $id_subjects->fetch_assoc();
    $sql = 'UPDATE `students` 
            SET `vote`=' . 1 . ' WHERE `group` = ' . $id_subjects['id'];
    return $connection_db->query($sql);

}

function clear_app_student($group){
    $connection_db = new mysqli('localhost', 'root', 'root', 'statistics');
    $sql = 'SELECT `id` FROM `groups` 
            WHERE `group` LIKE \'' . $group . '\'';

    $id_subjects = $connection_db->query($sql);
    $id_subjects = $id_subjects->fetch_assoc();
    $sql = 'UPDATE `groups_subjects` 
            SET `appraisal`=' . 0 . ' WHERE `group_id` = ' . $id_subjects['id'];
    return $connection_db->query($sql);
}

function save_passing_group($group, $value){
    $connection_db = new mysqli('localhost', 'root', 'root', 'statistics');
    $sql = 'UPDATE `groups` 
            SET `hide`=' . $value . ' WHERE `group` LIKE \'' . $group . '\'';
    return  $connection_db->query($sql);
}