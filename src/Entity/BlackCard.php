<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlackCardRepository")
 */
class BlackCard extends BlackCardReference
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BlackDeck", inversedBy="blackCards")
     */
    private $blackDeck;

    public function getBlackDeck(): ?BlackDeck
    {
        return $this->blackDeck;
    }

    public function setBlackDeck(?BlackDeck $blackDeck): self
    {
        $this->blackDeck = $blackDeck;

        return $this;
    }
}
