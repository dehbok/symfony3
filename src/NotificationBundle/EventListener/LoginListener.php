<?php
namespace NotificationBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class LoginListener implements EventSubscriberInterface
{

    private $container;

    private $template;

    private $mailer;

    public function __construct($container, $template, $mailer)
    {
        $this->container = $container;
        $this->template = $template;
        $this->mailer = $mailer;
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
        $user = $this->container->getToken()->getUser();
        $email = $user->getEmail();
        $name = $user->getUsername();

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