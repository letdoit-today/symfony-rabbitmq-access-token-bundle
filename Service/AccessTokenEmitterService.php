<?php /** @noinspection PhpUnused */

namespace DIT\RabbitMQAccessTokenBundle\Service;

use DIT\RabbitMQAccessTokenBundle\Entity\AccessTokenInterface;
use DIT\RabbitMQBundle\Service\AbstractDirectEmitterService;
use Exception;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

/**
 * Class AccessTokenEmitterService
 */
class AccessTokenEmitterService extends AbstractDirectEmitterService
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * AccessTokenEmitterService constructor.
     * @param ContainerBagInterface $params
     * @param SerializerInterface $serializer
     */
    public function __construct(ContainerBagInterface $params, SerializerInterface $serializer)
    {
        parent::__construct($params);
        $this->serializer = $serializer;
    }

    /**
     * @param AccessTokenInterface $entity
     * @throws Exception
     */
    public function emitCreateMessage(AccessTokenInterface $entity)
    {
        $this->emitAccessTokenMessage($entity, 'access_tokens.create');
    }

    /**
     * @param AccessTokenInterface $entity
     * @throws Exception
     */
    public function emitUpdateMessage(AccessTokenInterface $entity)
    {
        $this->emitAccessTokenMessage($entity, 'access_tokens.update');
    }

    /**
     * @param AccessTokenInterface $entity
     * @throws Exception
     */
    public function emitDeleteMessage(AccessTokenInterface $entity)
    {
        $this->emitAccessTokenMessage($entity, 'access_tokens.delete');
    }

    protected function getExchange(): string
    {
        return 'entities';
    }

    /**
     * @param AccessTokenInterface $entity
     * @param string $routingKey
     * @throws Exception
     */
    protected function emitAccessTokenMessage(AccessTokenInterface $entity, string $routingKey)
    {
        $message = $this->serializer->serialize($entity, 'json');

        $this->emitMessage($message, $routingKey);
    }
}
