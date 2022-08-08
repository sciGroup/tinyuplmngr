<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 10.07.2015 18:07
 */

namespace SciGroup\TinymcePluploadFileManagerBundle\Command;

use SciGroup\TinymcePluploadFileManagerBundle\Doctrine\ContentFileManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GarbageCollectorCommand extends Command
{
    private ContentFileManager $contentFileManager;

    public function __construct(ContentFileManager $contentFileManager)
    {
        $this->contentFileManager = $contentFileManager;

        parent::__construct();
    }

    /**
     * @see Command
     */
    protected function configure(): void
    {
        $this
            ->setName('scigroup:tpfm:garbage-collector')
            ->setDescription('Remove old garbage files')
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->contentFileManager->removeGarbageFiles();

        return 0;
    }
}