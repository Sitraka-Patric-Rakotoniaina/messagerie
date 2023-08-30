<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(normalizationContext: ['groups' => ['message:read']], denormalizationContext: ['groups' => ['message:write']])]
#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('message:read')]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'chatRoom')]
    #[Groups(['message:read', 'message:write'])]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity: ChatRoom::class, inversedBy: 'messages')]
    #[Groups(['message:read', 'message:write'])]
    private Collection $chatRooms;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Content cannot be blank")]
    #[Groups(['message:read', 'message:write'])]
    private ?string $content = null;

    public function __construct()
    {
        $this->chatRooms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, ChatRoom>
     */
    public function getChatRooms(): Collection
    {
        return $this->chatRooms;
    }

    public function addChatRoom(ChatRoom $chatRoom): static
    {
        if (!$this->chatRooms->contains($chatRoom)) {
            $this->chatRooms->add($chatRoom);
        }

        return $this;
    }

    public function removeChatRoom(ChatRoom $chatRoom): static
    {
        $this->chatRooms->removeElement($chatRoom);

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }
}
