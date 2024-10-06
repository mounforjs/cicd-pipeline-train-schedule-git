<?php
if ( !defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );
require __DIR__.'/s3upload/vendor/autoload.php';



class Aws_s3 {

    protected $aws_keys;

    public function __construct() {
        // Load model to retrieve AWS keys
        $CI = & get_instance();
        $CI->load->model( 'aws_keys_model' );
        $this->aws_keys = $CI->aws_keys_model->get_aws_keys();
        // Fetch AWS credentials from the database
    }

    public function upload( $fileUpload, $bucketName = null ) {

        // Use bucket from AWS keys or default to the provided one
        $bucketName = $bucketName ?? $this->aws_keys->bucket_name;

        // Initialize S3 client
        try {
            $s3 = new Aws\S3\S3Client( [
                'region'  => 'us-east-2',
                'version' => 'latest',
                'credentials' => [
                    'key'    => $this->aws_keys->access_key,
                    'secret' => $this->aws_keys->secret_key,
                ]
            ] );
        } catch ( Exception $e ) {
            die( 'Error: ' . $e->getMessage() );
        }

        // Extract the file extension
        $path = $fileUpload[ 'name' ];
        $imgext = pathinfo( $path, PATHINFO_EXTENSION );
        $keyName = uniqid( time() ) . '_icon.' . strtolower( $imgext );

        // Validate file type
        $validExtensions = [ 'jpg', 'jpeg', 'png', 'gif' ];
        if ( !in_array( strtolower( $imgext ), $validExtensions ) ) {
            return [ 'status' => 'error', 'path' => 'Not a valid image type!' ];
        }

        // Prepare the file for upload
        try {
            $file = $fileUpload[ 'tmp_name' ];

            // Check if the file exists
            if ( !file_exists( $file ) ) {
                die( 'Error: File does not exist.' );
            }

            $mime = mime_content_type( $file );
            $resource = fopen( $file, 'r' );

            if ( !$resource ) {
                die( 'Error: Could not open file for reading.' );
            }

            // Upload the file to S3
            $result = $s3->putObject( [
                'Bucket'      => $bucketName,
                'Key'         => $keyName,
                'Body'        => $resource,
                'Content-Type'=> $mime,
                'ACL'    => 'private',
                'SourceFile'  => $file
            ] );

            // Determine image path
            $imgpath = ( $imgext != 'gif' ) ?
            'https://dg7ltaqbp10ai.cloudfront.net/' . $keyName :
            $result[ 'ObjectURL' ];

            return [ 'status' => 'success', 'path' => $imgpath ];

        } catch ( Aws\S3\Exception\S3Exception $e ) {
            // Specific S3 exception handling
            die( 'S3 Error: ' . $e->getMessage() );
        } catch ( Exception $e ) {
            // General exception handling
            die( 'Error: ' . $e->getMessage() );
        }

    }
}