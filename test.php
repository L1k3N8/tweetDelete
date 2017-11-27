<!DOCTYPE HTML>
<html>
<head>

</head>
<body>

<?php include './include/mysql_con.php';
$sql = "SELECT tweet_id, timestamp, text FROM tweets LIMIT 100";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
        echo "id: " . $row["tweet_id"]. " - Date: " . $row["timestamp"]. "Text " . $row["text"]. "<br>";
    }
} else {
    echo "0 results";
}    
    
?>
</body>
</html>
