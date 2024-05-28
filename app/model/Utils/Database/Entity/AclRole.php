<?php declare(strict_types = 1);

namespace App\Model\Database\Entity;

use App\Model\Database\Entity\Attributes\TCreatedAt;
use App\Model\Database\Entity\Attributes\TId;
use App\Model\Database\Entity\Attributes\TUpdatedAt;
use App\Model\Database\Entity\decimal;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * @ORM\Entity(repositoryClass="App\Model\Database\Repository\AclRoleRepository")
 * @ORM\Table(name="`acl_role`")
 * @ORM\HasLifecycleCallbacks
 */
class AclRole extends AbstractEntity
{


	use TId;
	use TCreatedAt;
	use TUpdatedAt;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=255, nullable=FALSE, unique=false)
	 */
	private $name;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=255, nullable=FALSE, unique=false)
	 */
	private $url;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=255, nullable=FALSE, unique=false)
	 */
	private $comment;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $active = false;

	/**
	 * One product has many features. This is the inverse side.
	 * @var Collection<int, AclPrivileges>
	 * @OneToMany(targetEntity="AclPrivileges", mappedBy="aclRole")
	 */
	private Collection $privileges;

	/**
	 * One MenuItem has Many MenuItems.
	 * @ORM\OneToMany(targetEntity="AclRole", mappedBy="parent")
	 */
	private $children;

	/**
	 * Many MenuItems have One MenuItem.
	 * @ORM\ManyToOne(targetEntity="AclRole", inversedBy="children")
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
	 */
	private $parent;



	public function __construct() {
		$this->children = new ArrayCollection();
		$this->privileges = new ArrayCollection();
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getUrl(): string
	{
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function setUrl(string $url): void
	{
		$this->url = $url;
	}

	/**
	 * @return string
	 */
	public function getComment(): string
	{
		return $this->comment;
	}

	/**
	 * @param string $comment
	 */
	public function setComment(string $comment): void
	{
		$this->comment = $comment;
	}

	/**
	 * @return bool
	 */
	public function isActive(): bool
	{
		return $this->active;
	}

	/**
	 * @param bool $active
	 */
	public function setActive(bool $active): void
	{
		$this->active = $active;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getChildren(): ArrayCollection
	{
		return $this->children;
	}

	/**
	 * @param ArrayCollection $children
	 */
	public function setChildren(ArrayCollection $children): void
	{
		$this->children = $children;
	}

	/**
	 * @return mixed
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * @param mixed $parent
	 */
	public function setParent($parent): void
	{
		$this->parent = $parent;
	}

	/**
	 * @return Collection
	 */
	public function getPrivileges(): Collection
	{
		return $this->privileges;
	}

	/**
	 * @param Collection $privileges
	 */
	public function setPrivileges(Collection $privileges): void
	{
		$this->privileges = $privileges;
	}


	public function addPrivileges(AclPrivileges $feedPar){
		$this->privileges[] = $feedPar;
	}





}
