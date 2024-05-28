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

/**
 * @ORM\Entity(repositoryClass="App\Model\Database\Repository\FeedRepository")
 * @ORM\Table(name="`feed`")
 * @ORM\HasLifecycleCallbacks
 */
class Feed extends AbstractEntity
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
	 * @ORM\OneToMany(targetEntity="FeedSoubory", mappedBy="feed")
	 */
	private $dodavatel;

	/**
	 * @ORM\OneToMany(targetEntity="Images", mappedBy="feed")
	 */
	private $images;

	/**
	 * @ORM\OneToMany(targetEntity="ProductBasic", mappedBy="feed")
	 */
	private $product;

	/**
	 * @ORM\OneToMany(targetEntity="FeedZnacky", mappedBy="feed")
	 */
	private $znacky;

	/**
	 * @ORM\OneToMany(targetEntity="FeedKategorie", mappedBy="feed")
	 */
	private $kategorie;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=255, nullable=FALSE, unique=false)
	 */
	private $login;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=255, nullable=FALSE, unique=false)
	 */
	private $password;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=5, nullable=FALSE, unique=TRUE)
	 */
	private $prefix;

	/**
	 * @ORM\Column(type="text")
	 */
	private $settings;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $active = false;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $beh = false;

	/**
	 * @var decimal
	 * @ORM\Column(type="decimal", precision=12, scale=2, options={"default":1})
	 */
	private $koeficient;

	/**
	 * @var integer
	 * @ORM\Column(type="integer", nullable=FALSE, options={"default":21})
	 */
	private $dph;

	/**
	 * @var integer
	 * @ORM\Column(type="integer", nullable=FALSE, options={"default":0})
	 */
	private $prodlevaDoruceni;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=5, nullable=FALSE)
	 */
	private $mena;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=255, nullable=FALSE)
	 */
	private $casSpusteni;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=255, nullable=FALSE)
	 */
	private $runFrom;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=255, nullable=FALSE)
	 */
	private $runTo;



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
	public function getDodavatel(): string
	{
		return $this->dodavatel;
	}

	/**
	 * @param string $dodavatel
	 */
	public function setDodavatel(string $dodavatel): void
	{
		$this->dodavatel = $dodavatel;
	}

	/**
	 * @return string
	 */
	public function getLogin(): string
	{
		return $this->login;
	}

	/**
	 * @param string $login
	 */
	public function setLogin(string $login): void
	{
		$this->login = $login;
	}

	/**
	 * @return string
	 */
	public function getPassword(): string
	{
		return $this->password;
	}

	/**
	 * @param string $password
	 */
	public function setPassword(string $password): void
	{
		$this->password = $password;
	}

	/**
	 * @return string
	 */
	public function getPrefix(): string
	{
		return $this->prefix;
	}

	/**
	 * @param string $prefix
	 */
	public function setPrefix(string $prefix): void
	{
		$this->prefix = $prefix;
	}

	/**
	 * @return mixed
	 */
	public function getProduct()
	{
		return $this->product;
	}

	/**
	 * @param mixed $product
	 */
	public function setProduct($product): void
	{
		$this->product = $product;
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
	 * @return bool
	 */
	public function isBeh(): bool
	{
		return $this->beh;
	}

	/**
	 * @param bool $beh
	 */
	public function setBeh(bool $beh): void
	{
		$this->beh = $beh;
	}


	public function getKoeficient()
	{
		return $this->koeficient;
	}


	public function setKoeficient($koeficient): void
	{
		$this->koeficient = $koeficient;
	}

	/**
	 * @return integer
	 */
	public function getDph()
	{
		return $this->dph;
	}

	/**
	 * @param integer $dph
	 */
	public function setDph(int $dph): void
	{
		$this->dph = $dph;
	}



	/**
	 * @return string
	 */
	public function getMena(): string
	{
		return $this->mena;
	}

	/**
	 * @param string $mena
	 */
	public function setMena(string $mena): void
	{
		$this->mena = $mena;
	}

	/**
	 * @return string
	 */
	public function getCasSpusteni(): string
	{
		return $this->casSpusteni;
	}

	/**
	 * @param string $casSpusteni
	 */
	public function setCasSpusteni(string $casSpusteni): void
	{
		$this->casSpusteni = $casSpusteni;
	}

	/**
	 * @return string
	 */
	public function getRunFrom(): string
	{
		return $this->runFrom;
	}

	/**
	 * @param string $runFrom
	 */
	public function setRunFrom(string $runFrom): void
	{
		$this->runFrom = $runFrom;
	}

	/**
	 * @return string
	 */
	public function getRunTo(): string
	{
		return $this->runTo;
	}

	/**
	 * @param string $runTo
	 */
	public function setRunTo(string $runTo): void
	{
		$this->runTo = $runTo;
	}

	/**
	 * @return mixed
	 */
	public function getZnacky()
	{
		return $this->znacky;
	}

	/**
	 * @param mixed $znacky
	 */
	public function setZnacky($znacky): void
	{
		$this->znacky = $znacky;
	}

	/**
	 * @return mixed
	 */
	public function getKategorie()
	{
		return $this->kategorie;
	}

	/**
	 * @param mixed $kategorie
	 */
	public function setKategorie($kategorie): void
	{
		$this->kategorie = $kategorie;
	}

	/**
	 * @return int
	 */
	public function getProdlevaDoruceni()
	{
		return $this->prodlevaDoruceni;
	}

	/**
	 * @param int $prodlevaDoruceni
	 */
	public function setProdlevaDoruceni(int $prodlevaDoruceni): void
	{
		$this->prodlevaDoruceni = $prodlevaDoruceni;
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

	public function addProducts(ProductBasic $product)
	{
		$product->addFeed($this); // synchronously updating inverse side
		$this->product[] = $product;
	}


}
