<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\DiscriminatorColumn;

/**
 * @ORM\Entity()
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="class", type="string")
 */
class BlackCardReference
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $content;

    /**
     * @ORM\Column(type="integer")
     */
    protected $nbrOfBlanks;

    public function __construct($content = null, $nbrOfBlanks = 1)
    {
        $this->content = $content;
        $this->nbrOfBlanks = $nbrOfBlanks;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getNbrOfBlanks(): ?int
    {
        return $this->nbrOfBlanks;
    }

    public function setNbrOfBlanks(int $nbrOfBlanks): self
    {
        $this->nbrOfBlanks = $nbrOfBlanks;

        return $this;
    }

}
