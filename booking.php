<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking</title>
</head>
<?php
session_start();
include 'navbar.php';
include "s3bucket.php";
?>

<body>
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
        'TableName' => 'locations',
        'ProjectionExpression' => 'postal_code, suburb, address',
    ];

    $result = $dynamodb->scan($params);

    $params2 = [
        'TableName' => 'cinemas',
        'ProjectionExpression' => '#se, theater_num, available_tickets, postcode, film_title',
        'ExpressionAttributeNames' => ['#se' => 'session']
    ];

    $result2 = $dynamodb->scan($params2);

    $poster = "posters/" . $_SESSION['title'] . ".jpg";
    $cmd = $s3Client->getCommand('GetObject', [
        'Bucket' => 'a3-s3786798',
        'Key' => $poster
    ]);

    $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');
    $presignedUrl = (string)$request->getUri();

    foreach ($result['Items'] as $i) {
        $location = $marshaler->unmarshalItem($i);
        if ($location['postal_code'] == $_SESSION['postcode']) {

    ?>

            <body>
                <div style="margin-bottom: 0px; min-height: 200px; padding: 10px;">
                    <h2><?php echo $location['suburb'] ?></h2>
                    <img id='AE' src="<?php echo $presignedUrl ?>" style="max-height:280px">
                </div>
        <?php
        }
    }


        ?>
        <h2>Sessions</h2>
        <table border="1px" cellpadding="4" cellspacing="50">
            <tr>
                <td width="150px;" style="text-align: center;">
                    <span><strong>Theater Number<strong></span>
                </td>
                <td width="150px;" style="text-align: center;">
                    <span><strong>Session<strong></span>
                </td>
                <td width="150px;" style="text-align: center;">
                    <span><strong>Available Tickets<strong></span>
                </td>
                <td width="100px;" style="text-align: center;">
                </td>


                <?php
                foreach ($result2['Items'] as $i) {
                    $cinema = $marshaler->unmarshalItem($i);
                    if (
                        preg_replace("/\s+/", '', $cinema['film_title']) == $_SESSION['title'] &&
                        $cinema['postcode'] == $_SESSION['postcode']
                    ) {

                ?>
            <tr>
                <td>
                    <center><?php echo $cinema['theater_num']; ?></center>
                </td>
                <td>
                    <center><?php echo $cinema['session']; ?></center>
                </td>
                <td>
                    <center><?php echo $cinema['available_tickets']; ?></center>
                </td><?php
                        echo "<td><center><form action='movie.php' method='POST'><input type='submit' name='book' value='Book'></form></center></td>";
                    }
                }

                        ?>
            <tr>
            </tr>
        </table>
            </body>

</html>