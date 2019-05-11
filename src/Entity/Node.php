<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NodeRepository")
 * @ORM\Table(indexes={
 *      @ORM\Index(name="is_root", columns={"is_root"}),
 * })
 */
class Node
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="App\Tree\NodeIdGenerator"))
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="integer")
     */
    private $leftCredits;

    /**
     * @ORM\Column(type="integer")
     */
    private $rightCredits;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Node", cascade={"persist", "remove"}, fetch="EXTRA_LAZY")
     */
    private $leftNode;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Node", cascade={"persist", "remove"}, fetch="EXTRA_LAZY")
     */
    private $rightNode;

    /**
     * @ORM\Column(type="boolean", name="is_root")
     */
    private $root;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getLeftCredits(): ?int
    {
        return $this->leftCredits;
    }

    public function setLeftCredits(int $leftCredits): self
    {
        $this->leftCredits = $leftCredits;

        return $this;
    }

    public function getRightCredits(): ?int
    {
        return $this->rightCredits;
    }

    public function setRightCredits(int $rightCredits): self
    {
        $this->rightCredits = $rightCredits;

        return $this;
    }

    public function getLeftNode(): ?self
    {
        return $this->leftNode;
    }

    public function setLeftNode(?self $leftNode): self
    {
        $this->leftNode = $leftNode;

        return $this;
    }

    public function getRightNode(): ?self
    {
        return $this->rightNode;
    }

    public function setRightNode(?self $rightNode): self
    {
        $this->rightNode = $rightNode;

        return $this;
    }

    public function isRoot(): ?bool
    {
        return $this->root;
    }

    public function setRoot(bool $isRoot): self
    {
        $this->root = $isRoot;

        return $this;
    }
}
