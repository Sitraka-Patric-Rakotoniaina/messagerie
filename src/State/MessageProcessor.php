<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;

class MessageProcessor implements ProcessorInterface
{
    public function __construct(private readonly ProcessorInterface $persistProcessor)
    {
    }
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        dd($result);
    }
}
