<?php declare(strict_types = 1);

namespace App\Model\Utils;

use App\Model\ApiConnections;
use App\Model\App;
use App\Model\Cesty;
use Aws\S3\S3Client;
use Matrix\Exception;
use MongoDB\BSON\Regex;
use Nette\Utils\Arrays as NetteArrays;

final class AWSS3
{

	public $endpoint = ApiConnections::AWS_ENDPOINT;
	public $bucket = ApiConnections::AWS_BUCKET;
	public $aws  = null;
	public function awsS3connect()
	{
		$aws = new S3Client([
			'credentials' => [
				'key'    => ApiConnections::AWS_KEY,
				'secret' => ApiConnections::AWS_PASS
			],
			'region' => ApiConnections::AWS_REGION,
			'version' => 'latest',
			'endpoint' => ApiConnections::AWS_ENDPOINT,
			'use_path_style_endpoint' => true,
			'options' => [
				'ssl' => [
					'verify_peer' => false,
					'verify_peer_name' => false,
				],
			],
		]);

		return $aws;
	}

	public function quploadFile($file, $name, string $folder, $del = false)
	{
		try {
			$aws = $this->awsS3connect();

			$body = fopen($file, 'rb');

			$aws->putObject([
				'Bucket' => ApiConnections::AWS_BUCKET,
				'Key'    => $folder.'/'.$name,
				'Body'   => $body,
				'ACL' => 'public-read'
			]);

			if($del){
				unlink($file);
			}
			return ApiConnections::AWS_PUBLIC_URL .'/'. $folder.'/'. $name;

		}catch (Exception $e){
			$logger = new OurLogger();
			$logger->setLog('upload to contabo', $e);
//			print_r($e);
		}
	}

	public function ulozitObrazek($img, $mongoDB, $name, $em, $imgId, $item)
	{
		try {
		$vystupJpg = $this->uploadFile(Cesty::IMAGES_FILES_JPG.'/'.$img['jpg'], $img['jpg'], 'img', true);
		$vystupWebp = $this->uploadFile(Cesty::IMAGES_FILES_WEBP.'/'.$img['webp'], $img['webp'], 'img', true);

//		$dotaz = $mongoDB->findOneBy(['id' => (int)$imgId]);
			$item->setUrlJpg($vystupJpg);
			$item->setUrlWebp($vystupWebp);
		$em->persist($item);
		$em->flush();

		$mongoDB->getMongoDB()->updateOne(
			['link' => (string)$name],
			['$set' => [
				'urlJpg' => (string)$vystupJpg,
				'urlWebp' => (string)$vystupWebp
			]],
			['upsert' => false]
		);

		return true;
		}catch (Exception $e){
			$logger = new OurLogger();
			$logger->setLog('upload to contabo', $e);
//			print_r($e);
		}
	}
}