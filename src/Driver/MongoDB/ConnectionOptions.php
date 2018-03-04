<?php declare(strict_types=1);

namespace Igni\Storage\Driver\MongoDB;

use MongoDB;

final class ConnectionOptions
{
    private const READ_PREFERENCE = [
        MongoDB\Driver\ReadPreference::RP_PRIMARY => 'primary',
        MongoDB\Driver\ReadPreference::RP_PRIMARY_PREFERRED => 'primaryPreferred',
        MongoDB\Driver\ReadPreference::RP_SECONDARY => 'secondary',
        MongoDB\Driver\ReadPreference::RP_SECONDARY_PREFERRED => 'secondaryPreferred',
        MongoDB\Driver\ReadPreference::RP_NEAREST => 'nearest',
    ];
    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var string */
    private $database;

    /** @var int */
    private $connectTimeout;

    /** @var int */
    private $socketTimeout;

    /** @var string */
    private $appName;

    /** @var string */
    private $replicaSet;

    /** @var string */
    private $authMechanism;

    /** @var array */
    private $authOptions;

    /** @var MongoDB\Driver\ReadConcern */
    private $readConcern;

    /** @var MongoDB\Driver\WriteConcern */
    private $writeConcern;

    /** @var MongoDB\Driver\ReadPreference */
    private $readPreference;

    /** @var string */
    private $sslPemFile;

    /** @var string */
    private $sslPemPassword;

    /** @var resource */
    private $sslContext;

    public function __construct(string $database, string $username = null, string $password = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->readPreference = new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_PRIMARY);
    }

    public function setReadConcern(MongoDB\Driver\ReadConcern $concern): void
    {
        $this->readConcern = $concern;
    }

    public function setWriteConcern(MongoDB\Driver\WriteConcern $concern): void
    {
        $this->writeConcern = $concern;
    }

    public function setReadPreference(MongoDB\Driver\ReadPreference $preference): void
    {
        $this->readPreference = $preference;
    }

    public function setAppName(string $appName): void
    {
        $this->appName = $appName;
    }

    /**
     * Specifies non default authentication mechanism
     * @param string $mechanism
     * @param array $options
     * @see https://github.com/mongodb/specifications/blob/master/source/auth/auth.rst#auth-related-options
     */
    public function setAuth(string $mechanism, array $options = []): void
    {
        $this->authMechanism = $mechanism;
        $this->authOptions = $options;
    }

    public function setConnectionTimeout(int $milliseconds): void
    {
        $this->connectTimeout = $milliseconds;
    }

    public function setSocketTimeout(int $milliseconds): void
    {
        $this->socketTimeout = $milliseconds;
    }

    public function setReplicaSet(string $name): void
    {
        $this->replicaSet = $name;
    }

    public function useSSL(string $pemFile, string $pemPassword = null, array $context = null): void
    {
        if ($context !== null) {
            $this->sslContext = stream_context_create([
                'ssl' => $context
            ]);
        }

        $this->sslPemFile = $pemFile;
        $this->sslPemPassword = $pemPassword;
    }

    public function getURIOptions(): array
    {
        $options = [];
        if ($this->username !== null) {
            $options['username'] = $this->username;
        }

        if ($this->password !== null) {
            $options['password'] = $this->password;
        }

        if ($this->appName !== null) {
            $options['appname'] = $this->appName;
        }

        if ($this->authMechanism !== null) {
            $options['authMechanism'] = $this->authMechanism;
            if ($this->authOptions !== null) {
                $options['authMechanismProperties'] = $this->authOptions;
            }
        }

        if ($this->connectTimeout !== null) {
            $options['connectTimeoutMS'] = $this->connectTimeout;
        }

        if ($this->socketTimeout !== null) {
            $options['socketTimeoutMS'] = $this->socketTimeout;
        }

        if ($this->replicaSet !== null) {
            $options['replicaSet'] = $this->replicaSet;
        }

        if ($this->readConcern !== null) {
            $options['readConcernLevel'] = $this->readConcern->getLevel();
        }

        if ($this->readPreference !== null) {
            if (method_exists($this->readPreference, 'getMaxStalenessSeconds')) {
                $options['maxStalenessSeconds'] = $this->readPreference->getMaxStalenessSeconds();
            }
            $options['readPreference'] = self::READ_PREFERENCE[$this->readPreference->getMode()];
        }

        return $options;
    }

    public function getDriverOptions(): array
    {
        if (!$this->sslPemFile) {
            return [];
        }
        $options = [
            'pem_file' => $this->sslPemFile,
        ];

        if ($this->sslPemPassword) {
            $options['pem_pwd'] = $this->sslPemPassword;
        }

        if ($this->sslContext) {
            $options['context'] = $this->sslContext;
        }

        return $options;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }
}
