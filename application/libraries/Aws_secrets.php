<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');



require __DIR__.'/AWS/vendor/autoload.php';
use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;

class Aws_secrets {
    private $client;
    private $secret_name;

    public function __construct() {
        // Always retrieve the secret name
        $this->secret_name = getenv('AWS_SECRET_NAME');

        // Retrieve other environment variables
        $region_name = getenv('AWS_REGION_NAME');
        $aws_access_key_id = getenv('AWS_ACCESS_KEY_ID');
        $aws_secret_access_key = getenv('AWS_SECRET_ACCESS_KEY');

        var_dump( $aws_access_key_id); exit;

        // Initialize options array
        $options = [
            'version' => 'latest',
            'region'  => $region_name,
            'suppress_php_deprecation_warning' => true
        ];

        // Add credentials if available (primarily for development)
        if ($aws_access_key_id && $aws_secret_access_key) {
            $options['credentials'] = [
                'key'    => $aws_access_key_id,
                'secret' => $aws_secret_access_key,
            ];
        }

        // Initialize the SecretsManagerClient
        $this->client = new SecretsManagerClient($options);
    }

    public function getSecret() {
        try {
            $result = $this->client->getSecretValue([
                'SecretId' => $this->secret_name,
            ]);

            if (isset($result['SecretString'])) {
                return $result['SecretString']; // Return the secret as a string
            } else {
                log_message('error', 'Secret retrieved but no SecretString found.');
                return null;
            }
        } catch (AwsException $e) {
            log_message('error', 'Error retrieving secret: ' . $e->getMessage());
            return null;
        }
    }
}