<?php
/**
 * Created by PhpStorm.
 * Name:  Ubel Angel Fonseca Cedeño
 * Email: ubelangelfonseca@gmail.com
 * Date:  20/10/20
 * Time:  11:24
 */

namespace App\Cache;

use App\Entity\User;
use Doctrine\Common\Cache\RedisCache as RedisBaseCache;
use Symfony\Component\Security\Core\Security;


class RedisCache extends RedisBaseCache
{

    private const TIPO_OBJETO_IMG = 'Image';

    /** @var Security */
    protected $security;

    /* @var boolean */
    protected $redisFailed = true;

    /** @var integer */
    private $userId;


    /**
     * RedisCache constructor.
     * @param Security $security
     * @param $redis_database_host
     * @param $redis_database_port
     */
    public function __construct(Security $security, $redis_database_host, $redis_database_port) {
        $this->security = $security;
        try {
            // $redis = new \Redis();
            // $redis->connect($redis_database_host, $redis_database_port);

            // $this->setRedis($redis);

        } catch (\Exception $exception) {
            $this->redisFailed = true;
        }
    }

    public function contiene($name, $tipoObjeto = self::TIPO_OBJETO_IMG, User $user = null)
    {
        if ($this->redisFailed) {
            return false;
        }

        $id = $this->getUserId($user);
        $arrayCache = [];

        /* Si ya el trabajador esta en BD deberiamos recuperar el array de sus datos en cache */
        if ($this->contains($id)) {
            $arrayCache = $this->fetch($id);
        }

        /* Verifico que existan los datos del trabajador, que hayan menus guardados y que exista el menu entre los guardados en cache */
        if (!empty($arrayCache) && array_key_exists($tipoObjeto, $arrayCache) && array_key_exists($name, $arrayCache[$tipoObjeto])) {
            return true;
        }

        return false;
    }

    public function getUserId(User $user = null)
    {
        /* @var User $user */
        $user = null !== $user ? $user : $this->security->getToken()->getUser();

        return 'trabajador_' . $user->getId();
    }
}