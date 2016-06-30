<?php

namespace Drupal\shield\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ShieldEventSubscriber.
 *
 * @package Drupal\shield
 */
class ShieldEventSubscriber implements EventSubscriberInterface
{

  /**
   * Constructor.
   */
    public function __construct()
    {

    }

  /**
   * {@inheritdoc}
   */
    static function getSubscribedEvents()
    {
        $events['kernel.response'] = ['kernel_response'];

        return $events;
    }

  /**
   * This method is called whenever the kernel.response event is
   * dispatched.
   *
   * @param GetResponseEvent $event
   */
    public function kernel_response(Event $event)
    {
        $config = \Drupal::config('shield.settings');
        $user = $config->get('shield.user');
        if (!$user) {
            \Drupal::logger('shield')->notice('Event kernel.response no user set.');
            return;
        }

        /**
         * @todo allow drush to bypass
         */

        $pass = $config->get('shield.pass');
        if (!empty($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])
            && $_SERVER['PHP_AUTH_USER'] == $user
            && $_SERVER['PHP_AUTH_PW']   == $pass) {
            \Drupal::logger('shield')->notice('Event kernel.response variables set.');
            return;
        }

        $print = $config->get('shield.print');
        header(sprintf('WWW-Authenticate: Basic realm="%s"', strtr($print, array('[user]' => $user, '[pass]' => $pass))));
        header('HTTP/1.0 401 Unauthorized');
        exit;
        //drupal_set_message('Event kernel.response thrown by Subscriber in module shield.', 'status', true);


    }
}
