<?php

declare(strict_types=1);

/*
 * This file is part of the Toggler package.
 *
 * (c) SolidWorx <open-source@solidworx.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SolidWorx\Toggler\Storage;

use PDO;

class PdoStorage implements StorageInterface, PersistentStorageInterface
{
    /**
     * @var string
     */
    private $dsn;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private $tableName;

    /**
     * @var string|null
     */
    private $driver;

    /**
     * @var PDO|null
     */
    private $conn;

    public function __construct(
        #[\SensitiveParameter]
        string $dsn,
        #[\SensitiveParameter]
        string $username = '',
        #[\SensitiveParameter]
        string $password = '',
        string $tableName = 'features'
    ) {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->tableName = $tableName;
    }

    public function get(string $key): bool
    {
        $conn = $this->getConnection();
        $this->createTable();

        $sql = "SELECT enabled FROM $this->tableName WHERE feature = :feature";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['feature' => $key]);

        /** @var false|array{enabled: bool|int} $result */
        $result = $stmt->fetch();

        if (false !== $result) {
            return (bool) $result['enabled'];
        }

        return false;
    }

    public function set(string $key, bool $value): bool
    {
        $conn = $this->getConnection();
        $this->createTable();

        switch ($this->driver) {
            case 'sqlite':
                $sql = /* @lang SQLite */ "INSERT OR REPLACE INTO $this->tableName (feature, enabled) SELECT :feature, :enabled";
                break;

            case 'pgsql':
                $sql = /* @lang PostgreSQL */ "INSERT INTO $this->tableName (feature, enabled) VALUES (:feature, :enabled) ON CONFLICT (feature) DO UPDATE SET enabled = :enabled";
                break;

            case 'oci':
                $sql = /* @lang SQL */ "MERGE INTO $this->tableName USING DUAL ON (feature = :feature) WHEN MATCHED THEN UPDATE SET enabled = :enabled WHEN NOT MATCHED THEN INSERT (feature, enabled) VALUES (:feature, :enabled)";
                break;

            case 'mysql':
                $sql = /* @lang MySQL */ "INSERT INTO $this->tableName (feature, enabled) VALUES (:feature, :enabled) ON DUPLICATE KEY UPDATE enabled = :enabled";
                break;

            case 'sqlsrv':
                $sql = /* @lang SQL */ "IF EXISTS (SELECT * FROM $this->tableName WHERE feature = :feature) UPDATE $this->tableName SET enabled = :enabled WHERE feature = :feature ELSE INSERT INTO $this->tableName (feature, enabled) VALUES (:feature, :enabled)";
                break;

            default:
                throw new \RuntimeException('Unsupported driver');
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute(['feature' => $key, 'enabled' => $value]);

        return $value;
    }

    public function all(): array
    {
        $conn = $this->getConnection();
        $this->createTable();

        $sql = "SELECT feature FROM $this->tableName";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        /** @var list<array{feature: string}> $result */
        $result = $stmt->fetchAll();

        $features = [];
        foreach ($result as $row) {
            $features[] = $row['feature'];
        }

        return $features;
    }

    private function createTable(): void
    {
        // connect if we are not yet
        $conn = $this->getConnection();

        switch ($this->driver) {
            case 'mysql':
                $sql = /* @lang MySQL */ "CREATE TABLE IF NOT EXISTS $this->tableName (feature VARCHAR(255) NOT NULL PRIMARY KEY, enabled TINYINT(1) NOT NULL) COLLATE utf8mb4_bin, ENGINE = InnoDB";
                break;
            case 'sqlite':
                $sql = /* @lang SQLite */ "CREATE TABLE IF NOT EXISTS $this->tableName (feature TEXT NOT NULL PRIMARY KEY, enabled BOOLEAN NOT NULL)";
                break;
            case 'pgsql':
                $sql = /* @lang PostgreSQL */ "CREATE TABLE IF NOT EXISTS $this->tableName (feature VARCHAR(255) NOT NULL PRIMARY KEY, enabled BOOLEAN NOT NULL)";
                break;
            case 'oci':
                $sql = /* @lang SQL */ "CREATE TABLE IF NOT EXISTS $this->tableName (feature VARCHAR2(255) NOT NULL PRIMARY KEY, enabled NUMBER(1) NOT NULL)";
                break;
            case 'sqlsrv':
                $sql = /* @lang SQL */ "CREATE TABLE IF NOT EXISTS $this->tableName (feature VARCHAR(255) NOT NULL PRIMARY KEY, enabled BIT NOT NULL)";
                break;
            default:
                throw new \DomainException(sprintf('Creating the cache table is currently not implemented for PDO driver "%s".', $this->driver));
        }

        $conn->exec($sql);
    }

    private function getConnection(): PDO
    {
        if (!isset($this->conn)) {
            $this->conn = new PDO($this->dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }

        if (!isset($this->driver)) {
            $this->driver = strval($this->conn->getAttribute(PDO::ATTR_DRIVER_NAME));
        }

        return $this->conn;
    }
}
