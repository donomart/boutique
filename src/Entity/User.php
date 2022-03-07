<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity (
 *     fields={"email"},
 *     message="Cette adresse email est déjà utilisée.",groups={"registration"}
 * )
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="Vous devez renseigner une adresse email.", groups={"registration"})
     * @Assert\Email(message = "L'adresse email '{{ value }}' n'est pas valide.",groups={"registration"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Vous devez renseigner un mot de passe.", groups={"registration"})
     * @Assert\Length (
     *     min = 8,
     *     max = 50,
     *     minMessage = "Le mot de passe doit faire au moins {{ limit }} caractères.",
     *     maxMessage = "Le mot de passe doit faire moins de {{ limit }} caractères.",
     *    groups={"registration"}
     * )
     * @Assert\Regex (
     *     pattern="#(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).{8,}#",
     *     match=true,
     *     message="Le mot de passe doit contenir au moins une lettre minuscule, une majuscule, et un chiffre.",
     *      groups={"registration"}
     * )
     */
    private $password;

    /**
     * @Assert\NotBlank(message="Vous devez confirmer le mot de passe.", groups={"registration"})
     * @Assert\EqualTo (
     *     propertyPath = "password",
     *     message = "Les deux mots de passe sont différents.",
     *     groups={"registration"}
     * )
     */
    public $confirmPassword;

    private $currentPassword;

    /**
     * @var string The hashed password
     * @Assert\NotBlank(message="Vous devez renseigner un mot de passe.")
     * @Assert\Length (
     *     min = 8,
     *     max = 50,
     *     minMessage = "Le mot de passe doit faire au moins {{ limit }} caractères.",
     *     maxMessage = "Le mot de passe doit faire moins de {{ limit }} caractères."
     * )
     * @Assert\Regex (
     *     pattern="#(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).{8,}#",
     *     match=true,
     *     message="Le mot de passe doit contenir au moins une lettre minuscule, une majuscule, et un chiffre."
     * )
     */
    private $newPassword;

    /**
     * @Assert\EqualTo (
     *     propertyPath = "newPassword",
     *     message = "Les deux mots de passe sont différents."
     * )
     */
    private $newConfirmPassword;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez renseigner votre prénom.", groups={"registration"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez renseigner votre nom.", groups={"registration"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity=Address::class, mappedBy="user")
     */
    private $addresses;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="user")
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity=ResetPassword::class, mappedBy="user", orphanRemoval=true)
     */
    private $resetPasswords;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->resetPasswords = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get the value of currentPassword
     */
    public function getCurrentPassword()
    {
        return $this->currentPassword;
    }

    /**
     * Set the value of currentPassword
     *
     * @return  self
     */
    public function setCurrentPassword($currentPassword)
    {
        $this->currentPassword = $currentPassword;

        return $this;
    }

    /**
     * Get the value of newPassword
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }

    /**
     * Set the value of newPassword
     *
     * @return  self
     */
    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    /**
     * Get the value of newConfirmPassword
     */
    public function getNewConfirmPassword()
    {
        return $this->newConfirmPassword;
    }

    /**
     * Set the value of newConfirmPassword
     *
     * @return  self
     */
    public function setNewConfirmPassword($newConfirmPassword)
    {
        $this->newConfirmPassword = $newConfirmPassword;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection|Address[]
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses[] = $address;
            $address->setUser($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): self
    {
        if ($this->addresses->removeElement($address)) {
            // set the owning side to null (unless already changed)
            if ($address->getUser() === $this) {
                $address->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }


    public function getFullName() {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * @return Collection|ResetPassword[]
     */
    public function getResetPasswords(): Collection
    {
        return $this->resetPasswords;
    }

    public function addResetPassword(ResetPassword $resetPassword): self
    {
        if (!$this->resetPasswords->contains($resetPassword)) {
            $this->resetPasswords[] = $resetPassword;
            $resetPassword->setUser($this);
        }

        return $this;
    }

    public function removeResetPassword(ResetPassword $resetPassword): self
    {
        if ($this->resetPasswords->removeElement($resetPassword)) {
            // set the owning side to null (unless already changed)
            if ($resetPassword->getUser() === $this) {
                $resetPassword->setUser(null);
            }
        }

        return $this;
    }
}
