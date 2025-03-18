<?php
/**
 * @link https://github.com/creocoder/yii2-flysystem
 * @copyright Copyright (c) 2015 Alexander Kochetov
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace creocoder\flysystem;

use League\Flysystem\PhpseclibV3\SftpConnectionProvider;
use League\Flysystem\PhpseclibV3\SftpAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use Yii;
use yii\base\InvalidConfigException;

/**
 * SftpFilesystem
 *
 * @author Alexander Kochetov <creocoder@gmail.com>
 */
class SftpFilesystem extends Filesystem
{
    /**
     * @var string
     */
    public $host;
    /**
     * @var string
     */
    public $port;
    /**
     * @var string
     */
    public $username;
    /**
     * @var string
     */
    public $password;
    /**
     * @var integer
     */
    public $timeout;
    /**
     * @var string
     */
    public $root;
    /**
     * @var string
     */
    public $privateKey;
    /**
     * @var string
     */
    public $passphrase;
    /**
     * @var integer
     */
    public $permPrivate;
    /**
     * @var integer
     */
    public $permPublic;
    /**
     * @var integer
     */
    public $directoryPerm;
    /**
     * @var boolean
     */
    public $useAgent = false;
    /**
     * @var integer
     */
    public $maxTries = 10;
    /**
     * @var string|null
     */
    public $hostFingerprint = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->host === null) {
            throw new InvalidConfigException('The "host" property must be set.');
        }

        if ($this->username === null) {
            throw new InvalidConfigException('The "username" property must be set.');
        }

        if ($this->password === null && $this->privateKey === null) {
            throw new InvalidConfigException('Either "password" or "privateKey" property must be set.');
        }

        if ($this->root !== null) {
            $this->root = Yii::getAlias($this->root);
        }

        parent::init();
    }

    /**
     * @return SftpAdapter
     */
    protected function prepareAdapter(): SftpAdapter
    {
        $config = [];

        foreach ([
            'host',
            'port',
            'username',
            'password',
            'timeout',
            'root',
            'privateKey',
            'permPrivate',
            'permPublic',
            'directoryPerm',
        ] as $name) {
            if ($this->$name !== null) {
                $config[$name] = $this->$name;
            }
        }

        return new SftpAdapter(
            new SftpConnectionProvider(
                $this->host,
                $this->username,
                $this->password,
                $this->privateKey,
                $this->passphrase,
                $this->port,
                $this->useAgent,
                $this->timeout,
                $this->maxTries,
                $this->hostFingerprint,
                null,
            ),
            $this->root,
            PortableVisibilityConverter::fromArray([
                'file' => [
                    'public' => $this->permPublic,
                    'private' => $this->permPrivate,
                ],
                'dir' => [
                    'public' => $this->directoryPerm,
                    'private' => $this->directoryPerm,
                ],
            ])
        );
    }
}
