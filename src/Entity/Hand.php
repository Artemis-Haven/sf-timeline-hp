<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HandRepository")
 */
class Hand
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="hands")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="hands")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WhiteCard", mappedBy="hand", cascade={"persist", "remove"})
     */
    private $whiteCards;

    /**
     * @ORM\Column(type="integer")
     */
    private $points;

    public function __construct()
    {
        $this->whiteCards = new ArrayCollection();
        $this->points = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection|WhiteCard[]
     */
    public function getWhiteCards(): Collection
    {
        return $this->whiteCards;
    }
    public function getCards(): Collection
    {
        return $this->getWhiteCards();
    }

    public function getSelectedCards(): Collection
    {
        $collection = $this->getWhiteCards()->filter(function($card) {
            return $card->getSelected();
        });
        $iterator = $collection->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
        });
        return new ArrayCollection(iterator_to_array($iterator));
    }

    public function addWhiteCard(WhiteCard $whiteCard): self
    {
        if (!$this->whiteCards->contains($whiteCard)) {
            $this->whiteCards[] = $whiteCard;
            $whiteCard->setHand($this);
        }

        return $this;
    }

    public function removeWhiteCard(WhiteCard $whiteCard): self
    {
        if ($this->whiteCards->contains($whiteCard)) {
            $this->whiteCards->removeElement($whiteCard);
            // set the owning side to null (unless already changed)
            if ($whiteCard->getHand() === $this) {
                $whiteCard->setHand(null);
            }
        }

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function addOnePoint(): self
    {
        $this->points++;

        return $this;
    }

    public function areCardsSubmitted(): bool
    {
        $nbrOfSelectedCards = 0;
        foreach ($this->getCards() as $card) {
            if ($card->getSelected()) {
                $nbrOfSelectedCards++;
            }
        }
        return ($nbrOfSelectedCards == $this->getGame()->getBlackCard()->getNbrOfBlanks());
    }
}
