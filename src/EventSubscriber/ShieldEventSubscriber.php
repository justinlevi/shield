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
        drupal_set_message('Event kernel.response thrown by Subscriber in module shield.', 'status', true);
    }
}
