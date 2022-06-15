<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Api\AppAddUserController;
use App\Repository\ApplicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
#[ApiResource(
    attributes: ['security' => 'is_granted("ROLE_USER")'],
    collectionOperations: [
        'get' => [
            'security' => 'is_granted("ROLE_ADMIN") or is_granted("ROLE_MODERATOR")',
        ],
        'post' => [
            'security' => 'is_granted("ROLE_ADMIN")',
        ]
    ],
    itemOperations: [
        'get' => [
            'security' => 'is_granted("ROLE_ADMIN") or is_granted("ROLE_MODERATOR")',
        ],
        'add_user' => [
            'method' => 'POST',
            'path' => '/applications/{id}/add_user',
            'controller' => AppAddUserController::class,
            'denormalization_context' => [
                'groups' => ['app:add_user']
            ]
        ]
    ],
    normalizationContext: ['groups' => ['app:read']],
    denormalizationContext: ['groups' => ['app:write']],
)]
class Application
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['app:read'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups(['app:read', 'app:write'])]
    private $name;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'applications')]
    private $users;

    #[Groups(['app:add_user'])]
    #[Assert\NotBlank(groups: ['app:add_user'])]
    private $user;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addApplication($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeApplication($this);
        }

        return $this;
    }

    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }
}
