<?php


namespace App\GithubApi\Client;


class RouteProvider
{
    public function getUserGETRoute(string $username): string
    {
        return sprintf( 'https://api.github.com/users/%s', $username);
    }

    public function getRepoGETRoute($owner, $repo): string
    {
        return sprintf( 'https://api.github.com/repos/%s/%s', $owner, $repo);
    }
}
