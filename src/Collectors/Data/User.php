<?php
/**
 * Created by PhpStorm.
 * User: arnaud
 * Date: 07/11/15
 * Time: 13:48
 */

namespace Ndrx\Profiler\Laravel\Collectors\Data;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Ndrx\Profiler\DataSources\Contracts\DataSourceInterface;
use Ndrx\Profiler\JsonPatch;
use Ndrx\Profiler\Process;

/**
 * @property \Illuminate\Auth\Authenticatable|Model $user
 *
 * Class User
 * @package Ndrx\Profiler\Laravel\Collectors\Data
 */
class User extends \Ndrx\Profiler\Collectors\Data\User
{

    /**
     * User constructor.
     */
    public function __construct(Process $process, DataSourceInterface $dataSource, JsonPatch $jsonPatch = null)
    {
        parent::__construct($process, $dataSource, $jsonPatch);

        $this->user = Auth::user();
    }

    /**
     * Return the user user identifier email/username or whatever
     *
     * @return string
     */
    public function getIdentifier()
    {
        if ($this->user === null) {
            return null;
        }

        return $this->user->getAuthIdentifier();
    }

    /**
     * Return the user id or uuid
     *
     * @return string|int
     */
    public function getId()
    {
        if ($this->user === null) {
            return null;
        }

        return $this->user->getKey();
    }

    /**
     * User details for examples roles, timestamps...
     *
     * @return array
     */
    public function getDetails()
    {
        if ($this->user === null) {
            return null;
        }

        return $this->user->toArray();
    }
}