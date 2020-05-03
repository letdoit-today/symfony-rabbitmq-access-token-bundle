<?php

namespace DIT\RabbitMQAccessTokenBundle\Service;

use DIT\RabbitMQAccessTokenBundle\Entity\AccessTokenInterface;
use DIT\RabbitMQBundle\Service\AbstractDirectReceiverService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

/**
 * Class AccessTokenReceiverService
 */
class AccessTokenReceiverService extends AbstractDirectReceiverService
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * AccessTokenReceiverService constructor.
     * @param ContainerBagInterface $params
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     */
    public function __construct(
        ContainerBagInterface $params,
        SerializerInterface $serializer,
        EntityManagerInterface $em
    ) {
        parent::__construct($params);
        $this->serializer = $serializer;
        $this->em = $em;
    }

    public function getOutput()
    {
        return $this->output;
    }

    /** @noinspection PhpUnused */

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    protected function getRoutingKeys(): array
    {
        return [
            'access_tokens.create',
            'access_tokens.update',
            'access_tokens.delete',
        ];
    }

    protected function getExchange(): string
    {
        return 'entities';
    }

    protected function handleDefault(string $routingKey, string $body)
    {
        $this->writeWarning("Unhandle routingKey '$routingKey'");
    }

    protected function handleAccessTokenJson(string $json, callable $callback)
    {
        try {
            $className = $this->params->get('letdoittoday.access_token_class');
            /** @var AccessTokenInterface $entity */
            $entity = $this->serializer->deserialize($json, $className, 'json');
            /* TODO: Replace merge method */
            $entity = $this->em->merge($entity);
            $callback($entity);
        } catch (Exception $exception) {
            $this->writeError($exception->getMessage());
        }
    }

    /** @noinspection PhpUnused */
    protected function handleAccessTokensCreateMessage(string $message)
    {
        $this->handleAccessTokenJson(
            $message,
            /** @var AccessTokenInterface $entity */
            function ($entity) {
                $this->em->flush();
                $this->writeInfo("Created access token: {$entity->getId()}-{$entity->getAccessToken()}");
            }
        );
    }

    /** @noinspection PhpUnused */
    protected function handleAccessTokensUpdateMessage(string $message)
    {
        $this->handleAccessTokenJson(
            $message,
            /** @var AccessTokenInterface $entity */
            function ($entity) {
                $this->em->flush();
                $this->writeInfo("Updated access token: {$entity->getId()}-{$entity->getAccessToken()}");
            }
        );
    }

    /** @noinspection PhpUnused */
    protected function handleAccessTokensDeleteMessage(string $message)
    {
        $this->handleAccessTokenJson(
            $message,
            /** @var AccessTokenInterface $entity */
            function ($entity) {
                $message = "Deleted access token: {$entity->getId()}-{$entity->getAccessToken()}";
                $this->em->remove($entity);
                $this->em->flush();
                $this->writeInfo($message);
            }
        );
    }

    protected function writeInfo(string $message)
    {
        $this->writeMessage("<info>$message</info>");
    }

    protected function writeWarning(string $message)
    {
        $this->writeMessage("<comment>$message</comment>");
    }

    protected function writeError(string $message)
    {
        $this->writeMessage("<error>$message</error>");
    }

    protected function writeMessage(string $message)
    {
        if (!empty($this->output)) {
            $this->output->writeln($message);
        } else {
            echo $message.PHP_EOL;
        }
    }
}
