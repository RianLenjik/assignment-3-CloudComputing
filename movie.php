<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie</title>
</head>
<?php
session_start();
include "navbar.php";

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
    'ProjectionExpression' => 'title, actors, director, plot, release_date, running_time_secs, postal_code',
];

$result = $dynamodb->scan($params);

foreach ($result['Items'] as $i) {
    $film = $marshaler->unmarshalItem($i);
    if (preg_replace("/\s+/", '', $film['title']) == $_SESSION['title']) {
?>

        <body>
            <div style="margin-bottom: 0px; min-height: 200px; padding: 10px;">
                <h2><?php echo $film['title'] ?></h2>
                <img id='AE' src="posters/AE.jpg" style="max-height:280px">
                <div class="extra-wrap1">

                    <br><span class="data"><strong>Release date: </strong><?php echo $film['release_date'] ?></span><br>
                    <span class="data"><strong>Cast: </strong><?php echo $film['actors'] ?></span><br>
                    <span class="data"><strong>Director: </strong><?php echo $film['director'] ?></span><br>
                    <span class="data"><strong>Running Time: </strong><?php if ($film['running_time_secs'] > 0) {
                                                                            $hours = floor($film['running_time_secs'] / 3600);
                                                                            $minutes = floor(($film['running_time_secs'] / 60) % 60);
                                                                            if ($hours > 1) {
                                                                                echo "$hours hours and $minutes minutes";
                                                                            } else {
                                                                                echo "$hours hour and $minutes minutes";
                                                                            }
                                                                        } else {
                                                                            echo "TBA";
                                                                        } ?></span><br>
                    <span style="vertical-align: inherit;"><strong>Synopsis: </strong><?php echo $film['plot'] ?></span><br><br>
                    <a href="https://www.youtube.com/watch?v=hA6hldpSTF8" target="_blank" class="btn btn-primary">Watch Trailer</a><br>
                </div>
        <?php
    }
}

$params2 = [
    'TableName' => 'locations',
    'ProjectionExpression' => 'postal_code, suburb, address',
];

$result2 = $dynamodb->scan($params2);

$params3 = [
    'TableName' => 'cinemas',
    'ProjectionExpression' => 'postcode, #se',
    'ExpressionAttributeNames' => ['#se' => 'session']
];

$result3 = $dynamodb->scan($params3);
        ?>
        <h2>Playing at</h2>
        <table border="1px" cellpadding="4" cellspacing="50">
            <tr>
                <td width="150px;" style="text-align: center;">
                    <span><strong>Theater<strong></span>
                </td>
                <td width="100px;" style="text-align: center;">
                </td>


                <?php
                foreach ($result['Items'] as $i) {
                    $film = $marshaler->unmarshalItem($i);
                    if (preg_replace("/\s+/", '', $film['title']) == $_SESSION['title']) {
                        foreach ($result2['Items'] as $j) {
                            $location = $marshaler->unmarshalItem($j);
                            if (strlen($film['postal_code']) > 4) {
                                $postcodes = explode(", ", $film['postal_code']);
                                foreach ($postcodes as $postcode) {
                                    if ($postcode == $location['postal_code']) {
                ?>
            <tr>
                <td>
                    <center><?php echo $location['suburb']; ?></center>
                </td><?php
                                        $postcode = $location['postal_code'];
                                        echo "<td><center><form action='movie.php' method='POST'><input type='submit' name='book[$postcode]' value='Book'></form></center></td>";
                                        echo "</tr>";
                                    }
                                }
                            } else {

                                if ($film['postal_code'] == $location['postal_code']) {
                        ?>
            <tr>
                <td>
                    <center><?php echo $location['suburb']; ?></center>
                </td><?php
                                    $postcode = $location['postal_code'];
                                    echo "<td><center><form action='movie.php' method='POST'><input type='submit' name='book[$postcode]' value='Book'></form></center></td>";
                                    echo "</tr>";
                                }
                            }
                        }
                    }
                }
                        ?>
            </tr>
        </table>
        <br>
        <br>
        <br>
        <br>
        <br>
            </div>
        </body>

</html>

<?php
if (isset($_POST['book'])) {
    foreach (array_keys($_POST['book']) as $value) {
        @$_SESSION['postcode'] = $value;
        echo "<script type='text/javascript'>window.location.href = 'booking.php';</script>";
    }
}
