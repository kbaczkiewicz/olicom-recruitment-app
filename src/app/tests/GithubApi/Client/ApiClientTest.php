<?php


namespace App\Test\GithubApi\Client;


use App\GithubApi\Client\ApiClient;
use App\GithubApi\Client\RouteProvider;
use App\GithubApi\Exception\RepositoryNotFoundException;
use App\GithubApi\Exception\UserNotFoundException;
use App\GithubApi\Exception\ValidationException;
use App\Value\Repository;
use App\Value\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ApiClientTest extends TestCase
{
    public function testGetUserApiResponse()
    {
        $client = new ApiClient(
            $this->mockHttpClientForUserCall(),
            $this->mockValidatorWithoutErrors(),
            $this->mockRouteProvider()
        );

        $user = $client->getUser('kbaczkiewicz');
        $this->assertTrue($user instanceof User);
    }

    public function testGetRepoApiResponse()
    {
        $client = new ApiClient(
            $this->mockHttpClientForRepoCall(),
            $this->mockValidatorWithoutErrors(),
            $this->mockRouteProvider()
        );

        $repo = $client->getRepository('kbaczkiewicz', 'console-mirko');
        $this->assertTrue($repo instanceof Repository);
    }

    public function testThrowsExceptionOnValidationErrorsWhileCallingForUser()
    {
        $this->expectException(ValidationException::class);
        $client = new ApiClient(
            $this->mockHttpClientForUserCall(),
            $this->mockValidatorWithError(),
            $this->mockRouteProvider()
        );

        $client->getUser('kbaczkiewicz');
    }

    public function testThrowsExceptionOnValidationErrorsWhileCallingForRepo()
    {
        $this->expectException(ValidationException::class);
        $client = new ApiClient(
            $this->mockHttpClientForRepoCall(),
            $this->mockValidatorWithError(),
            $this->mockRouteProvider()
        );

        $client->getRepository('kbaczkiewicz', 'console-mirko');
    }

    public function testThrowsExceptionOnUserNotFound()
    {
        $this->expectException(UserNotFoundException::class);
        $client = new ApiClient(
            $this->mockHttpClientWithNotFoundResponse($this->getUserLink()),
            $this->mockValidatorWithoutErrors(),
            $this->mockRouteProvider()
        );

        $client->getUser('kbaczkiewicz');
    }

    public function testThrowsExceptionOnRepositoryNotFound()
    {
        $this->expectException(RepositoryNotFoundException::class);
        $client = new ApiClient(
            $this->mockHttpClientWithNotFoundResponse($this->getRepoLink()),
            $this->mockValidatorWithoutErrors(),
            $this->mockRouteProvider()
        );

        $client->getRepository('kbaczkiewicz', 'console-mirko');
    }

    private function mockHttpClientForRepoCall(): MockObject
    {
        $mock = $this->createMock(HttpClientInterface::class);
        $mock
            ->expects($this->any())
            ->method('request')
            ->with('GET', $this->getRepoLink())
            ->will($this->returnValue($this->mockRepoResponse()));

        return $mock;
    }

    private function mockHttpClientForUserCall(): MockObject
    {
        $mock = $this->createMock(HttpClientInterface::class);
        $mock
            ->expects($this->any())
            ->method('request')
            ->with('GET', $this->getUserLink())
            ->will($this->returnValue($this->mockUserResponse()));

        return $mock;
    }

    private function mockHttpClientWithNotFoundResponse(string $link): MockObject
    {
        $mock = $this->createMock(HttpClientInterface::class);
        $mock
            ->expects($this->any())
            ->method('request')
            ->with('GET', $link)
            ->will($this->returnValue($this->mockNotFoundResponse()));

        return $mock;
    }

    private function mockUserResponse(): MockObject
    {
        $mock = $this->createMock(ResponseInterface::class);
        $mock
            ->expects($this->once())
            ->method('getContent')
            ->with()
            ->will($this->returnValue($this->getUserGETResponse()));

        $mock
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        return $mock;
    }

    private function mockRepoResponse(): MockObject
    {
        $mock = $this->createMock(ResponseInterface::class);
        $mock
            ->expects($this->once())
            ->method('getContent')
            ->with()
            ->will($this->returnValue($this->getRepoGETResponse()));

        $mock
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        return $mock;
    }

    private function mockNotFoundResponse(): MockObject
    {
        $mock = $this->createMock(ResponseInterface::class);
        $mock
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(404);

        return $mock;
    }

    private function mockValidatorWithoutErrors(): MockObject
    {
        $mock = $this->createMock(ValidatorInterface::class);
        $mock
            ->expects($this->any())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        return $mock;
    }

    private function mockValidatorWithError(): MockObject
    {
        $mock = $this->createMock(ValidatorInterface::class);
        $mock
            ->expects($this->once())
            ->method('validate')
            ->willReturn(
                new ConstraintViolationList(
                    [
                        new ConstraintViolation(
                            'test',
                            null,
                            [],
                            'test',
                            null,
                            null
                        ),
                    ]
                )
            );

        return $mock;
    }

    private function mockRouteProvider(): MockObject
    {
        $mock = $this->createMock(RouteProvider::class);
        $mock
            ->expects($this->any())
            ->method('getUserGETRoute')
            ->will($this->returnValue($this->getUserLink()));

        $mock
            ->expects($this->any())
            ->method('getRepoGETRoute')
            ->will($this->returnValue($this->getRepoLink()));

        return $mock;
    }

    private function getUserLink(): string
    {
        return 'https://api.github.com/users/kbaczkiewicz';
    }

    private function getRepoLink(): string
    {
        return 'https://api.github.com/repos/kbaczkiewicz/console-mirko';
    }

    private function getUserGETResponse(): string
    {
        return '{
          "login": "kbaczkiewicz",
          "id": 17267724,
          "node_id": "MDQ6VXNlcjE3MjY3NzI0",
          "avatar_url": "https://avatars3.githubusercontent.com/u/17267724?v=4",
          "gravatar_id": "",
          "url": "https://api.github.com/users/kbaczkiewicz",
          "html_url": "https://github.com/kbaczkiewicz",
          "followers_url": "https://api.github.com/users/kbaczkiewicz/followers",
          "following_url": "https://api.github.com/users/kbaczkiewicz/following{/other_user}",
          "gists_url": "https://api.github.com/users/kbaczkiewicz/gists{/gist_id}",
          "starred_url": "https://api.github.com/users/kbaczkiewicz/starred{/owner}{/repo}",
          "subscriptions_url": "https://api.github.com/users/kbaczkiewicz/subscriptions",
          "organizations_url": "https://api.github.com/users/kbaczkiewicz/orgs",
          "repos_url": "https://api.github.com/users/kbaczkiewicz/repos",
          "events_url": "https://api.github.com/users/kbaczkiewicz/events{/privacy}",
          "received_events_url": "https://api.github.com/users/kbaczkiewicz/received_events",
          "type": "User",
          "site_admin": false,
          "name": "K Bączkiewicz",
          "company": null,
          "blog": "",
          "location": null,
          "email": null,
          "hireable": null,
          "bio": null,
          "twitter_username": null,
          "public_repos": 10,
          "public_gists": 0,
          "followers": 0,
          "following": 0,
          "created_at": "2016-02-16T08:22:03Z",
          "updated_at": "2020-07-14T08:48:45Z"
        }';
    }

    private function getRepoGETResponse(): string
    {
        return '{
            "id": 112575992,
            "node_id": "MDEwOlJlcG9zaXRvcnkxMTI1NzU5OTI=",
            "name": "console-mirko",
            "full_name": "kbaczkiewicz/console-mirko",
            "private": false,
            "owner": {
              "login": "kbaczkiewicz",
              "id": 17267724,
              "node_id": "MDQ6VXNlcjE3MjY3NzI0",
              "avatar_url": "https://avatars3.githubusercontent.com/u/17267724?v=4",
              "gravatar_id": "",
              "url": "https://api.github.com/users/kbaczkiewicz",
              "html_url": "https://github.com/kbaczkiewicz",
              "followers_url": "https://api.github.com/users/kbaczkiewicz/followers",
              "following_url": "https://api.github.com/users/kbaczkiewicz/following{/other_user}",
              "gists_url": "https://api.github.com/users/kbaczkiewicz/gists{/gist_id}",
              "starred_url": "https://api.github.com/users/kbaczkiewicz/starred{/owner}{/repo}",
              "subscriptions_url": "https://api.github.com/users/kbaczkiewicz/subscriptions",
              "organizations_url": "https://api.github.com/users/kbaczkiewicz/orgs",
              "repos_url": "https://api.github.com/users/kbaczkiewicz/repos",
              "events_url": "https://api.github.com/users/kbaczkiewicz/events{/privacy}",
              "received_events_url": "https://api.github.com/users/kbaczkiewicz/received_events",
              "type": "User",
              "site_admin": false
            },
            "html_url": "https://github.com/kbaczkiewicz/console-mirko",
            "description": null,
            "fork": false,
            "url": "https://api.github.com/repos/kbaczkiewicz/console-mirko",
            "forks_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/forks",
            "keys_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/keys{/key_id}",
            "collaborators_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/collaborators{/collaborator}",
            "teams_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/teams",
            "hooks_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/hooks",
            "issue_events_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/issues/events{/number}",
            "events_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/events",
            "assignees_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/assignees{/user}",
            "branches_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/branches{/branch}",
            "tags_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/tags",
            "blobs_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/git/blobs{/sha}",
            "git_tags_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/git/tags{/sha}",
            "git_refs_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/git/refs{/sha}",
            "trees_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/git/trees{/sha}",
            "statuses_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/statuses/{sha}",
            "languages_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/languages",
            "stargazers_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/stargazers",
            "contributors_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/contributors",
            "subscribers_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/subscribers",
            "subscription_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/subscription",
            "commits_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/commits{/sha}",
            "git_commits_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/git/commits{/sha}",
            "comments_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/comments{/number}",
            "issue_comment_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/issues/comments{/number}",
            "contents_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/contents/{+path}",
            "compare_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/compare/{base}...{head}",
            "merges_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/merges",
            "archive_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/{archive_format}{/ref}",
            "downloads_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/downloads",
            "issues_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/issues{/number}",
            "pulls_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/pulls{/number}",
            "milestones_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/milestones{/number}",
            "notifications_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/notifications{?since,all,participating}",
            "labels_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/labels{/name}",
            "releases_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/releases{/id}",
            "deployments_url": "https://api.github.com/repos/kbaczkiewicz/console-mirko/deployments",
            "created_at": "2017-11-30T06:53:21Z",
            "updated_at": "2017-11-30T06:53:54Z",
            "pushed_at": "2017-11-30T14:06:01Z",
            "git_url": "git://github.com/kbaczkiewicz/console-mirko.git",
            "ssh_url": "git@github.com:kbaczkiewicz/console-mirko.git",
            "clone_url": "https://github.com/kbaczkiewicz/console-mirko.git",
            "svn_url": "https://github.com/kbaczkiewicz/console-mirko",
            "homepage": null,
            "size": 6,
            "stargazers_count": 0,
            "watchers_count": 0,
            "language": "Python",
            "has_issues": true,
            "has_projects": true,
            "has_downloads": true,
            "has_wiki": true,
            "has_pages": false,
            "forks_count": 0,
            "mirror_url": null,
            "archived": false,
            "disabled": false,
            "open_issues_count": 0,
            "license": null,
            "forks": 0,
            "open_issues": 0,
            "watchers": 0,
            "default_branch": "master",
            "temp_clone_token": null,
            "network_count": 0,
            "subscribers_count": 1
         }';
    }
}
