<?php declare(strict_types = 1);

namespace App\Model\Database\Repository;

use App\Model\App;
use App\Model\Database\Entity\Dodavatele;
use App\Model\Database\Entity\Feed;
use Contributte\Redis\Caching\RedisStorage;
use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Predis\Client;

/**
 * @method Dodavatele|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method Dodavatele|NULL findOneBy(array $criteria, array $orderBy = NULL)
 * @method Dodavatele[] findAll()
 * @method Dodavatele[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends AbstractRepository<Dodavatele>
 */
class DodavateleRepository extends AbstractRepository
{

	public function getMongoDB($db = App::MONGO_DB_WEB){
		$configFCE = new App();
		$mongoDB = $configFCE->getMongoDB($db);
		$feedMDB = $mongoDB->db_dodavatele;
		return $feedMDB;
	}


	public function poleHodnot($value){
		$pole = [
			'id' => $value->getId(),
			'name' => $value->getName(),
			'ico' => $value->getIco(),
			'dic' => $value->getDic(),
			'cislo_uctu' => $value->getCisloUctu(),
			'tel' => $value->getTel(),
			'email' => $value->getEmail(),
			'ulice' => $value->getUlice(),
			'mesto' => $value->getMesto(),
			'mena' => $value->getMena(),
			'settings' => $value->getSettings(),
			'zboslu' => $value->getZboslu(),
			'obchodni_zastupce_jmeno' => $value->getObchodniZastupceJmeno(),
			'obchodni_zastupce_tel' => $value->getObchodniZastupceTel(),
			'obchodni_zastupce_email' => $value->getObchodniZastupceEmail()
		];
		return $pole;
	}

}
