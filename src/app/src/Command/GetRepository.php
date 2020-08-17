<?php


namespace App\Command;


use App\GithubApi\Client\ApiClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetRepository extends Command
{
    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        parent::__construct();
        $this->apiClient = $apiClient;
    }

    protected function configure()
    {
        $this->setName('github:repo:get')
            ->addArgument('owner', InputArgument::REQUIRED, 'Owner of the repository')
            ->addArgument('repo', InputArgument::REQUIRED, 'Name of the repository')
            ->setDescription('Get info about GitHub repository')
            ->setHelp("The return data is presented as JSON");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $owner = $input->getArgument('owner');
            $repoName = $input->getArgument('repo');
            $repository = $this->apiClient->getRepository($owner, $repoName);
            $output->writeln(json_encode($repository->jsonSerialize()));

            return self::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("<error>".$e->getMessage()."</error>");

            return self::FAILURE;
        }
    }
}
