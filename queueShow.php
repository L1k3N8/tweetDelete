<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Twitter Job Runner</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="./js/jquery.js"></script>
    <link href="css/portfolio-item.css" rel="stylesheet">
<link href="./css/clusterize.css" rel="stylesheet">
<script src="./js/clusterize.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">Home</a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="queueShow.php">Queue</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Tweet Listing
                </h1>
            </div>
        </div>
        <div class="row" style="height:500px">
             <div class="col-md-8">
            <?php 
            global $con;
            include('./include/mysql_con.php');
            $q = "select tweet_id, time_added from d_queue order by queue_id ASC";
            $result = mysqli_query($con, $q);
            if (mysqli_num_rows($result) > 0) {
                // output data of each row
                $start = "<table style='border-collapse: separate; border-spacing: 10px;'><thead><tr><th>Tweet ID</th><th>Time Added To Queue</th></tr></thead><tbody>";
                while($row = mysqli_fetch_assoc($result)) {
                    $tim = $row['time_added'];
                    $idno = $row['tweet_id'];
                    $start .= "<tr><td>$idno</td><td>$tim</td></tr>";
                }
                $start .= "</tbody></table>";
                echo $start;
            }
                
            ?>
            </div>
    </div>
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
