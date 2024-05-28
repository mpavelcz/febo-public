<?php declare(strict_types = 1);

namespace App\Model\Database\Repository;

use App\Model\App;
use App\Model\Database\Entity\AclRole;
use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;

/**
 * @method AclRole|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method AclRole|NULL findOneBy(array $criteria, array $orderBy = NULL)
 * @method AclRole[] findAll()
 * @method AclRole[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends AbstractRepository<AclRole>
 */
class AclRoleRepository extends AbstractRepository
{

	public $cacheName = 'vsechny-role';
	public $cacheNameSpace = 'role';
	public function findOneByEmail(string $email): ?User
	{
		return $this->findOneBy(['email' => $email]);
	}

	public function findAllRoles(){
		$storage = new FileStorage(App::CACHE_CESTA);
		$cache = new Cache($storage, $this->cacheNameSpace);

		$value = $cache->load($this->cacheName,function () {
			$value = $this->findAll();
			return $value;
		}, [Cache::EXPIRE => APP::CACHE_TIME]);
		return $value;
	}

	public function deleteCacheRole()
	{
		return parent::deleteCache($this->cacheName, $this->cacheNameSpace); 
	}

}
