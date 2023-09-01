<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Message;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class MessageProcessor implements ProcessorInterface
{
    public function __construct(private readonly ProcessorInterface $persistProcessor, private readonly HubInterface $publisher)
    {
    }
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Message
    {
        /** @var Message $result */
        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        $this->sendMessage($result);
        return $result;
    }

    private function sendMessage(Message $message): void
    {
        $topic = '/api/chat_rooms/' . $message->getChatRoom()->getId();
        $update = new Update(
            $topic,
            json_encode(['message' => $message->getContent()])
        );
        $this->publisher->publish($update);
    }
}
