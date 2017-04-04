<?php

namespace NotificationBundle\Controller;

use FOS\UserBundle\Controller\ProfileController as FOSUser;
use FOS\UserBundle\FOSUserEvents;

class ProfileController extends FOSUser
{
    public function showAction()
    {
        $response = parent::showAction();
//$this->renderView();
        return $response;
    }
}