<?php
/**
 * @author Dmitry Petrov <old.fightmaster@gmail.com>
 */

namespace Fightmaster\Trailrun\Competition;


class AccessList
{
    const ACCESS_ADMIN = 1;
    const ACCESS_MODERATOR = 2;
    const ACCESS_JUDGE = 3;
    const ACCESS_GUEST = 4;

    public static function getAccessList()
    {
        return [
            self::ACCESS_ADMIN, self::ACCESS_MODERATOR, self::ACCESS_JUDGE, self::ACCESS_GUEST
        ];
    }

}
