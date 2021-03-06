<?php

namespace LaravelEnso\DocumentsManager\app\Classes;

use LaravelEnso\DocumentsManager\app\Models\Document;

class Presenter extends Handler
{
    private $document;

    public function __construct(Document $document)
    {
        parent::__construct();

        $this->document = $document;
    }

    public function download()
    {
        return $this->fileManager
            ->download(
                $this->document->original_name,
                $this->document->saved_name
            );
    }

    public function inline()
    {
        return $this->fileManager
            ->inline($this->document->saved_name);
    }
}
