<?php


namespace App\Controller;


use App\GithubApi\Client\ApiClient;
use App\GithubApi\Exception\UserNotFoundException;
use App\GithubApi\Exception\ValidationException;
use App\Value\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GetUser
{
    private $apiClient;
    private $validator;

    public function __construct(ApiClient $apiClient, ValidatorInterface $validator)
    {
        $this->apiClient = $apiClient;
        $this->validator = $validator;
    }

    /**
     * @Route("/users/{login}", name="get_user", methods={"get"})
     */
    public function run(Request $request)
    {
        try {
            $username = $request->get('login');

            return new JsonResponse(['data' => $this->apiClient->getUser($username)]);
        } catch (ValidationException $e) {
            return new JsonResponse(['reason' => 'API response error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (UserNotFoundException $e) {
            return new JsonResponse(['reason' => 'User with given username not found'], Response::HTTP_NOT_FOUND);

        }
    }
}
