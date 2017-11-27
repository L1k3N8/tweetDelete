<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="L1K3N8">
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
                    <a class="navbar-brand" href="#">All Tweets</a>
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
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Tweet Listing
                    </h1>
                </div>
            </div>
            <div class="row" style="height:500px">
                <form action="index.php" method="post"> 
                    <input  type="text" name="Sname"> 
                    <input  type="submit" name="submit" value="Search"> 
                </form> 
                <div class="col-md-8">
                    <div class="clusterize">
                        <table >
                            <thead><tr>
                                <td>Timestamp</td>
                                <td>Text</td>
                                <td>Add to Queue</td>
                                </tr>
                            </thead>
                        </table>
                        <div id="scrollArea" class="clusterize-scroll">
                            <table>
                                <tbody id="contentArea" class="clusterize-content">
                                    <tr class="clusterize-no-data">
                                        <td>Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <?php 
                if(isset($_POST['Sname'])){
                    $word = $_POST['Sname'];
                    $wc = "AND text LIKE '" . "%$word%" . "'"; 
                    $cd = "checked";
                }else{
                    $wc = '';
                    $cd = '';
                }
                global $con;
                include('./include/mysql_con.php');
                $q = "SELECT tweet_id, timestamp, text FROM tweets WHERE tweet_id NOT IN (SELECT dt_id from deleted_ids) AND tweet_id NOT IN (SELECT tweet_id from d_queue) $wc ORDER BY tweet_id DESC";
                $result = mysqli_query($con, $q);
                if (mysqli_num_rows($result) > 0) {
                    // output data of each row
                    $rows = array();
                    while($row = mysqli_fetch_assoc($result)) {
                        $txt = str_replace(array('\'', '"'), '', $row['text']);
                        $id = $row['tweet_id'];
                        $when = $row['timestamp'];
                        $button = <<<OOP
<input type="checkbox" value="$id" $cd class="newQueue">
OOP;
                        $rows[] = "<tr><td>$when</td><td>$txt</td><td>$button</td></tr><br>";
                    }
                }
                $final = json_encode($rows);
                $tag = <<<DJS
            <script>
            var data = $final;
                var clusterize = new Clusterize({
                  rows: data,
                  scrollId: 'scrollArea',
                  contentId: 'contentArea'
                });
            </script>                
DJS;
                echo $tag;
                unset($_POST);
                ?>
                <div class="col-md-4">
                    <h3>Runner</h3>
                    <p><input type="submit" id="qadd" value="Add Selected Tweets To Queue"> </p>
                    <p id="results">results</p>
                </div>
            </div>
            <script>
                $( "#qadd" ).click(function() {
                    var arr = [];
                    $('input.newQueue:checkbox:checked').each(function () {
                        arr.push($(this).val());
                    });
                    $('#results').load('queueMod.php', {"NewIDS": arr});
                });
            </script>
        </div>
        <script src="js/jquery.js"></script>
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>
