<?php

namespace DIT\RabbitMQAccessTokenBundle\Entity;

/**
 * Interface AccessTokenInterface
 */
interface AccessTokenInterface
{
    public function getId(): ?int;

    public function getAccessToken(): ?string;

    public function setAccessToken(string $email);

    public function getUserId(): ?int;

    public function setUserId(int $userId);

    public function getRoles(): ?array;

    public function setRoles(array $roles);
}
