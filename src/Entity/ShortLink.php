<?php

namespace App\Entity;

use App\Repository\ShortLinkRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ShortLinkRepository::class)
 * @ORM\Table(name="short_link", indexes={
 *   @ORM\Index(name="hash_expires_at_idx", columns={"hash", "expires_at"})
 * })
 */
class ShortLink
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hash;

    /**
     * @ORM\Column(type="text")
     */
    private $url;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $clicks;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expires_at;

    const HASH_LENGTH = 6;


    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->clicks = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getClicks(): ?int
    {
        return $this->clicks;
    }

    public function setClicks(int $clicks): self
    {
        $this->clicks = $clicks;

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTime $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getExpiresAt(): ?DateTime
    {
        return $this->expires_at;
    }

    public function setExpiresAt($expires_at): self
    {
        $this->expires_at = $expires_at;

        return $this;
    }
}
