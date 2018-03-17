<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018. 03. 16.
 * Time: 23:49
 */

namespace OnIt\PythonBackend\Repository;

use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use Psr\Http\Message\ResponseInterface;


class PythonBackendRepository
{
    const BASE_URI = 'http://0.0.0.0:5004';

    /** @var Client */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param UploadedFile $file
     *
     * @return ResponseInterface
     */
    public function detect($file)
    {
        return $this->client->request(
            'POST',
            '/detect',
            [
                'multipart' =>
                    [
                        [
                            'name' => 'file',
                            'contents' => file_get_contents($file->path()),
                            'filename' => 'file.jpg'
                        ]
                    ]
            ]
        );
    }

    /**
     * @param array $data
     *
     * @return mixed|ResponseInterface
     */
    public function train(array $data)
    {
        return $this->client->request(
            'POST',
            'train',
            [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode($data),
            ]

        );
    }

    /**
     * @param array $encoding
     *
     * @return mixed|ResponseInterface
     */
    public function recognize(array $encoding)
    {
        return $this->client->request(
            'POST',
            'recognize',
            [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode($encoding),
            ]
        );
    }
}