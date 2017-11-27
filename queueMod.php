<?php

if(isset($_POST['NewIDS'])){
    global $con;
    include '/var/www/html/tweetDelete/include/mysql_con.php';
    $list = $_POST['NewIDS'];
    $idList = is_array($list) ? $list : explode("," , $list);
    foreach($idList as $curId){
        $q = "INSERT INTO d_queue (tweet_id) VALUES('". $curId . "')";
        if(mysqli_query($con, $q)){
            $str .= "Adding ID $curId Successful! <br>";
        }else{
            $str .= "Adding ID $curId Failed! <br>";
        }
        unset($q);
    }
    echo $str;
}
?>