<?php

namespace App\Message;

class AddTimestampToImage implements AsyncMessages
{
    /**
     * @var int
     */
    private $imagePostId;

    public function __construct(int $imagePostId)
    {
        $this->imagePostId = $imagePostId;
    }

    /**
     * @return int
     */
    public function getImagePostId(): int
    {
        return $this->imagePostId;
    }
}