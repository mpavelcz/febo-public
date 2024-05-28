<?php declare(strict_types = 1);

namespace App\Model\Database\Repository;

use App\Model\App;
use App\Model\Database\Entity\Feed;
use Contributte\Redis\Caching\RedisStorage;
use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Predis\Client;

/**
 * @method Feed|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method Feed|NULL findOneBy(array $criteria, array $orderBy = NULL)
 * @method Feed[] findAll()
 * @method Feed[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends AbstractRepository<Feed>
 */
class FeedRepository extends AbstractRepository
{

	public function getMongoDBFeed($db = App::MONGO_DB_WEB){
		$configFCE = new App();
		$mongoDB = $configFCE->getMongoDB($db);
		$feedMDB = $mongoDB->db_feed;
		return $feedMDB;
	}


	public function findAllFeeds($dir = App::CACHE_CESTA)
	{
		$storage = new FileStorage($dir);
//		$storage = new RedisStorage();
//		print_r($storage);
		$cache = new Cache($storage, App::CACHE_FOLDER_FEEDS);

		$znacky = [];
		$value = $cache->load('all',function (&$dependencies) {
			$dependencies[Cache::Expire] = APP::CACHE_TIME_60;
			$pole = [];
			$value = $this->findAll();
			foreach ($value as $item){
				array_push($pole, $item->getId());
			}
			return $pole;
		});

		foreach ($value as $item){
			$vystup = $this->najdiJeden($item, $dir);
			array_push($znacky, $vystup);
		}
		return $znacky;
	}

	public function najdiJeden($id, $dir = App::CACHE_CESTA){
		$storage = new FileStorage($dir);

		$cache = new Cache($storage, App::CACHE_FOLDER_FEEDS);

		$value = $cache->load($id,function () use ($id, $redis) {
			$dependencies[Cache::Expire] = APP::CACHE_TIME_150;
			$pole = $redis->hGetAll(App::CACHE_FOLDER_FEEDS .':'.$id);

			return $pole;
		});

		return $value;
	}


	public function poleHodnot($value){

			$pole = [
				'id' => $value->getId(),
				'name' => $value->getName(),
				'active' => $value->isActive(),
				'beh' => $value->isBeh(),
				'prefix' => $value->getPrefix(),
				'koeficient' => $value->getKoeficient(),
				'runFrom' => $value->getRunFrom(),
				'runTo' => $value->getRunTo(),
				'settings' => $value->getSettings(),
				'prodlevaDoruceni' => $value->getProdlevaDoruceni()
			];
			return $pole;
	}

	public function poleHodnotById($id, $wcli = 'web'){

		$storage = new FileStorage(App::CACHE_CESTA);
		$cache = new Cache($storage, 'feeds');
		$pole = $cache->load('feed-'.$id.'-'.$wcli, function ($dependencies) use ( $id) {
			$dependencies[Cache::EXPIRE] = App::CACHE_TIME;
			return $this->poleHodnot($this->find($id));
		});
		return $pole;
	}

}
