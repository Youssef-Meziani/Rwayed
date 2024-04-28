<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MinioExtension extends AbstractExtension
{
    private string $uploadsBaseUrl;

    public function __construct(string $uploadsBaseUrl)
    {
        $this->uploadsBaseUrl = $uploadsBaseUrl;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('minio_url', [$this, 'getMinioUrl']),
        ];
    }

    public function getMinioUrl(string $imagePath): string
    {
        return $this->uploadsBaseUrl . $imagePath;
    }
}
