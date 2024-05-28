<?php declare(strict_types = 1);

namespace App\Model\Database\Entity;

use App\Model\Database\Entity\Attributes\TCreatedAt;
use App\Model\Database\Entity\Attributes\TId;
use App\Model\Database\Entity\Attributes\TUpdatedAt;
use App\Model\Exception\Logic\InvalidArgumentException;
use App\Model\Database\Entity\FeedZnacky;
use App\Model\Security\Identity;
use App\Model\Utils\DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation\Enum;

/**
 * @ORM\Entity(repositoryClass="App\Model\Database\Repository\DodavateleRepository")
 * @ORM\Table(name="`dodavatele`")
 * @ORM\HasLifecycleCallbacks
 */


class Dodavatele extends AbstractEntity
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
	private $ico;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=255, nullable=FALSE, unique=false)
	 */
	private $dic;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=255, nullable=FALSE, unique=false)
	 */
	private $cisloUctu;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=255, nullable=FALSE, unique=false)
	 */
	private $tel;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=255, nullable=FALSE, unique=false)
	 */
	private $email;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=255, nullable=FALSE, unique=false)
	 */
	private $ulice;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=10, nullable=FALSE, unique=false)
	 */
	private $psc;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=255, nullable=FALSE, unique=false)
	 */
	private $mesto;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=5, nullable=FALSE, unique=false)
	 */
	private $mena;

	/**
	 * @ORM\Column(type="integer", length=5, nullable=FALSE, unique=false)
	 */
	private $feedId;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=255, nullable=FALSE, unique=false)
	 */
	private $obchodniZastupceJmeno;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=255, nullable=FALSE, unique=false)
	 */
	private $obchodniZastupceTel;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=255, nullable=FALSE, unique=false)
	 */
	private $obchodniZastupceEmail;

	/**
	 * @ORM\Column(type="text", nullable=true, unique=false)
	 */
	private $settings;

	/**
	 * @ORM\OneToMany(targetEntity="InvoicesData", mappedBy="customer")
	 */
	private $customer;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=30, nullable=FALSE, unique=false)
	 */
	private $zboslu;

	/**
	 * @ORM\OneToMany(targetEntity="InvoicesData", mappedBy="supplier")
	 */
	private $supplier;

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getIco(): string
	{
		return $this->ico;
	}

	public function setIco(string $ico): void
	{
		$this->ico = $ico;
	}

	public function getDic(): string
	{
		return $this->dic;
	}

	public function setDic(string $dic): void
	{
		$this->dic = $dic;
	}

	public function getCisloUctu(): string
	{
		return $this->cisloUctu;
	}

	public function setCisloUctu(string $cisloUctu): void
	{
		$this->cisloUctu = $cisloUctu;
	}

	public function getTel(): string
	{
		return $this->tel;
	}

	public function setTel(string $tel): void
	{
		$this->tel = $tel;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function setEmail(string $email): void
	{
		$this->email = $email;
	}

	public function getUlice(): string
	{
		return $this->ulice;
	}

	public function setUlice(string $ulice): void
	{
		$this->ulice = $ulice;
	}

	public function getPsc(): string
	{
		return $this->psc;
	}

	public function setPsc(string $psc): void
	{
		$this->psc = $psc;
	}

	public function getMesto(): string
	{
		return $this->mesto;
	}

	public function setMesto(string $mesto): void
	{
		$this->mesto = $mesto;
	}

	public function getMena(): string
	{
		return $this->mena;
	}

	public function setMena(string $mena): void
	{
		$this->mena = $mena;
	}

	public function getObchodniZastupceJmeno(): string
	{
		return $this->obchodniZastupceJmeno;
	}

	public function setObchodniZastupceJmeno(string $obchodniZastupceJmeno): void
	{
		$this->obchodniZastupceJmeno = $obchodniZastupceJmeno;
	}

	public function getObchodniZastupceTel(): string
	{
		return $this->obchodniZastupceTel;
	}

	public function setObchodniZastupceTel(string $obchodniZastupceTel): void
	{
		$this->obchodniZastupceTel = $obchodniZastupceTel;
	}

	public function getObchodniZastupceEmail(): string
	{
		return $this->obchodniZastupceEmail;
	}

	public function setObchodniZastupceEmail(string $obchodniZastupceEmail): void
	{
		$this->obchodniZastupceEmail = $obchodniZastupceEmail;
	}

	/**
	 * @return mixed
	 */
	public function getSettings()
	{
		return $this->settings;
	}

	/**
	 * @param mixed $settings
	 */
	public function setSettings($settings): void
	{
		$this->settings = $settings;
	}

	/**
	 * @return mixed
	 */
	public function getInvoices()
	{
		return $this->invoices;
	}

	/**
	 * @param mixed $invoices
	 */
	public function setInvoices($invoices): void
	{
		$this->invoices = $invoices;
	}

	/**
	 * @return mixed
	 */
	public function getFeedId()
	{
		return $this->feedId;
	}

	/**
	 * @param mixed $feedId
	 */
	public function setFeedId($feedId): void
	{
		$this->feedId = $feedId;
	}



	/**
	 * @return mixed
	 */
	public function getCustomer()
	{
		return $this->customer;
	}

	/**
	 * @param mixed $customer
	 */
	public function setCustomer($customer): void
	{
		$this->customer = $customer;
	}

	/**
	 * @return mixed
	 */
	public function getSupplier()
	{
		return $this->supplier;
	}

	/**
	 * @param mixed $supplier
	 */
	public function setSupplier($supplier): void
	{
		$this->supplier = $supplier;
	}

	public function getZboslu(): string
	{
		return $this->zboslu;
	}

	public function setZboslu(string $zboslu): void
	{
		$this->zboslu = $zboslu;
	}










}
