<?php declare(strict_types = 1);

namespace App\Model\Utils;

use App\Model\ApiConnections;
use App\Model\App;
use App\Model\Database\EntityManager;
use MongoDB\BSON\Regex;
use Nette\Utils\Arrays as NetteArrays;

final class Banka
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

	public function platby()
	{
		$app = new App();
		$daoBankaPlatba = $this->EntityManager->getInvoicesDataRepository();
		$daoInvoicesFiles = $this->EntityManager->getInvoicesFilesRepository();
		$daoBanka = $app->getMongoDB()->banka;

		$dotazTMPSTP = $daoInvoicesFiles->getMongoDB()->find(['splatnost_timestamp' => ['', null]]);
		foreach ($dotazTMPSTP as $xx){
			$daoInvoicesFiles->getMongoDB()->updateOne(
				['id' => $xx['id']],
				['$set' => ['splatnost_timestamp' => strtotime($xx['splatnost'])]],
				['upsert' => false]
			);
		}

		$dotazPlatby = $daoBankaPlatba->findBy(['payed' => false]);

		foreach ($dotazPlatby as $itm){
			$dotazBanka = $daoBanka->find(['vs' => (int)$itm->getVar(), 'ucet' => (string)$itm->getSupplier()->getCisloUctu()]);

			$castka = 0;
			foreach ($dotazBanka as $bank){
				$castka = $castka + $bank['castka'];
			}

			$doplatit = (int)$itm->getPriceVat()-(int)($castka*-1);
			$itm->setDoplatit($doplatit);

			$daoInvoicesFiles->getMongoDB()->updateOne(
				['id' => $itm->getFiles()->getId()],
				['$set' => ['doplatit' => $doplatit, 'forma_uhrady' => $itm->getFormaUhrady()]],
				['upsert' => false]
			);

			if(ceil($castka*-1) == ceil((float)$itm->getPriceVat())){
				$itm->setPayed(true);

				$app->actionRefreshData('Admin:Finance:Payed', $itm->getId());
			}

			$this->EntityManager->persist($itm);
			$this->EntityManager->flush();

		}
	}


}
