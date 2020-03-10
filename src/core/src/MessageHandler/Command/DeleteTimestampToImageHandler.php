<?php

namespace App\MessageHandler\Command;

use App\Message\Command\DeleteTimestampToImage;
use App\Message\Event\ImagePostDeletedEvent;
use App\Repository\ImagePostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DeleteTimestampToImageHandler implements MessageHandlerInterface
{
    /**
     * @var MessageBusInterface
     */
    private $eventBus;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ImagePostRepository
     */
    private $imagePostRepository;

    public function __construct(
        MessageBusInterface $eventBus,
        EntityManagerInterface $entityManager,
        ImagePostRepository $imagePostRepository
    ) {
        $this->eventBus = $eventBus;
        $this->entityManager = $entityManager;
        $this->imagePostRepository = $imagePostRepository;
    }

    public function __invoke(DeleteTimestampToImage $imagePost)
    {
        $imagePost = $this->imagePostRepository->find($imagePost->getImagePostId());
        $fileName = $imagePost->getFilename();

        $this->entityManager->remove($imagePost);
        $this->entityManager->flush();

        $this->eventBus->dispatch(new ImagePostDeletedEvent($fileName));
    }
}