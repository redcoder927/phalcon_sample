<?php

namespace app\library;

class EnumHolder
{
    public const users_access_level = ['admin' => 'admin', 'user' => 'user'];
    public const users_status = ['verified' => 'verified', 'pending' => 'pending', 'ban' => 'ban'];

}