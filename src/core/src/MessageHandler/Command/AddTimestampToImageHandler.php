<?php

namespace App\MessageHandler\Command;

use App\Message\Command\AddTimestampToImage;
use App\Photo\PhotoFileManager;
use App\Photo\PhotoTimestampficatorManager;
use App\Repository\ImagePostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AddTimestampToImageHandler implements MessageHandlerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var PhotoTimestampficatorManager
     */
    private $timestampficator;

    /**
     * @var PhotoFileManager
     */
    private $photoManager;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ImagePostRepository
     */
    private $imagePostRepository;

    public function __construct(
        PhotoTimestampficatorManager $timestampficator,
        PhotoFileManager $photoManager,
        EntityManagerInterface $entityManager,
        ImagePostRepository $imagePostRepository
    ) {
        $this->timestampficator = $timestampficator;
        $this->photoManager = $photoManager;
        $this->entityManager = $entityManager;
        $this->imagePostRepository = $imagePostRepository;
    }

    public function __invoke(AddTimestampToImage $addTimestampToImage)
    {
        $imagePostId = $addTimestampToImage->getImagePostId();
        $imagePost = $this->imagePostRepository->find($imagePostId);

        if(!$imagePost){
            if($this->logger){
                $this->logger->alert(sprintf('Image post %d was missing !', $imagePostId));
            }

            return;
        }

        $updatedContents = $this->timestampficator->timestampfy(
            $this->photoManager->read($imagePost->getFilename())
        );
        $this->photoManager->update($imagePost->getFilename(), $updatedContents);
        $imagePost->markAsTimestampAdded();
        $this->entityManager->flush();
    }
}