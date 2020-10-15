<?php

namespace Drupal\siteminder\EventSubscriber;

use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\siteminder\Service\Siteminder;

class InitSubscriber implements EventSubscriberInterface {

  /**
   * @var Siteminder
   */
  protected $siteminder;

  /**
   * InitSubscriber constructor.
   *
   * @param \Drupal\siteminder\Service\Siteminder $siteminder
   */
  public function __construct(
  Siteminder $siteminder_info
  ) {
    $this->siteminder = $siteminder_info;
  }

  public function loginSiteminder(GetResponseEvent $event) {
    // Check if the current domain is not excluded from Siteminder.
    if ($this->siteminder->checkAuthDomain()) {
      $current_path = \Drupal::service('path.current')->getPath();
      // Check if a new user wants to edit his profile
      $new_user = ($event->getRequest()->query->get('status') == "new_user") ? true : false;
      if ($current_path != '/siteminder_login' && $current_path != '/user/login' && $current_path != '/access_denied' && $current_path != '/pending_validation' && $current_path != '/user/logout' && !$new_user) {
        // If the user is NOT already logged in and the
        // HTTP header is sending a siteminder ID, login.
        if (!\Drupal::currentUser()->isAuthenticated()) {
          // We want to save the original URL
          // so that we can redirect users after login.
          $config = \Drupal::service('config.factory')->getEditable('siteminder.settings');
          $config->set('user_initial_url', $current_path)->save();
          // Now that we have the URL, redirect to login.
          $response = new RedirectResponse("/siteminder_login", 301);
          $event->setResponse($response);
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['loginSiteminder', 100];
    return $events;
  }

}
