<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Node;
use App\Repository\NodeRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

final class TreeImportCommand extends Command
{
    protected static $defaultName = 'tree:import';

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
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('input', InputArgument::REQUIRED, 'Input file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $input = $input->getArgument('input');
        if (!\file_exists($input)) {
            throw new \RuntimeException(\sprintf('File does not exist [%s]', $input));
        }

        if (!$this->nodeRepository->isEmpty()) {
            throw new \RuntimeException('Cannot import data, repository is not empty');
        }

        $output->writeln('Importing data...');
        $progressBar = new ProgressBar($output);
        $nodes = $this->fileToNodes($input, $progressBar);
        $this->nodeRepository->saveNodes($nodes);
        $progressBar->finish();
        $output->writeln('');
        $output->writeln('Successfully imported');
    }

    /**
     * @param string      $file
     * @param ProgressBar $progressBar
     *
     * @return iterable|Node
     */
    private function fileToNodes(string $file, ProgressBar $progressBar): iterable
    {
        $arrayNodes = \array_reverse(Yaml::parseFile($file));
        $progressBar->start(\count($arrayNodes));
        $processedNodes = [];
        foreach ($arrayNodes as $arrayNode) {
            $node = new Node();

            (function (Node $node, int $id) {
                $node->id = $id;
            })->bindTo(null, Node::class)($node, $arrayNode['id']);

            $node->setUsername($arrayNode['username']);
            $node->setLeftCredits($arrayNode['leftCredits']);
            $node->setRightCredits($arrayNode['rightCredits']);
            $node->setRoot($arrayNode['is_root']);

            if (null !== $leftId = $arrayNode['leftNodeId']) {
                $node->setLeftNode($processedNodes[$leftId]);
            }
            if (null !== $rightId = $arrayNode['rightNodeId']) {
                $node->setRightNode($processedNodes[$rightId]);
            }

            yield $node;

            $processedNodes[$node->getId()] = $node;
            $progressBar->advance();
        }
    }
}
