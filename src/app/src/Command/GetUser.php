<?php


namespace App\Command;


use App\GithubApi\Client\ApiClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetUser extends Command
{
    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        parent::__construct();
        $this->apiClient = $apiClient;
    }

    protected function configure()
    {
        $this->setName('github:user:get')
            ->addArgument('username', InputArgument::REQUIRED, 'Username to search in GitHub API')
            ->setDescription('Get info about GitHub user')
            ->setHelp("The return data is presented as JSON");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $username = $input->getArgument('username');
            $user = $this->apiClient->getUser($username);
            $output->writeln(json_encode($user->jsonSerialize()));

            return self::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln("<error>".$e->getMessage()."</error>");

            return self::FAILURE;
        }
    }
}
