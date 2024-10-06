<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require ('application/libraries/s3upload/vendor/autoload.php');

class Upload extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('aws_keys_model');
        check_login();
    }

    public function index() {
        $this->load->view('s3upload/index');
    }

    public function upload_image() {
        if ($this->input->server('REQUEST_METHOD') == "POST") {

            $aws_keys = $this->aws_keys_model->get_aws_keys();

            $s3 = new Aws\S3\S3Client([
                'region'  => 'us-east-2',
                'version' => 'latest',
                'credentials' => [
                    'key'    => $aws_keys->access_key,
                    'secret' => $aws_keys->secret_key,
                ]
            ]);

            $keyName = basename($_FILES["fileToUpload"]['name']);
            $imgext = pathinfo($keyName, PATHINFO_EXTENSION);

            if (!in_array(strtolower($imgext), ['jpg', 'jpeg', 'png', 'gif'])) {
                echo 'Not a valid image type!';
                return;
            }

            try {
                $file = $_FILES["fileToUpload"]['tmp_name'];
                $mime = mime_content_type($file);

                $result = $s3->putObject([
                    'Bucket' => $aws_keys->bucket_name,
                    'Key'    => $keyName,
                    'Body'   => fopen($file, 'r'),
                    'Content-Type' => $mime,
                    'SourceFile' => $file
                ]);

                $imgpath = ($imgext != 'gif') ? 
                    "https://dg7ltaqbp10ai.cloudfront.net/fit-in/200x200/".$keyName : 
                    $result['ObjectURL'];

                $data['imgpath'] = $imgpath;
                $this->load->view('s3upload/index', $data);
                
            } catch (S3Exception $e) {
                die('Error:' . $e->getMessage());
            } catch (Exception $e) {
                die('Error:' . $e->getMessage());
            }
        }
    }
}
