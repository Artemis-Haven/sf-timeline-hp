<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WhiteCardRepository")
 */
class WhiteCard extends WhiteCardReference
{

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\WhiteDeck", inversedBy="whiteCards")
     * @ORM\JoinColumn(nullable=true)
     */
    private $whiteDeck;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Hand", inversedBy="whiteCards", fetch="EAGER")
     * @ORM\JoinColumn(nullable=true)
     */
    private $hand;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

    /**
     * @ORM\Column(type="boolean")
     */
    private $selected;

    public function __construct($content = null)
    {
        parent::__construct($content);
        $this->selected = false;
    }


    public function getWhiteDeck(): ?WhiteDeck
    {
        return $this->whiteDeck;
    }

    public function setWhiteDeck(?WhiteDeck $whiteDeck): self
    {
        $this->whiteDeck = $whiteDeck;

        return $this;
    }

    public function getHand(): ?Hand
    {
        return $this->hand;
    }

    public function setHand(?Hand $hand): self
    {
        $this->hand = $hand;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getSelected(): ?bool
    {
        return $this->selected;
    }

    public function setSelected(bool $selected): self
    {
        $this->selected = $selected;

        return $this;
    }
}
