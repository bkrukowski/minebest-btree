<?php

declare(strict_types=1);

namespace App\Tree;

use App\Entity\Node;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\IdentityGenerator;

final class NodeIdGenerator extends IdentityGenerator
{
    /**
     * {@inheritdoc}
     */
    public function generate(EntityManager $em, $entity)
    {
        /** @var Node $entity */
        if (null !== $id = $entity->getId()) {
            return $id;
        }

        return parent::generate($em, $entity);
    }
}
