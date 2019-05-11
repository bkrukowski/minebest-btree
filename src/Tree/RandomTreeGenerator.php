<?php

declare(strict_types=1);

namespace App\Tree;

use App\Entity\Node;

final class RandomTreeGenerator
{
    private $autoincrement = 1;

    /**
     * @var array
     */
    private $nameParts;

    /**
     * @var int
     */
    private $probHavingChildren;

    /**
     * @param array $nameParts
     * @param int   $probHavingChildren
     */
    public function __construct(array $nameParts, int $probHavingChildren)
    {
        $this->nameParts = $nameParts;
        $this->probHavingChildren = $probHavingChildren;
    }

    public function generate(int $depth): Node
    {
        if ($depth < 1) {
            throw new \InvalidArgumentException(\sprintf('Depth must greater or equal to 1, %d given', $depth));
        }

        $result = $this->createRandomNode($depth, 1);
        $result->setRoot(true);

        return $result;
    }

    private function createRandomNode(int $maxDepth, int $currentDepth)
    {
        $node = new Node();

        (function (Node $node, int $id) {
            $node->id = $id;
        })->bindTo(null, Node::class)($node, $this->getNewId());

        $node->setUsername($this->randomUsername());
        $node->setLeftCredits($this->randomCredits());
        $node->setRightCredits($this->randomCredits());
        $node->setRoot(false);

        if ($maxDepth > $currentDepth) {
            if ($this->randomBool($this->probHavingChildren)) {
                $node->setLeftNode($this->createRandomNode($maxDepth, $currentDepth + 1));
            }
            if ($this->randomBool($this->probHavingChildren)) {
                $node->setRightNode($this->createRandomNode($maxDepth, $currentDepth + 1));
            }
        }

        return $node;
    }

    private function randomBool(int $positivePercentage): bool
    {
        return $positivePercentage > \mt_rand(0, 99);
    }

    private function randomUsername(): string
    {
        $result = '';
        $first = true;
        foreach ($this->nameParts as $array) {
            \shuffle($array);
            $part = $array[0];
            if (!$first) {
                $part = \ucfirst($part);
            }
            $result .= $part;
            $first = false;
        }

        return $result.\mt_rand(80, 99);
    }

    private function randomCredits(): int
    {
        return \mt_rand(0, 10000);
    }

    private function getNewId(): int
    {
        return $this->autoincrement++;
    }
}
