<?php

namespace App\Photo;

use Doctrine\ORM\EntityManagerInterface;
use Intervention\Image\Constraint;
use Intervention\Image\ImageManager;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Finder\Finder;

class PhotoTimestampficatorManager
{
    private $entityManager;
    private $imageManager;
    private $photoFilesystem;

    public function __construct(EntityManagerInterface $entityManager, ImageManager $imageManager, FilesystemInterface $photoFilesystem)
    {
        $this->entityManager = $entityManager;
        $this->imageManager = $imageManager;
        $this->photoFilesystem = $photoFilesystem;
    }

    public function timestampfy(string $imageContents): string
    {
        $targetPhoto = $this->imageManager->make($imageContents);

        $timestampFilename = $this->getRandomTimestampFilename();
        $timestampPhoto = $this->imageManager->make($timestampFilename);

        $targetWidth = $targetPhoto->width() * .3;
        $targetHeight = $targetPhoto->height() * .4;

        $timestampPhoto->resize($targetWidth, $targetHeight, function(Constraint $constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $targetPhoto = $targetPhoto->insert(
            $timestampPhoto,
            'bottom-right'
        );

        // for dramatic effect, make this *really* slow
        sleep(2);

        return (string) $targetPhoto->encode();
    }

    private function getRandomTimestampFilename(): string
    {
        $finder = new Finder();
        $finder->in(__DIR__.'/../../assets/timestamps')
            ->files();

        // array keys are the absolute file paths
        $timestampFiles = iterator_to_array($finder->getIterator());

        return array_rand($timestampFiles);
    }
}
