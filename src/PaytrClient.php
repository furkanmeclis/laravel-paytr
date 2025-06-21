<?php
/**
 * Laravel Paytr
 *
 * @author    Furkan Meclis
 * @copyright 2024 Furkan Meclis
 * @license   MIT
 * @link      https://github.com/furkanmeclis/laravel-paytr
 */

namespace FurkanMeclis\Paytr;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

/**
 * Bu sınıf, diğer Request sınıfları için temel bir istemci görevi görür.
 * Yapılandırma ve Guzzle istemcisini yönetir.
 */
abstract class PaytrClient
{
    protected Client $client;
    protected array $credentials;
    protected array $options;

    public function __construct(array $credentials = [], array $options = [])
    {
        // Credentials ve Options config dosyasından yüklenir
        $this->credentials = $credentials ?: config('paytr.credentials');
        $this->options = $options ?: config('paytr.options');

        $this->client = new Client([
            'base_uri' => $this->options['base_uri'] ?? 'https://www.paytr.com',
            'timeout'  => $this->options['timeout'] ?? 60,
        ]);
    }

    /**
     * @return array
     */
    public function getCredentials(): array
    {
        return $this->credentials;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $body
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function call(string $method, string $uri, array $body = []): ResponseInterface
    {
        return $this->client->request($method, $uri, [
            'form_params' => $body,
        ]);
    }
} 