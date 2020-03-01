<?php

namespace App\Message;

class DeleteFile implements AsyncMessages
{
    /**
     * @var string
     */
    private $fileName;

    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }
}