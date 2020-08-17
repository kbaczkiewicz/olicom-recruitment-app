<?php


namespace App\GithubApi\Client;


use App\GithubApi\Exception\RepositoryNotFoundException;
use App\GithubApi\Exception\UserNotFoundException;
use App\GithubApi\Exception\ValidationException;
use App\Value\Email;
use App\Value\Repository;
use App\Value\Url;
use App\Value\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiClient
{
    private $httpClient;
    private $validator;
    private $routeProvider;

    public function __construct(
        HttpClientInterface $httpClient,
        ValidatorInterface $validator,
        RouteProvider $routeProvider
    ) {
        $this->httpClient = $httpClient;
        $this->validator = $validator;
        $this->routeProvider = $routeProvider;
    }

    public function getUser(string $username): User
    {
        $apiResponse = $this->httpClient->request('GET', $this->routeProvider->getUserGETRoute($username));
        if (Response::HTTP_NOT_FOUND === $apiResponse->getStatusCode()) {
            throw new UserNotFoundException('User not found');
        }

        $userResponse = \App\GithubApi\Response\User::fromArray(json_decode($apiResponse->getContent(), true));
        $errors = $this->validator->validate($userResponse);
        if (count($errors) > 0) {
            throw new ValidationException('API Response errors');
        }

        return new User(
            $userResponse->getName(),
            new Url($userResponse->getUrl()),
            \DateTime::createFromFormat(
                'Y-m-d\TH:i:s\Z',
                $userResponse->getCreatedAt()
            ),
            $userResponse->getEmail() ? new Email($userResponse->getEmail()) : null
        );
    }

    public function getRepository(string $owner, string $repoName): Repository
    {
        $apiResponse = $this->httpClient->request('GET', $this->routeProvider->getRepoGETRoute($owner, $repoName));
        if (Response::HTTP_NOT_FOUND === $apiResponse->getStatusCode()) {
            throw new RepositoryNotFoundException('Repository not found');
        }

        $repoResponse = \App\GithubApi\Response\Repository::fromArray(json_decode($apiResponse->getContent(), true));
        $errors = $this->validator->validate($repoResponse);
        if (count($errors) > 0) {
            throw new ValidationException('API response error');
        }

        return new Repository(
            $repoResponse->getFullName(),
            new Url($repoResponse->getCloneUrl()),
            $repoResponse->getStars(),
            \DateTime::createFromFormat('Y-m-d\TH:i:s\Z', $repoResponse->getCreatedAt()),
            $repoResponse->getDescription()
        );
    }
}
