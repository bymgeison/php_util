<?php

namespace GX4\Util;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

/**
 * Class TS3
 *
 * Responsável por manipular arquivos no Amazon S3.
 */
class TS3
{
    protected $awsKey;
    protected $awsSecretKey;
    protected $s3Client;

    /**
     * TS3 constructor.
     *
     * @param string $awsKey
     * @param string $awsSecretKey
     * @param string $endpoint
     * @param bool $usePathStyle
     * @param string $region
     */
    public function __construct($awsKey, $awsSecretKey, $endpoint = 'http://s3.us-east-1.amazonaws.com/', $usePathStyle = true, $region = 'us-east-1')
    {
        $this->awsKey = $awsKey;
        $this->awsSecretKey = $awsSecretKey;

        $this->s3Client = new S3Client([
            'region' => $region,
            'version' => 'latest',
            'endpoint' => $endpoint,
            'use_path_style_endpoint' => $usePathStyle,
            'credentials' => [
                'key' => $this->awsKey,
                'secret' => $this->awsSecretKey,
            ],
        ]);
    }

    /**
     * Envia um arquivo local para o S3.
     *
     * @param string $bucket
     * @param string $key
     * @param string $sourceFile
     * @param bool $deleteAfter
     */
    public function uploadFile($bucket, $key, $sourceFile, $deleteAfter = true)
    {
        $this->s3Client->putObject([
            'Bucket' => $bucket,
            'Key' => $key,
            'SourceFile' => $sourceFile,
            'ACL' => 'public-read',
        ]);

        if ($deleteAfter) {
            unlink($sourceFile);
        }
    }

    /**
     * Baixa um arquivo do S3 e salva localmente.
     *
     * @param string $bucket
     * @param string $key
     * @param string $filename
     * @param string $savePath
     */
    public function downloadFile($bucket, $key, $filename, $savePath)
    {
        $object = $this->s3Client->getObject(['Bucket' => $bucket, 'Key' => $key]);
        file_put_contents($savePath . $filename, $object['Body']->getContents());
    }

    /**
     * Exclui um arquivo do S3.
     *
     * @param string $bucket
     * @param string $key
     */
    public function deleteFile($bucket, $key)
    {
        $this->s3Client->deleteObject(['Bucket' => $bucket, 'Key' => $key]);
    }

    /**
     * Lista arquivos dentro de um diretório no S3.
     *
     * @param string $bucket
     * @param string $prefix
     * @return array
     */
    public function listDirectory($bucket, $prefix)
    {
        $response = $this->s3Client->listObjects(['Bucket' => $bucket, 'Prefix' => $prefix]);
        return $response->getPath('Contents');
    }

    /**
     * Verifica se um arquivo existe no S3.
     *
     * @param string $bucket
     * @param string $key
     * @return bool
     */
    public function fileExists($bucket, $key)
    {
        try {
            $response = $this->s3Client->getObject([
                'Bucket' => $bucket,
                'Key' => $key,
            ]);

            return $response !== null;
        } catch (S3Exception $e) {
            if ($e->getAwsErrorCode() === 'NoSuchKey') {
                return false;
            }
        }

        return false;
    }
}
