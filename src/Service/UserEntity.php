<?php

namespace App\Service;


use League\OAuth2\Server\Entities\Interfaces\UserEntityInterface;


class UserEntity implements UserEntityInterface
{

    /**
     * Return the user's identifier.
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return (string)$this->username;


    }


}

