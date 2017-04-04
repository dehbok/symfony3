<?php

namespace NotificationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class NotificationBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
