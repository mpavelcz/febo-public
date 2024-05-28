<?php declare(strict_types = 1);

namespace App\Modules\Admin\Bank;

use App\Domain\Order\Event\OrderCreated;
use App\Model\App;
use App\Model\Cesty;
use App\Model\Database\Entity\AclRole;
use App\Model\Database\Entity\Dostupnost;
use App\Model\Database\Entity\ParametrValue;
use App\Model\Database\Entity\Parametry;
use App\Model\Database\Entity\Znacky;
use App\Model\Database\Repository\AclRoleRepository;
use App\Model\Database\Repository\FeedZnackyRepository;
use App\Model\Utils\AWSS3;
use App\Model\Utils\Strings;
use App\Modules\Admin\BaseAdminPresenter;
use Contributte\Translation\Wrappers\Message;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Nette\Forms\Controls\Button;
use Nette\Security\Permission;
use phpseclib3\File\ASN1\Maps\BaseDistance;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Ublaboo\DataGrid\DataGrid;

final class BankPresenter extends BaseAdminPresenter
{
	private $resour = 'Admin:Bank';

	public function renderDefault()
	{
		$this->checkAcl($this->resour, 'view');
		$this->template->vypis = 'homepage';

		$this->template->drobky = [
			'Bank:default' => [0 => 'msg.menu.bank', 1=> 0],
			'#' => [0 => 'msg.menu.bank', 1 => 1]
		];

//		echo phpinfo();
	}

	public function createComponentBankaGrid($name)
	{
		$app = new App();
		$grid = new DataGrid($this, $name);
		$daoBankList = $app->getMongoDB()->banka;

//		bdump($this->EntityManager->getFeedKategorieRepository()->findAllKategorie());

		$dotaz = json_decode(json_encode($daoBankList->find([])->toArray()), true);

		$grid->setDataSource($dotaz);
		$grid->setItemsPerPageList([50, 100, 500], false);

		$grid->addColumnText('id','Id')
			->setSortable();

		$grid->addColumnText('nazev', $this->translator->translate('msg.forms.sender'))
			->setSortable();
		$grid->addFilterText('nazev', $this->translator->translate('msg.forms.sender'));

		$grid->addColumnText('datum_splatnosti', $this->translator->translate('msg.forms.date'))
			->setSortable();
		$grid->addFilterText('datum_splatnosti', $this->translator->translate('msg.forms.date'));

		$grid->addColumnText('castka', $this->translator->translate('msg.forms.price'))
			->setSortable();
		$grid->addFilterText('castka', $this->translator->translate('msg.forms.price'));

		$grid->addColumnText('vs', $this->translator->translate('msg.forms.var'))
			->setSortable();
		$grid->addFilterText('vs', $this->translator->translate('msg.forms.var'));

		$grid->addColumnText('ucet', $this->translator->translate('msg.forms.account'))
			->setSortable();
		$grid->addFilterText('ucet', $this->translator->translate('msg.forms.account'));



		return $grid;
	}

	public function createComponentUploadBankCsvForm(){
		$form = new Form();

		$form->addSelect('company', 'Company', ['febo' => 'Febo s.r.o.', 'jm' => 'Jan Mervart'])
			->setPrompt('Pick a company')
			->addRule($form::REQUIRED);
		$form->addUpload('file', 'File');
//		$form->addSubmit('submit', 'Submit');

		$form->addSubmit('send', 'Submit')
			->onClick[] = [$this, 'formBankSucceeded'];
		return $form;
	}

	public function formBankSucceeded(Form $form){

		$kde = $this->resour;
		$co = 'add';
		$app = new App();
		$aws = new AWSS3();

		$values = $form->getValues();

		$file = $values['file'];

		if($file->isOk()){

			$nameFile = $app->randomHash($app->getDatumNow('stamp')) .'-'.$file->getName();
			$file->move(Cesty::FILES_BANK_REPORTS_FULL .'/'. $nameFile);

			$aws->uploadFile(Cesty::FILES_BANK_REPORTS_FULL .'/'. $nameFile, $nameFile, Cesty::FILES_BANK_REPORTS);

			$daoBankFiles = $app->getMongoDB()->bankCsvFiles;
			$pole = [
				'company' => (string)$values['company'],
				'datum' => (string)$app->getDatumNow(),
				'id' => (int)$app->getDatumNow('stemp'),
				'file_name' => (string)$nameFile,
				'loaded' => (int)0,
				'user_id' => $this->user->getIdentity()->getId(),
				'user_name' => $this->user->getIdentity()->getFullname()
			];
			$daoBankFiles->insertOne($pole);

			$app->actionRefreshData($kde, $pole['id'], $this->user);
			$app->actionRefreshData($kde.":Platby", $pole['id'], $this->user);
			$app->actionRefreshData($kde.":Dopravy", $pole['id'], $this->user);

		}

		$this->flashMessage($this->translator->translate('msg.menu.bank', 0) . ' - '. $this->translator->translate('msg.flashmsg.success', 2), 'alert-success');
		$this->redirect('this');

	}


}