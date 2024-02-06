<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;


class Dashing
{
    private string $host = "";
    private int $port = 0;
    private string $token = "";
    public function __construct(
        private ContainerBagInterface $container
    )
    {
        $this->setHost($container->get('dashing.host'));
        $this->setPort($container->get('dashing.port'));
        $this->setToken($container->get('dashing.token'));
    }

    public function setHost(string $host): static
    {
        $this->host = $host;

        return $this;
    }

    public function setPort(int $port): static
    {
        $this->port = $port;

        return $this;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getUrl($uri)
    {
        return sprintf('%s:%s%s',
            $this->host,
            $this->port,
            $uri
        );
    }

    public function send(string $uri, array $data): int
    {
        $data['auth_token'] = $this->token;

        $url = $this->getUrl($uri);

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $d = json_encode($data));
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        curl_exec($curl);

        $infos = curl_getinfo($curl);
        $http_code = $infos['http_code'];
        if (204 != $http_code)
        {
            throw new \Exception(sprintf('Unable to post to smasing, got http code %d, not 204', $http_code));
        }

        return 0;
    }

}
