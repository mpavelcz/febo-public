<?php declare(strict_types = 1);

namespace App\Model\Srovnavace;

use App\Model\ApiConnections;
use App\Model\App;
use App\Model\Cesty;
use App\Model\Database\Entity\HeurekaKategorie;
use App\Model\Database\EntityManager;
use App\Model\Utils\XmlDownloader;
use AsocialMedia\AllegroApi\AllegroRestApi;
use Aws\S3\S3Client;
use Matrix\Exception;
use MongoDB\BSON\Regex;
use MongoDB\Client;
use Nette\Utils\Arrays as NetteArrays;
use Nette\Utils\DateTime;

final class CategoryHeureka
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
//		parent::__construct();
		$this->EntityManager = $EntityManager;
	}

	public function nacistHeurekaKats()
	{
		$soubor = 'heureka-kats.xml';
		$link = 'https://www.heureka.cz/direct/xml-export/shops/heureka-sekce.xml';
		$xmlDown = new XmlDownloader();

		$xmlDown->ziskatXMLfeed($link, $soubor);
		$vystup = $xmlDown->vycteniDat($soubor, 'CATEGORY');

		foreach ($vystup as $item){
			$this->prochazeniHeurka($item);
		}

		return $vystup;
	}

	public function ulozitHeurekaKats($item)
	{
//		print_r($item);
//		print_r((int)$item['CATEGORY_ID']);
//		print_r('-/--/--/--/-');
		$daoKategorieHeureka = $this->EntityManager->getHeurekaKategorieRepository();

		$dotaz = $daoKategorieHeureka->findBy(['cid' => (int)$item['CATEGORY_ID']]);
		if(empty($dotaz)){
			$xx = new HeurekaKategorie();
			$xx->setCid((int)$item['CATEGORY_ID']);
			$xx->setName((string)$item['CATEGORY_NAME']);
			$xx->setFullpath((string)$item['CATEGORY_FULLNAME']);
			$this->EntityManager->persist($xx);
			$this->EntityManager->flush();
			$daoKategorieHeureka->findOneMongo(['cid' => (int)$item['CATEGORY_ID']], $daoKategorieHeureka, $daoKategorieHeureka->getMongoDB(), true);
		}else{
//			$dotaz->setName((string)$item['CATEGORY_NAME']);
//			$dotaz->setFullpath((string)$item['CATEGORY_FULLNAME']);
//			$this->EntityManager->persist($dotaz);
//			$this->EntityManager->flush();

			$daoKategorieHeureka->findOneMongo(['cid' => (int)$item['CATEGORY_ID']], $daoKategorieHeureka, $daoKategorieHeureka->getMongoDB(), true);
		}

	}

	public function prochazeniHeurka($data)
	{
//		print_r($data);
		if(!empty((string)$data['CATEGORY_FULLNAME'])){
			$this->ulozitHeurekaKats($data);
		}else{
			foreach ($data['CATEGORY'] as $item){
//				print_r('------');
//				print_r((array)$item);
				$this->prochazeniHeurka((array)$item);
			}
		}
	}

}