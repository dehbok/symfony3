<?php
namespace AppBundle\EventListener;

use AppBundle\Entity\Notification;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class LoginListener implements EventSubscriberInterface
{

    private $user;

    private $template;

    private $mailer;

    private $doctrine;

    public function __construct($container, $template, $mailer, $doctrine)
    {
        $this->user = $container->getToken()->getUser();
        $this->template = $template;
        $this->mailer = $mailer;
        $this->doctrine = $doctrine;
    }


    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::SECURITY_IMPLICIT_LOGIN => 'onLogin',
            SecurityEvents::INTERACTIVE_LOGIN => 'onLogin',
        );
    }

    public function onLogin($event)
    {
        $this->sendEmail();
        $this->createNotification();
    }

    private function createNotification()
    {
        $newNotification = new Notification();
        $newNotification->setUser($this->user);
        $newNotification->setCreatedDate(new \DateTime("now"));
        $newNotification->setIsDisplayed(false);

        $manager = $this->doctrine->getManager();
        $manager->persist($newNotification);
        $manager->flush();
    }

    private function sendEmail()
    {
        $email = $this->user->getEmail();
        $name = $this->user->getUsername();

        $message = \Swift_Message::newInstance()
            ->setSubject('Login notification')
            ->setFrom('noreplay@symfony_test.com')
            ->setTo($email)
            ->setBody(
                $this->template->render(
                // app/Resources/views/Emails/login.html.twig
                    'Emails/login.html.twig',
                    array('name' => $name)
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }
}