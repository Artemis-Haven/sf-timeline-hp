<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Game", mappedBy="members")
     */
    private $games;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Hand", mappedBy="Owner", orphanRemoval=true)
     */
    private $hands;

    public function __construct()
    {
        parent::__construct();
        $this->games = new ArrayCollection();
        $this->hands = new ArrayCollection();
        // your own logic
    }

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->addMember($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->contains($game)) {
            $this->games->removeElement($game);
            $game->removeMember($this);
        }

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
    public function getHand(Game $game): Hand
    {
        foreach ($this->hands as $hand) {
            if ($hand->getGame() == $game) {
                return $hand;
            }
        }
        return null;
    }

    public function addHand(Hand $hand): self
    {
        if (!$this->hands->contains($hand)) {
            $this->hands[] = $hand;
            $hand->setOwner($this);
        }

        return $this;
    }

    public function removeHand(Hand $hand): self
    {
        if ($this->hands->contains($hand)) {
            $this->hands->removeElement($hand);
            // set the owning side to null (unless already changed)
            if ($hand->getOwner() === $this) {
                $hand->setOwner(null);
            }
        }

        return $this;
    }

}
