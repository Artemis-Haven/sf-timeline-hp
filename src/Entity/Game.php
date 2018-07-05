<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 */
class Game
{
    const STATE_SELECT_CARD = 'SELECT';
    const STATE_ELECT_CARD = 'ELECT';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $started;

    /**
     * @ORM\Column(type="boolean")
     */
    private $ended;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="games")
     */
    private $members;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $turn;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Hand", mappedBy="game", orphanRemoval=true)
     */
    private $hands;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="game", orphanRemoval=true)
     * @ORM\OrderBy({"createdAt" = "ASC"})
     */
    private $messages;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\BlackDeck", mappedBy="game", cascade={"persist", "remove"})
     */
    private $blackDeck;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\WhiteDeck", mappedBy="game", cascade={"persist", "remove"})
     */
    private $whiteDeck;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\BlackCard", cascade={"persist", "remove"})
     */
    private $blackCard;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $state;

    public function __toString()
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->hands = new ArrayCollection();
        $this->started = false;
        $this->ended = false;
        $this->messages = new ArrayCollection();
    }

    public function getId()
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function start(): self
    {
        $this->started = true;
        $this->setState(self::STATE_SELECT_CARD);

        return $this;
    }

    public function getStarted(): ?bool
    {
        return $this->started;
    }

    public function setStarted(bool $started): self
    {
        $this->started = $started;

        return $this;
    }

    public function getEnded(): ?bool
    {
        return $this->ended;
    }

    public function setEnded(bool $ended): self
    {
        $this->ended = $ended;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
        }

        return $this;
    }

    public function removeMember(User $member): self
    {
        if ($this->members->contains($member)) {
            $this->members->removeElement($member);
        }

        return $this;
    }

    public function getTurn(): ?User
    {
        return $this->turn;
    }

    public function setTurn(?User $turn): self
    {
        $this->turn = $turn;

        return $this;
    }
    
    /**
     * @return Collection|Hand[]
     */
    public function getHands(): Collection
    {
        return $this->hands;
    }

    /**
     * @return Hand
     */
    public function getHand(User $user): Hand
    {
        foreach ($this->hands as $hand) {
            if ($hand->getOwner() == $user) {
                return $hand;
            }
        }
        return null;
    }

    public function addHand(Hand $hand): self
    {
        if (!$this->hands->contains($hand)) {
            $this->hands[] = $hand;
            $hand->setGame($this);
        }

        return $this;
    }

    public function removeHand(Hand $hand): self
    {
        if ($this->hands->contains($hand)) {
            $this->hands->removeElement($hand);
            // set the owning side to null (unless already changed)
            if ($hand->getGame() === $this) {
                $hand->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setGame($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getGame() === $this) {
                $message->setGame(null);
            }
        }

        return $this;
    }

    public function getBlackDeck(): ?BlackDeck
    {
        return $this->blackDeck;
    }

    public function setBlackDeck(BlackDeck $blackDeck): self
    {
        $this->blackDeck = $blackDeck;

        // set the owning side of the relation if necessary
        if ($this !== $blackDeck->getGame()) {
            $blackDeck->setGame($this);
        }

        return $this;
    }

    public function getWhiteDeck(): ?WhiteDeck
    {
        return $this->whiteDeck;
    }

    public function setWhiteDeck(WhiteDeck $whiteDeck): self
    {
        $this->whiteDeck = $whiteDeck;

        // set the owning side of the relation if necessary
        if ($this !== $whiteDeck->getGame()) {
            $whiteDeck->setGame($this);
        }

        return $this;
    }

    public function getBlackCard(): ?BlackCard
    {
        return $this->blackCard;
    }

    public function setBlackCard(?BlackCard $blackCard): self
    {
        $this->blackCard = $blackCard;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        if (in_array($state, [self::STATE_SELECT_CARD, self::STATE_ELECT_CARD])) {
            $this->state = $state;
        }

        return $this;
    }
}
