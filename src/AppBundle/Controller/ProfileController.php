<?php

namespace AppBundle\Controller;

use FOS\UserBundle\Controller\ProfileController as FOSUser;
use FOS\UserBundle\FOSUserEvents;
use AppBundle\Entity\User as UserInterface;

class ProfileController extends FOSUser
{
    public function showAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $repository = $this->getDoctrine()->getRepository('AppBundle:Notification');
        $notification = $repository->findOneBy(
            array(
                'user' => $user->getId(),
                'isDisplayed' => false
            ),
            array(
                'createdDate' => 'DESC'
            )
        );


        return $this->render('@FOSUser/Profile/show.html.twig', array(
            'user' => $user,
            'lastNotificationDate' => ($notification) ? $notification->getCreatedDate()->format('Y-m-d H:i:s') : null
        ));
    }
}