<?php


namespace App\Controller;


use App\GithubApi\Client\ApiClient;
use App\GithubApi\Exception\RepositoryNotFoundException;
use App\GithubApi\Exception\UserNotFoundException;
use App\GithubApi\Exception\ValidationException;
use App\Value\Repository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GetRepository
{
    private $apiClient;
    private $validator;

    public function __construct(ApiClient $apiClient, ValidatorInterface $validator)
    {
        $this->apiClient = $apiClient;
        $this->validator = $validator;
    }

    /**
     * @Route("/repositories/{ownerlogin}/{repositoryname}", name="get_repository", methods={"get"})
     */
    public function run(Request $request)
    {
        try {
            $owner = $request->get('ownerlogin');
            $repo = $request->get('repositoryname');

            return new JsonResponse(['data' => $this->apiClient->getRepository($owner, $repo)]);
        } catch (ValidationException $e) {
            return new JsonResponse(['reason' => 'API response error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (RepositoryNotFoundException $e) {
            return new JsonResponse(['reason' => 'Repository with given owner or name not found'], Response::HTTP_NOT_FOUND);


        }
    }
}
