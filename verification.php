<?php
session_start();
// If necessary, modify the path in the require statement below to refer to the
// location of your Composer autoload.php file.
require 'vendor/autoload.php';

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;
// Create an SesClient. Change the value of the region parameter if you're
// using an AWS Region other than US West (Oregon). Change the value of the
// profile parameter if you want to use a profile in your credentials file
// other than the default.
$SesClient = new SesClient([
    'profile' => 'default',
    'version' => 'latest',
    'region' => 'us-east-1'
]);
// Replace sender@example.com with your "From" address.
// This address must be verified with Amazon SES.
$sender_email = 'rlenjik@gmail.com';
// Replace these sample addresses with the addresses of your recipients. If
// your account is still in the sandbox, these addresses must be verified.
$recipient_emails = ['rlenjik@gmail.com', 'rlenjik@gmail.com'];
// Specify a configuration set. If you do not want to use a configuration
// set, comment the following variable, and the
// 'ConfigurationSetName' => $configuration_set argument below.
//$configuration_set = 'ConfigSet';
$subject = 'Email Verification Request';
$plaintext_body = 'Please verify my email address ' . $_SESSION['email'];
$char_set = 'UTF-8';
try {
    $result = $SesClient->sendEmail([
        'Destination' => [
            'ToAddresses' => $recipient_emails,
        ],
        'ReplyToAddresses' => [$sender_email],
        'Source' => $sender_email,
        'Message' => [
            'Body' => [
                'Text' => [
                    'Charset' => $char_set,
                    'Data' => $plaintext_body,
                ],
            ],
            'Subject' => [
                'Charset' => $char_set,
                'Data' => $subject,
            ],
        ],
        // If you aren't using a configuration set, comment or delete the
        // following line
        //'ConfigurationSetName' => $configuration_set,
    ]);
    $messageId = $result['MessageId'];
    echo ("Email sent! Message ID: $messageId" . "\n");
    echo "<script type='text/javascript'>window.location.href = 'index.php';</script>";
} catch (AwsException $e) {
    // output error message if fails
    echo $e->getMessage();
    echo ("The email was not sent. Error message: " . $e->getAwsErrorMessage() . "\n");
    echo "\n";
}
