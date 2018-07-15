<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="rpg_users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 */
class User extends BaseEntity implements UserInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     *
     * @var string
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     *
     * @var string
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=254, unique=true)
     *
     * @var string
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CartItem", mappedBy="user")
     *
     * @var CartItem[]|ArrayCollection
     */
    private $cartItems;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
        $this->cartItems = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return User
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getSalt(): ?string
    {
        return 'Cpo02N(o.<s`^0B@2U,./|-bc^C49H+4@r5soL9Z/ldQ2Xnf=+}xg:jrzq*=>SGx';
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return CartItem[]|ArrayCollection
     */
    public function getCartItems(): ArrayCollection
    {
        return $this->cartItems;
    }

    /**
     * @param CartItem[]|ArrayCollection $cartItems
     * @return User
     */
    public function setCartItems($cartItems)
    {
        $this->cartItems = $cartItems;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function eraseCredentials()
    {
    }

    /**
     * @ORM\PrePersist()
     *
     * @return void
     */
    public function prePersist(): void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate()
     *
     * @return void
     */
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }
}
