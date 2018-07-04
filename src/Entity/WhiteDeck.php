<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WhiteDeckRepository")
 */
class WhiteDeck
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Game", inversedBy="whiteDeck", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WhiteCard", mappedBy="whiteDeck")
     */
    private $whiteCards;

    public function __construct()
    {
        $this->whiteCards = new ArrayCollection();
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
     * @return Collection|WhiteCard[]
     */
    public function getWhiteCards(): Collection
    {
        return $this->whiteCards;
    }

    /**
     * @return Collection|WhiteCard[]
     */
    public function getCards(): Collection
    {
        return $this->getWhiteCards();
    }

    public function addWhiteCard(WhiteCard $whiteCard): self
    {
        if (!$this->whiteCards->contains($whiteCard)) {
            $this->whiteCards[] = $whiteCard;
            $whiteCard->setWhiteDeck($this);
        }

        return $this;
    }

    public function addCard(WhiteCard $whiteCard): self
    {
        return $this->addWhiteCard($whiteCard);
    }

    public function removeWhiteCard(WhiteCard $whiteCard): self
    {
        if ($this->whiteCards->contains($whiteCard)) {
            $this->whiteCards->removeElement($whiteCard);
            // set the owning side to null (unless already changed)
            if ($whiteCard->getWhiteDeck() === $this) {
                $whiteCard->setWhiteDeck(null);
            }
        }

        return $this;
    }

    public function removeCard(WhiteCard $whiteCard): self
    {
        return $this->removeWhiteCard($whiteCard);
    }

    public function popCard(): WhiteCard
    {
        $card = $this->getWhiteCards()->first();
        $this->removeWhiteCard($card);
        return $card;
    }
}
