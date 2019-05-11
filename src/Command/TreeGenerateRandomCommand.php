<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Node;
use App\Tree\RandomTreeGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

final class TreeGenerateRandomCommand extends Command
{
    private const DEFAULT_DEPTH = 5;

    private const DEFAULt_OUTPUT = 'example.yaml';

    protected static $defaultName = 'tree:generate-random';

    /**
     * @var RandomTreeGenerator
     */
    private $generator;

    /**
     * @param RandomTreeGenerator $randomTreeGenerator
     */
    public function __construct(RandomTreeGenerator $randomTreeGenerator)
    {
        $this->generator = $randomTreeGenerator;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Generate random tree')
            ->addOption('depth', 'd', InputOption::VALUE_OPTIONAL, 'Depth', self::DEFAULT_DEPTH)
            ->addOption('output', 'o', InputOption::VALUE_OPTIONAL, 'Output file', self::DEFAULt_OUTPUT)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $depth = (int) $input->getOption('depth');
        $output = $input->getOption('output');

        if ($depth < 1) {
            throw new \InvalidArgumentException(\sprintf('Depth must greater or equal to 1, %d given', $depth));
        }

        $tree = $this->generator->generate($depth);

        $resultArray = [];
        $this->treeWalk(
            $tree,
            function (Node $node) use (&$resultArray) {
                $resultArray[] = $this->nodeToArray($node);
            }
        );

        $result = Yaml::dump($resultArray);

        $io->title('Random tree');
        if ($io->isVerbose()) {
            $io->writeln($result);
        }

        $fs = new Filesystem();
        if ($fs->exists($output)) {
            throw new \RuntimeException(\sprintf('File `%s` already exists', $output));
        }
        $fs->dumpFile($output, $result);
        $io->writeln(\sprintf('Printed to file `%s`', $output));
    }

    private function treeWalk(Node $tree, callable $fn)
    {
        $fn($tree);
        if (null !== $left = $tree->getLeftNode()) {
            $this->treeWalk($left, $fn);
        }
        if (null !== $right = $tree->getRightNode()) {
            $this->treeWalk($right, $fn);
        }
    }

    private function nodeToArray(Node $node): array
    {
        return [
            'id' => $node->getId(),
            'username' => $node->getUsername(),
            'leftCredits' => $node->getLeftCredits(),
            'rightCredits' => $node->getRightCredits(),
            'leftNodeId' => $node->getLeftNode() ? $node->getLeftNode()->getId() : null,
            'rightNodeId' => $node->getRightNode() ? $node->getRightNode()->getId() : null,
            'is_root' => $node->isRoot(),
        ];
    }
}
