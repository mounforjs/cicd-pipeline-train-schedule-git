<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tinymce extends CI_Controller {

	public function __construct () 	{
		parent::__construct();
	}


	public function uploadImage() {
		reset ($_FILES);
		$temp = current($_FILES);
		if (is_uploaded_file($temp['tmp_name'])) {
			// Sanitize input
			if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
				header("HTTP/1.1 400 Invalid file name.");
				return;
			}

			// Verify extension
			if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png"))) {
				header("HTTP/1.1 400 Invalid extension.");
				return;
			}

			require 's3upload/vendor/autoload.php';
			$bucketName = 'winwinlabs';
			$IAM_KEY = 'AKIAQJB56S7IBUCT5GOQ';
			$IAM_SECRET = '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';
			// Connect to AWS
			try {
				$s3 = new Aws\S3\S3Client([
					'region'  => 'us-east-2',
					'version' => 'latest',
					'credentials' => [
						'key'    => "AKIAQJB56S7IBUCT5GOQ",
						'secret' => "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",
					]
				]);
			} catch (Exception $e) {
				die("Error: " . $e->getMessage());
			}

			$username = getprofile()->username;
			$path = basename($temp['name']);
			$date = time();
			$res = 'icon';
			$imgext = pathinfo($path, PATHINFO_EXTENSION);
			$keyName =  $date.'_'.$username.'_'.$res.'.'.$imgext;
			$pathInS3 = 'https://winwinlabs.s3.us-east-2.amazonaws.com/' . $keyName;
			$imgext = pathinfo($temp['name'], PATHINFO_EXTENSION);

			// Add it to S3
			try {
				// Uploaded:
				$file = $temp['tmp_name'];
				$result = $s3->putObject(
					array(
						'Bucket'=>$bucketName,
						'Key' =>  $keyName,
						'Body'   => 'this is the body!',
						'Content-Type' =>"image/*",
						'SourceFile' => $file
					)
				);
			} catch (S3Exception $e) {
				die('Error:' . $e->getMessage());
			} catch (Exception $e) {
				die('Error:' . $e->getMessage());
			}

			if ($imgext!='gif') {
				$imgpath = "https://dg7ltaqbp10ai.cloudfront.net/fit-in/".$keyName;
			} else {
				$imgpath = $result['ObjectURL'];
			}
				
			echo json_encode(array('location' => $imgpath));
		 } else {
			// Notify editor that the upload failed
			header("HTTP/1.1 500 Server Error");
		}
	}
}
?>
