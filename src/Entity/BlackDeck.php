<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlackDeckRepository")
 */
class BlackDeck
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Game", inversedBy="blackDeck", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BlackCard", mappedBy="blackDeck")
     */
    private $blackCards;

    public function __construct()
    {
        $this->blackCards = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    /**
     * @return Collection|BlackCard[]
     */
    public function getBlackCards(): Collection
    {
        return $this->blackCards;
    }
    public function getCards(): Collection
    {
        return $this->getBlackCards();
    }

    public function addBlackCard(BlackCard $blackCard): self
    {
        if (!$this->blackCards->contains($blackCard)) {
            $this->blackCards[] = $blackCard;
            $blackCard->setBlackDeck($this);
        }

        return $this;
    }

    public function addCard(BlackCard $blackCard): self
    {
        return $this->addBlackCard($blackCard);
    }

    public function removeBlackCard(BlackCard $blackCard): self
    {
        if ($this->blackCards->contains($blackCard)) {
            $this->blackCards->removeElement($blackCard);
            // set the owning side to null (unless already changed)
            if ($blackCard->getBlackDeck() === $this) {
                $blackCard->setBlackDeck(null);
            }
        }

        return $this;
    }

    public function removeCard(BlackCard $blackCard): self
    {
        return $this->removeBlackCard($blackCard);
    }

    public function popCard(): BlackCard
    {
        $card = $this->getBlackCards()->first();
        $this->removeBlackCard($card);
        return $card;
    }
}
