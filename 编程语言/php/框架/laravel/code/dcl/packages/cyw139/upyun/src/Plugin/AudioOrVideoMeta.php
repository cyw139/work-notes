<?php


namespace Cyw139\Upyun\Plugin;


class AudioOrVideoMeta extends \League\Flysystem\Plugin\AbstractPlugin
{

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        return 'avMeta';
    }

    public function handle($path = null)
    {
        return $this->filesystem->getAdapter()->avMeta($path);
    }
}
