<?php
require 'vendor/autoload.php';
date_default_timezone_set('Australia/Melbourne');

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

$s3Client = new S3Client([
    'version' => 'latest',
    'region' => 'us-east-1',
    'credentials' => [
        'key' => 'AKIAZ5GJJKV6WO6QCCVN',
        'secret' => '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'
    ],
]);
