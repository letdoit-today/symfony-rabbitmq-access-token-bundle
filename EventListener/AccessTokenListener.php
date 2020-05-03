<?php /** @noinspection PhpUnused */

namespace DIT\RabbitMQAccessTokenBundle\EventListener;

use DIT\RabbitMQAccessTokenBundle\Entity\AccessTokenInterface;
use DIT\RabbitMQAccessTokenBundle\Service\AccessTokenEmitterService;
use Exception;

/**
 * Class AccessTokenListener
 */
class AccessTokenListener
{
    /**
     * @var AccessTokenEmitterService
     */
    protected $emitterService;

    /**
     * @var AccessTokenInterface
     */
    protected $deletingEntity;

    /**
     * AccessTokenListener constructor.
     * @param AccessTokenEmitterService $emitterService
     */
    public function __construct(AccessTokenEmitterService $emitterService)
    {
        $this->emitterService = $emitterService;
    }

    /**
     * @param AccessTokenInterface $entity
     * @throws Exception
     */
    public function postPersist(AccessTokenInterface $entity)
    {
        $this->emitterService->emitCreateMessage($entity);
    }

    /**
     * @param AccessTokenInterface $entity
     * @throws Exception
     */
    public function postUpdate(AccessTokenInterface $entity)
    {
        $this->emitterService->emitUpdateMessage($entity);
    }

    /**
     * @param $entity
     */
    public function preRemove(AccessTokenInterface $entity)
    {
        $this->deletingEntity = clone $entity;
    }

    /**
     * @throws Exception
     */
    public function postRemove()
    {
        $this->emitterService->emitDeleteMessage($this->deletingEntity);
    }

}
