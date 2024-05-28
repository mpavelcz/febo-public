<?php declare(strict_types = 1);

namespace App\Modules\Base;

use App\Model\App;
use Nette\Application\UI\ComponentReflection;
use Nette\Security\IUserStorage;

abstract class SecuredPresenter extends BasePresenter
{

	/**
	 * @param ComponentReflection|mixed $element
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function checkRequirements($element): void
	{
		if (!$this->user->isLoggedIn()) {
			if ($this->user->getLogoutReason() === IUserStorage::INACTIVITY) {
				$this->flashInfo('You have been logged out for inactivity');
			}

			$this->redirect(
				App::DESTINATION_SIGN_IN,
				['backlink' => $this->storeRequest()]
			);
		}
	}

	public function checkAccess($checking, $what){

		$allow = false;
		switch ($what){
			case 'user':
				if($this->user->getId() == $checking){
					$allow = true;
				}
				if(in_array('acl-admin', $this->user->getRoles())){
					$allow = true;
				}
				break;
			default:
				$allow = false;
				break;
		}

		$this->template->checkAcl = $allow;
		if(!$allow){
			$this->checkAclRedirect('Home:');
		}
		return $allow;

	}
	public function checkAcl($resource, $action, $rtf = true, $redirect = 'Home:'){
		if(!$this->user->isAllowed($resource, $action)){
			$this->template->checkAcl = false;
			if($rtf){
				$this->checkAclRedirect($redirect);
			}
			return false;
		}
		$this->template->checkAcl = true;
		return true;
	}

	public function checkAclRedirect($redirect){
		if($redirect){
			$this->flashInfo($this->translator->translate('msg.flash.nonacl'));
			$this->redirect(':Admin:Home:');
		}
	}

}
