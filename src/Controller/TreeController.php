<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\NodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class TreeController extends AbstractController
{
    /**
     * @var NodeRepository
     */
    private $nodeRepository;

    /**
     * @param NodeRepository $nodeRepository
     */
    public function __construct(NodeRepository $nodeRepository)
    {
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * @Route("/", name="tree")
     */
    public function index()
    {
        return $this->render('tree/index.html.twig', [
            'root' => $this->nodeRepository->findRoot(),
        ]);
    }
}
