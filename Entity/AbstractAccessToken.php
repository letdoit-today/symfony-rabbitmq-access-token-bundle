<?php /** @noinspection PhpUnused */

namespace DIT\RabbitMQAccessTokenBundle\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class AbstractAccessToken
 */
abstract class AbstractAccessToken implements AccessTokenInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @Serializer\Type("int")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=200)
     * @Serializer\Type("string")
     */
    protected $accessToken;

    /**
     * @ORM\Column(type="integer")
     * @Serializer\Type("int")
     */
    protected $userId;

    /**
     * @ORM\Column(type="simple_array")
     * @Serializer\Type("array<string>")
     */
    protected $roles = [];

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Type("DateTime")
     */
    protected $expiredAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles ?? [];

        return $this;
    }

    public function getExpiredAt(): ?DateTimeInterface
    {
        return $this->expiredAt;
    }

    public function setExpiredAt(?DateTimeInterface $expiredAt): self
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }
}
