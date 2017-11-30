<?php
/*
*   Name:       queue.php
*   Version:    1.0
*   Notes:      This script runs continuously in the background to delete tweets in the queue.
*               It's designed to always wait 60 seconds in between deletions, so it never 
*               runs into API rate-limiting issues, however I'm not fully convinced that the
*               timing is working properly.
*/
function logIt($text){
    $logfile = '/var/log/tweetDLete.log';
    $now = date("F j, Y, g:i a");
    file_put_contents($logfile, "TweetDLete -- [" . $now . "] : $text \n", FILE_APPEND);
}
global $con;
include './include/mysql_con.php';
include './include/apiKeys.php';

function delete_tweet($id){
    global $con;
    require_once('../twitter-api-php/TwitterAPIExchange.php');
    $url = "https://api.twitter.com/1.1/statuses/destroy/$id.json";
    $postfields = array('id' => "$id");
    $requestMethod = 'POST';
    $settings = array(
        'oauth_access_token' => $oauthToken,
        'oauth_access_token_secret' => $oauthSecret,
        'consumer_key' => $myKey,
        'consumer_secret' => $mySecret
    );
    $twitter = new TwitterAPIExchange($settings);
    try{
        $response =  $twitter->buildOauth($url, $requestMethod)
            ->setPostfields($postfields)
            ->performRequest();
    }
    catch(Exception $ew){
        return false;
    }
    if(!empty($response)){
        $q = "DELETE FROM d_queue WHERE tweet_id = $id";
        $q2 = "INSERT INTO deleted_ids(dt_id) VALUES ('". $id ." ')";
        mysqli_query($con, $q);
        mysqli_query($con, $q2);
        logIt("Successful move on Tweet $id");
        return true;

    }else{
        return false;
    }
}

function handle(){
    $slpCount = 0;
    check:
    while(pause_button() == "Running"){
        $rtn = intval(queueCheck());
        switch ($rtn) {
            case -1:
                logIt("Nothing found in queue. Continuing...");
                break;
            case 0:
                logIt("Time check passed. A tweet should have been deleted...");
                break;
            case -2:
                logIt("Post-delete table was empty. A tweet should have been deleted.");
                break;
            default:
                logIt("Time check failed. Going to sleep for $rtn seconds.");
                sleep($rtn);
                break;
        }
    }
    $slpCount ++;
    if ($slpCount == 30){
        logIt("Queue deletion stopped. Will try again later");
        $slpCount = 0;
    }
    sleep(60);
    goto check;
}

function pause_button(){
    global $con;
    $sql = "SELECT state from runnerStatus";
    $result = mysqli_query($con, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $wantedState = $row['state'];
        }
    }else{
        $wantedState = "Stopped";
    }
    return $wantedState;
}

function queueCheck(){
    global $con;
    $sql = "SELECT tweet_id,time_added from d_queue ORDER BY tweet_id ASC LIMIT 1";
    $result = mysqli_query($con, $sql);
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $oldestQueue = $row['tweet_id'];
            $sql2 = "SELECT UNIX_TIMESTAMP(time_deleted) as `whendeleted`, unix_timestamp(now(4)) as `nowtime` from deleted_ids ORDER by time_deleted DESC LIMIT 1";
            $result2 = mysqli_query($con, $sql2);
            if (mysqli_num_rows($result2) > 0) {
                while($row2 = mysqli_fetch_assoc($result2)) {
                    $lastDel = $row2['whendeleted'];
                    $nowTime = $row2['nowtime'];
                    $dif = $nowTime - $lastDel;
                    if($dif < 60 ){
                        return $dif;
                    }
                    else{
                        delete_tweet($oldestQueue);
                        return 0;
                    }
                }
            }
            else{
                delete_tweet($oldestQueue);
                return -2;
            }
        }
    } else {
        return -1;
    }
}

//Starts the script
handle();

?>

