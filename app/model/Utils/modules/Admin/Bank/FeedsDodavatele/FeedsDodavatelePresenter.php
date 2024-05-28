<?php declare(strict_types = 1);

namespace App\Modules\Admin\FeedsDodavatele;

use App\Domain\Order\Event\OrderCreated;
use App\Model\App;
use App\Model\Database\Entity\AclRole;
use App\Model\Database\Entity\ParametrValue;
use App\Model\Database\Entity\Znacky;
use App\Model\Database\Repository\AclRoleRepository;
use App\Model\Database\Repository\FeedZnackyRepository;
use App\Model\Utils\Strings;
use App\Modules\Admin\BaseAdminPresenter;


use App\Modules\Admin\components\ListOfProducts\ListOfProductsControl;
use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Contributte\Translation\Wrappers\Message;
use Mpdf\Tag\Li;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Nette\Forms\Controls\Button;
use Nette\Security\Permission;
use Nette\Utils\Paginator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Ublaboo\DataGrid\DataGrid;

final class FeedsDodavatelePresenter extends BaseAdminPresenter
{

	private $resour = 'Admin:Feeds';
	private $listProducts;

	public function actionDefault()
	{

	}


	/***** DODAVATELE *****/
	public function renderDefault(){
		$this->checkAcl($this->resour.':Dodavatele', 'view');
		$this->template->drobky = [
			'Home:default' => [0 => 'msg.menu.feeds', 1=> 0],
			'#' => [0 => 'msg.menu.supplier', 1 => 1]
		];
	}

	public function handleEditDodavatele($id){
		$daoZnackyFeed = $this->EntityManager->getFeedRepository()->find($id);
//		bdump($daoZnackyFeed);
		$this['editZnackyFeedForm']->setDefaults([
			'name' => $daoZnackyFeed->getName(),
			'sku' => $daoZnackyFeed->getSku(),
			'id' => $daoZnackyFeed->getId(),
			'active' => $daoZnackyFeed->isActive(),
			'dodavatel' => $daoZnackyFeed->getFeed()->getName(),

			'koeficient' => $daoZnackyFeed->getKoeficient()
		]);

		if(!empty($daoZnackyFeed->getZnacka())){
			$this['editZnackyFeedForm']->setDefaults([
				'znacka' => $daoZnackyFeed->getZnacka()->getId()
			]);
		}

		$this->payload->message = 'Success';
		if ($this->isAjax()) {
			$this->payload->exampleModal = true;
			$this->payload->complete = true;
			$this->payload->modalId = 'exampleModal';
			$this->payload->isModal = TRUE;
//			$this->payload->closeModal = TRUE;
			$this->redrawControl("modal");
			$this->redrawControl("modalJs");
			$this->redrawControl("modalJsDva");

		}
	}

	public function createComponentDodavateleFeedGrid($name)
	{
		$grid = new DataGrid($this, $name);

//		bdump($this->EntityManager->getFeedRepository()->findAll());
		$daoFeed = $this->EntityManager->getFeedRepository();

		$grid->setDataSource($daoFeed->findAllMongo($daoFeed->getMongoDBFeed()));
		$grid->setItemsPerPageList([50, 100,150,200], true);
		$grid->addColumnText('id','Id')
			->setSortable();
		$grid->addColumnText('name',$this->translator->translate('msg.forms.name'))
			->setSortable();
		$grid->addFilterText('name', $this->translator->translate('msg.forms.name'));
		$grid->addColumnText('prefix',$this->translator->translate('msg.forms.prefix'))
			->setSortable();
		$grid->addFilterText('prefix', $this->translator->translate('msg.forms.prefix'));
		$columStatus = $grid->addColumnStatus('active', $this->translator->translate('msg.forms.active'))
			->setFilterSelect([
				'' => 'All',
				1 => 'Active',
				0 => 'Inactive',
			]);
		$grid->addColumnText('runFrom', $this->translator->translate('msg.forms.runFrom'))
			->setSortable();
		$grid->addFilterText('runFrom', $this->translator->translate('msg.forms.runFrom'));
		$grid->addColumnText('runTo', $this->translator->translate('msg.forms.runTo'))
			->setSortable();
		$grid->addFilterText('runTo', $this->translator->translate('msg.forms.runTo'));
		$grid->addColumnText('koeficient', $this->translator->translate('msg.forms.koeficient'))
			->setSortable();
		$grid->addFilterText('koeficient', $this->translator->translate('msg.forms.koeficient'));

		$grid->addColumnText('settings', $this->translator->translate('msg.forms.settings'))
			->setSortable();
		$grid->addFilterText('settings', $this->translator->translate('msg.forms.settings'));

		$grid->addColumnText('prodlevaDoruceni',$this->translator->translate('msg.forms.delay'))
			->setSortable();
		$grid->addFilterText('prodlevaDoruceni', $this->translator->translate('msg.forms.delay'));

//		$grid->addAction('akce', '', 'editZnackyFeed!')
//			->setIcon('fa-solid fa-pencil')
//			->setDataAttribute('bs-toggle', 'modal')
//			->setDataAttribute('bs-target', '#exampleModal')
//			->setClass('btn btn-sm btn-primary ajax');

		return $grid;
	}
	/***** DODAVATELE *****/
    
}