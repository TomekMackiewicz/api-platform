<?php

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
 * @ORM\Table(name="`user`")
 * @UniqueEntity("email")
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
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(
     *   message = "The email '{{ value }}' is not a valid email."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @ApiProperty(security="is_granted('ROLE_ADMIN')")
     * @Assert\Choice(choices=User::ROLES, message="Role name is invalid.")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Regex(
     *   pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.).{6,}$/",
     *   message="Password is required to be minimum 6 chars in length and to include at least one letter and one number."
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(
     *   type="integer",
     *   message="The value {{ value }} is not a valid {{ type }}."
     * )
     * @ApiProperty(security="is_granted('ROLE_ADMIN')")
     */
    private $status;

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
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getStatus(): int
    {
        return (int) $this->status;
    }

    public function setStatus(int $status): self
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

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }
}
