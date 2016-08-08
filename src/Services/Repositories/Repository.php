<?php


namespace Bitbucket\Services\Repositories;

use Bitbucket\Services\Api;

class Repository extends Api
{

    /**
     * @param $account
     * @param $repo
     * @param null $filter
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function branches($account, $repo, $filter = null)
    {
        $url = sprintf('repositories/%s/%s/refs/branches', $account, $repo);
        if ($filter !== null) {
            $url .= sprintf('?q=name ~ "%s"', $filter);
        }

        return $this->requestGet($url);
    }

    /**
     * @param $account
     * @param $repo
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function commits($account, $repo)
    {
        return $this->requestGet(
            sprintf('repositories/%s/%s/commits', $account, $repo)
        );
    }

    /**
     * @param $account
     * @param $repo
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function branch($account, $repo)
    {
        return $this->requestGet(
            sprintf('repositories/%s/%s/main-branch', $account, $repo)
        );
    }

    /**
     * @param $account
     * @param $repo
     * @param null $filer
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function tags($account, $repo, $filer = null)
    {
        $url = sprintf('repositories/%s/%s/refs/tags', $account, $repo);
        if ($filer !== null) {
            $url .= sprintf('?q=name ~ "%s"', $filer);
        }

        return $this->requestGet($url);
    }
}
