<?php


namespace Cyw139\Upyun;

use League\Flysystem\Config;
use League\Flysystem\Adapter\AbstractAdapter;
use Upyun\Upyun;

class UpyunAdapter extends AbstractAdapter
{
    const UPLOAD_PICTURE = 1;
    const UPLOAD_AUDIO = 2;
    const UPLOAD_VIDEO = 3;

    protected $serviceName;
    protected $operator;
    protected $password;
    protected $domain;
    protected $protocol;

    public function __construct($serviceName, $operator, $password, $domain, $protocol = 'http')
    {
        $this->serviceName = $serviceName;
        $this->operator = $operator;
        $this->password = $password;
        $this->domain = $domain;
        $this->protocol = $protocol;
    }

    protected function client()
    {
        $config = new \Upyun\Config($this->serviceName, $this->operator, $this->password);
        $config->useSsl = config('filesystems.disks.upyun.protocol') === 'https' ? true : false;
        return new Upyun($config);
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function write($path, $contents, Config $config)
    {
        $withAsyncProcess = $config->has('withAsyncProcess') ? $config->get('withAsyncProcess') : false;
        $params = $config->has('params') ? $config->get('params') : [];
        return $this->client()->write($path, $contents, $params, $withAsyncProcess);
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function writeStream($path, $resource, Config $config)
    {
        $withAsyncProcess = $config->has('withAsyncProcess') ? $config->get('withAsyncProcess') : false;
        $params = $config->has('params') ? $config->get('params') : [];
        return $this->client()->write($path, $resource, $params, $withAsyncProcess);
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function update($path, $contents, Config $config)
    {
        return $this->write($path, $contents, $config);
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function updateStream($path, $resource, Config $config)
    {
        return $this->writeStream($path, $resource, $config);
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function rename($path, $newpath): bool
    {
        return $this->client()->move($path, $newpath);
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function copy($path, $newpath): bool
    {
        return $this->client()->copy($path, $newpath);
    }
    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function move($path, $newpath): bool
    {
        return $this->client()->move($path, $newpath);
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function delete($path)
    {
        return $this->client()->delete($path);
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function deleteDir($dirname)
    {
        return $this->client()->deleteDir($dirname);
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function createDir($dirname, Config $config)
    {
        return $this->client()->createDir($dirname);
    }

    /**
     * @inheritDoc
     */
    public function setVisibility($path, $visibility)
    {
        return true;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function has($path)
    {
        return $this->client()->has($path);
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function read($path)
    {
        $contents = file_get_contents($this->getUrl($path));
        return compact('contents', 'path');
    }

    /**
     * @inheritDoc
     */
    public function readStream($path)
    {
        $stream = fopen($this->getUrl($path), 'r');
        return compact('stream', 'path');
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function listContents($directory = '', $recursive = false)
    {
        $list = [];

        $result = $this->client()->read($directory, null, ['X-List-Limit' => 100, 'X-List-Iter' => null]);

        foreach ($result['files'] as $files) {
            $list[] = $this->normalizeFileInfo($files, $directory);
        }

        return $list;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata($path)
    {
        return $this->client()->info($path);
    }

    /**
     * @inheritDoc
     */
    public function getSize($path)
    {
        $response = $this->getMetadata($path);

        return ['size' => $response['x-upyun-file-size']];
    }

    /**
     * @inheritDoc
     */
    public function getMimetype($path)
    {
        $headers = get_headers($this->getUrl($path), 1);
        $mimetype = $headers['Content-Type'];
        return compact('mimetype');
    }

    /**
     * @param string $path
     */
    public function getType(string $path): array
    {
        $response = $this->getMetadata($path);

        return ['type' => $response['x-upyun-file-type']];
    }

    /**
     * @param string $path
     */
    public function avMeta(string $path): array
    {
        return $this->client()->avMeta($path);
    }

    /**
     * @inheritDoc
     */
    public function getTimestamp($path)
    {
        $response = $this->getMetadata($path);

        return ['timestamp' => $response['x-upyun-file-date']];
    }

    /**
     * @inheritDoc
     */
    public function getVisibility($path)
    {
        return true;
    }

    /**
     * Normalize the file info.
     *
     * @param array $stats
     * @param string $directory
     *
     * @return array
     */
    protected function normalizeFileInfo(array $stats, string $directory): array
    {
        $filePath = ltrim($directory . '/' . $stats['name'], '/');

        return [
            'type' => $this->getType($filePath)['type'],
            'path' => $filePath,
            'timestamp' => $stats['time'],
            'size' => $stats['size'],
        ];
    }


    /**
     * @param $domain
     * @return string
     */
    protected function normalizeHost($domain): string
    {
        if (0 !== stripos($domain, 'https://') && 0 !== stripos($domain, 'http://')) {
            $domain = $this->protocol . "://{$domain}";
        }

        return rtrim($domain, '/') . '/';
    }

    /**
     * @param $path
     * @return string
     */
    public function getUrl($path): string
    {
        return $this->normalizeHost($this->domain) . $path;
    }
}
