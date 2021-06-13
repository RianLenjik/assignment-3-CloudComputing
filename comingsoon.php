<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/b99e675b6e.js"></script>
    <link rel="stylesheet" href="styles.css" type="text/css" media="all">
    <title>Coming Soon</title>
</head>
<?php
include "navbar.php";
include "s3bucket.php";
?>

<body>
    <center>
        <h1>Coming Soon</h1>
    </center>

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
    <div class="wrapper">
        <div class="view main">
            <div class="view_wrap grid-view" style="display: block;">
                <?php
                foreach ($result['Items'] as $i) {
                    $film = $marshaler->unmarshalItem($i);
                    if ($film['year'] > 2021) {
                        $poster = "posters/" . preg_replace("/\s+/", '', $film['title']) . ".jpg";
                        $cmd = $s3Client->getCommand('GetObject', [
                            'Bucket' => 'a3-s3786798',
                            'Key' => $poster
                        ]);

                        $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');
                        $presignedUrl = (string)$request->getUri();
                ?>
                        <div class="view_item">
                            <form action="comingsoon.php" method="POST">
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
            $_SESSION['title'] = $value;
            echo "<script type='text/javascript'>window.location.href = 'movie.php';</script>";
        }
    }

    ?>
</body>

</html>