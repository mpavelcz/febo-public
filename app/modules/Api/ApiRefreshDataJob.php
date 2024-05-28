<?php

namespace App\Modules\Api;

use App\Model\ApiConnections;
use App\Model\App;
use App\Model\Database\Entity\FeedParametry;
use App\Model\Database\Entity\Images;
use App\Model\Database\Entity\ProductBasic;
use App\Model\Database\Entity\ProductImages;
use App\Model\Database\Entity\ProductParams;
use App\Model\Database\Entity\ProductPrice;
use App\Model\Database\Entity\ProductTexts;
use App\Model\Database\EntityManager;
use App\Model\Utils\AWSS3;
use App\Model\Utils\Dph;
use App\Model\Utils\Obrazky;
use App\Model\Utils\OurApiConnect;
use App\Model\Utils\ParserFiles;
use App\Model\Utils\Strings;
use GuzzleHttp\Client;
use App\Model\Database\Repository\KategorieBeRepository;
use phpseclib3\Crypt\EC\Curves\prime192v1;


class ApiRefreshDataJob
{
	/**
	 * @inject
	 * @var EntityManager
	 */
	public $EntityManager;

	/**
	 * @param EntityManager $EntityManager
	 */
	public function __construct(EntityManager $EntityManager)
	{
		$this->EntityManager = $EntityManager;
	}


	public function finance($id, $refreshDataMongoDb, $item)
	{
		$app = new App();
		$guzzle = new Client();
		$daoInvoicesFiles = $this->EntityManager->getInvoicesFilesRepository();
		$dotaz = $daoInvoicesFiles->findOneBy(['id' => (int)$id, 'loaded' => (int)0]);
		if(!empty($dotaz) AND !empty($dotaz->getUrl()) AND empty($dotaz->getUrlGiven())){
			$response = $guzzle->request('POST', ApiConnections::MICROSOFT_AZURE_URL_FEBO_TEST_1, [
				'headers' => [
					'Content-Type'              => 'application/json',
					'Ocp-Apim-Subscription-Key' => ApiConnections::MICROSOFT_AZURE_KEY_FEBO_TEST_1
				],
				'json' => [
					'urlSource' => $dotaz->getUrl()
				]
			]);

			$urlNew = $response->getHeaders()['Operation-Location'][0];

			if(!empty($urlNew)) {
				$dotaz->setUrlGiven($urlNew);
				$dotaz->setSended(1);
				$this->EntityManager->persist($dotaz);
				$this->EntityManager->flush();

				$app->actionRefreshData('Admin:Finance:InvocieLoad', $dotaz->getId());
				$daoInvoicesFiles->findOneMongo(['id' => (int)$dotaz->getId()], $daoInvoicesFiles, $daoInvoicesFiles->getMongoDB(), true);
			}

			$refreshDataMongoDb->deleteOne(['kde' => (string)$item['kde'], 'id' => (int)$id]);
		}
		return true;
	}

    public function financeInvoiceLoad($id, $refreshDataMongoDb, $item)
	{
		$app = new App();
		$guzzle = new Client();
		$daoInvoicesFiles = $this->EntityManager->getInvoicesFilesRepository();
		$dotaz = $daoInvoicesFiles->findOneBy(['id' => (int)$id]);
		sleep(15);

		$response = $guzzle->request('GET', $dotaz->getUrlGiven(), [
			'headers' => [
				'Content-Type'              => 'application/json',
				'Ocp-Apim-Subscription-Key' => ApiConnections::MICROSOFT_AZURE_KEY_FEBO_TEST_1
			]
		]);

		$vystup = json_decode($response->getBody()->getContents(), true);

		if(!empty($vystup['analyzeResult']['documents'][0]['fields'])){

			$dotazHash = $daoInvoicesFiles->findBy(['description' => json_encode($vystup['analyzeResult']['documents'][0]['fields'])]);

			if(count($dotazHash) == 0){
				$dotaz->setLoaded(1);
				$dotaz->setDescription(json_encode($vystup['analyzeResult']['documents'][0]['fields']));
				$this->EntityManager->persist($dotaz);
				$this->EntityManager->flush();

				$daoInvoicesFiles->findOneMongo(['id' => (int)$dotaz->getId()], $daoInvoicesFiles, $daoInvoicesFiles->getMongoDB(), true);

				$app->actionRefreshData('Admin:Finance:InvoiceIncoming', $dotaz->getId());
			}else{
				$this->EntityManager->remove($dotaz);
				$this->EntityManager->flush();
				$daoInvoicesFiles->getMongoDB()->deleteOne(['id' => $id]);
			}

			$refreshDataMongoDb->deleteOne(['kde' => (string)$item['kde'], 'id' => (int)$id]);
		}
		return true;
	}

}