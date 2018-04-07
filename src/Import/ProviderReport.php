<?php


namespace App\Import;


class ProviderReport
{
    public $numberOfImportedEvents=0;

    public $numberOfFoundEvents=0;

    public $key;

    public function __construct(string $key)
    {
        $this->key = $key;
        $this->numberOfImportedEvents=0;
        $this->numberOfFoundEvents=0;
    }
}