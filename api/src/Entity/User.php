<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="users")
 * @UniqueEntity("email", message="validation.unique")
 * @UniqueEntity("username", message="validation.unique")
 * @ApiResource(
 *   collectionOperations={
 *     "get"={"security"="is_granted('ROLE_ADMIN')"},
 *     "post"
 *   },
 *   itemOperations={
 *     "get"={"security"="is_granted('ROLE_ADMIN') or object.getId() == user.getId()"},
 *     "put"={"security"="object.getId() == user.getId()"},
 *     "delete"={"security"="is_granted('ROLE_ADMIN') or object.getId() == user.getId()"},
 *     "patch"={"security"="is_granted('ROLE_ADMIN') or object.getId() == user.getId()"}
 *   }
 * )
 */
class User implements UserInterface
{
    const ROLES = [
        [],
        ['ROLE_USER'], 
        ['ROLE_ADMIN'],
        ['ROLE_USER', 'ROLE_ADMIN']
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="validation.not_blank")
     */
    private string $username = '';

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="validation.not_blank")
     * @Assert\Email(message="validation.email")
     */
    private string $email = '';

    /**
     * @ORM\Column(type="json")
     * @Assert\Choice(choices=User::ROLES, message="validation.invalid_choice")
     */
    private iterable $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="validation.not_blank")
     * @Assert\Regex(
     *   pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.).{6,}$/",
     *   message="validation.invalid_password"
     * )
     */
    private string $password = '';

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(
     *   type="integer",
     *   message="validation.not_int"
     * )
     * @Assert\Choice({0, 1}, message="validation.choice")
     * @ApiProperty(security="is_granted('ROLE_ADMIN')")
     */
    private ?int $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): string
    {
        return (string) $this->username;
    }
    
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

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

    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getStatus(): ?int
    {
        return (int) $this->status;
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;

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

    public function eraseCredentials()
    {
        //$this->password = null;
    }
}
