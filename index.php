<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/b99e675b6e.js"></script>
    <link rel="stylesheet" href="styles.css" type="text/css" media="all">
    <title>Main</title>
</head>

<?php
session_start();
include "navbar.php";
include "s3bucket.php";
?>

<body>
    <center>
        <h1>Now Showing</h1>
    </center>
    <div class="grid">
        <center>
            <!-- <a  href="gui.movie_details.php?sid=&movie_id=11KTME">
            <a href="movie.php"><img id='AE' src="posters/AE.jpg" style="max-height:280px"></a>
            </a>
            <div class="clearfix "> </div>
            <div class="title">AVENGERS: ENDGAME</div>

            <div class="btn-group-sm rating">
                <span class="btn btn-default btn-outline disabled" style="color: #005350;">2D</span>
                <a class="btn btn-default btn-outline disabled" style="color: #005350;">PG13+</a>
                <div class="clearfix"></div>
                <span style="color:red; font-size:9px; margin-top:5px"></span>
            </div> -->
            <center>
    </div>



    <?php
    require 'vendor/autoload.php';
    date_default_timezone_set('Australia/Melbourne');

    use Aws\DynamoDb\Exception\DynamoDbException;
    use Aws\DynamoDb\Marshaler;

    $sdk = new Aws\Sdk([
        'region' => 'us-east-1',
        'version' => 'latest'
    ]);
    $dynamodb = $sdk->createDynamoDb();
    $marshaler = new Marshaler();

    $params = [
        'TableName' => 'films',
        'ProjectionExpression' => 'title, #yr',
        'ExpressionAttributeNames' => ['#yr' => 'year']
    ];

    $result = $dynamodb->scan($params);

    ?>
    <form action="verification.php" method="POST">
        <input type="submit" name="verify" value="Verify Email">
    </form>

    <div class="wrapper">
        <div class="view main">
            <div class="view_wrap grid-view" style="display: block;">
                <?php
                foreach ($result['Items'] as $i) {
                    $film = $marshaler->unmarshalItem($i);
                    if ($film['year'] < 2022) {
                        $poster = "posters/" . preg_replace("/\s+/", '', $film['title']) . ".jpg";
                        $cmd = $s3Client->getCommand('GetObject', [
                            'Bucket' => 'a3-s3786798',
                            'Key' => $poster
                        ]);

                        $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');
                        $presignedUrl = (string)$request->getUri();
                ?>
                        <div class="view_item">
                            <form action="index.php" method="POST">
                                <div class="vi_left">
                                    <input type="image" name="movie[<?php echo preg_replace("/\s+/", '', $film['title']); ?>]" type="submit" src="<?php echo $presignedUrl ?>" style="width:170px;height:340;">
                                </div>
                            </form>
                            <div class="vi_right">
                                <p class="title"><?php echo $film['title']; ?></p>
                            </div>
                        </div>
                <?php
                    }
                }

                ?>
            </div>
        </div>

    </div>
    <script src="scripts.js"></script>

    <?php

    if (isset($_POST['movie'])) {
        foreach (array_keys($_POST['movie']) as $value) {
            @$_SESSION['title'] = $value;
            echo "<script type='text/javascript'>window.location.href = 'movie.php';</script>";
        }
    }

    ?>
</body>

</html>