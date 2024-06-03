<?php

namespace Funkymed\TenantAwareBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tenant")]
class Tenant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $databaseHost;

    #[ORM\Column(type: "string", length: 255)]
    private string $databaseName;

    #[ORM\Column(type: "string", length: 255)]
    private string $databaseUser;

    #[ORM\Column(type: "string", length: 255)]
    private string $databasePassword;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $hostname;

    public function getId(): int
    {
        return $this->id;
    }

    public function getDatabaseHost(): string
    {
        return $this->databaseHost;
    }

    public function setDatabaseHost(string $databaseHost): void
    {
        $this->databaseHost = $databaseHost;
    }

    public function getDatabaseName(): string
    {
        return $this->databaseName;
    }

    public function setDatabaseName(string $databaseName): void
    {
        $this->databaseName = $databaseName;
    }

    public function getDatabaseUser(): string
    {
        return $this->databaseUser;
    }

    public function setDatabaseUser(string $databaseUser): void
    {
        $this->databaseUser = $databaseUser;
    }

    public function getDatabasePassword(): string
    {
        return $this->databasePassword;
    }

    public function setDatabasePassword(string $databasePassword): void
    {
        $this->databasePassword = $databasePassword;
    }

    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    public function setHostname(string $hostname): self
    {
        $this->hostname = $hostname;
        return $this;
    }

    public function getDatabaseConfiguration()
    {
        return [
            'host' => $this->getDatabaseHost(),
            'port' => 3306,
            'dbname' => $this->getDatabaseName(),
            'user' => $this->getDatabaseUser(),
            'password' => $this->getDatabasePassword(),
        ];
    }

}
