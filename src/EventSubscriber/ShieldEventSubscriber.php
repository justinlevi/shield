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
        $pass = $config->get('shield.pass');
        $print = $config->get('shield.print');
        $sitename = \Drupal::config('system.site')->get('name');

        if (!$user) {
            \Drupal::logger('shield')->notice('Event kernel.response no user set.');
            return;
        }

        /**
         * @todo allow drush to bypass
         */

        /*
         * Pull out username and password from encrypted server variable
         */
        if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(
                ':',
                base64_decode(substr($_SERVER['REDIRECT_HTTP_AUTHORIZATION'], 6))
            );
            \Drupal::logger('shield')->notice('Event kernel.response credentials decrypted.');
        }

        if (!empty($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])
            && $_SERVER['PHP_AUTH_USER'] == $user
            && $_SERVER['PHP_AUTH_PW']   == $pass) {
            \Drupal::logger('shield')->notice('Event kernel.response variables set.');
            return;
        }

        //header(sprintf('WWW-Authenticate: Basic realm="%s"', strtr($print, array('[user]' => $user, '[pass]' => $pass))));
        header('WWW-Authenticate: Basic realm="' . $sitename . '"');
        header('HTTP/1.0 401 Unauthorized');
        exit();
    }
}
