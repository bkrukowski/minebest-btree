<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Node;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Node|null find($id, $lockMode = null, $lockVersion = null)
 * @method Node|null findOneBy(array $criteria, array $orderBy = null)
 * @method Node[]    findAll()
 * @method Node[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class NodeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Node::class);
    }

    /**
     * @param Node[]|iterable $nodes
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function saveNodes(iterable $nodes)
    {
        $connection = $this->getEntityManager()->getConnection();
        $connection->beginTransaction();
        foreach ($nodes as $node) {
            if (!$node instanceof Node) {
                throw new \InvalidArgumentException(\sprintf(
                    'Method %s::%s expects %s[], %d given in iterable',
                    static::class,
                    __FUNCTION__,
                    Node::class,
                    \is_object($node) ? \get_class($node) : \gettype($node)
                ));
            }
            $this->getEntityManager()->persist($node);
        }
        $this->getEntityManager()->flush();
        $connection->commit();
    }

    public function isEmpty(): bool
    {
        $r = $this->createQueryBuilder('n')
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;

        return empty($r);
    }

    public function findRoot(): ?Node
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.root = :root')
            ->setParameter('root', true)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
