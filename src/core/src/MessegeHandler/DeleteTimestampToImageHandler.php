<?php

namespace App\MessegeHandler;

use App\Message\DeleteFile;
use App\Message\DeleteTimestampToImage;
use App\Repository\ImagePostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DeleteTimestampToImageHandler implements MessageHandlerInterface
{
    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ImagePostRepository
     */
    private $imagePostRepository;

    public function __construct(
        MessageBusInterface $messageBus,
        EntityManagerInterface $entityManager,
        ImagePostRepository $imagePostRepository
    ) {
        $this->messageBus = $messageBus;
        $this->entityManager = $entityManager;
        $this->imagePostRepository = $imagePostRepository;
    }

    public function __invoke(DeleteTimestampToImage $imagePost)
    {
        $imagePost = $this->imagePostRepository->find($imagePost->getImagePostId());
        $fileName = $imagePost->getFilename();

        /**
         * @todo Add transaction
         */
        $this->entityManager->remove($imagePost);
        $this->entityManager->flush();

        $this->messageBus->dispatch(new DeleteFile($fileName));
    }
}