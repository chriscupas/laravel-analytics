<?php

namespace AndreasElia\Analytics\Tests\Support;

use Illuminate\Contracts\Auth\Authenticatable;

class DummyUser implements Authenticatable
{
    public $id;

    public $name;

    public $email;

    public $email_verified_at;

    public $password;

    public $remember_token;

    public function __construct($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->id;
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function getAuthPasswordName()
    {
        return 'password';
    }
}
