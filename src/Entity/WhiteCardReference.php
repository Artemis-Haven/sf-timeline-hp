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
class WhiteCardReference
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

    public function __construct($content = null)
    {
        $this->content = $content;
    }

    public function __toString()
    {
        return $this->content;
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
}
