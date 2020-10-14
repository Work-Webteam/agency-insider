<?php

namespace Drupal\siteminder\EventSubscriber;

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
    // Check if the current domain is not excluded from Siteminder
    if ($this->siteminder->checkAuthDomain()) {
      $current_path = \Drupal::service('path.current')->getPath();
      // Check if a new user wants to edit his profile
      $new_user = ($event->getRequest()->query->get('status') == "new_user") ? true : false;
      if ($current_path != '/siteminder_login' && $current_path != '/user/login' && $current_path != '/access_denied' && $current_path != '/pending_validation' && $current_path != '/user/logout' && !$new_user) {
        // If the user is NOT already logged in and the HTTP header is sending a siteminder ID, login.
        if (!\Drupal::currentUser()->isAuthenticated()) {
          $response = new RedirectResponse("/siteminder_login", 301);
          $event->setResponse($response);
        }
      }
    }
    return;
  }

  /**
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *
   * Helper function that is used to save the initial access url.
   * This will allow us to redirect user to the initial request url instead of
   * redirecting to the homepage on every authentication.
   */
  public function saveRequestUri(GetResponseEvent $event) {
    // Only do this if the user has not been authenticated -
    // no sense updating this over and over for no reason.
    $logged_in = \Drupal::currentUser()->isAnonymous();
    if($logged_in) {
      return;
    }
    // Grab the path request.
    $path = \Drupal::service('path.current')->getPath();
    // We don't want to overwrite the initial access url when users
    // are redirected.
    if($path != '/siteminder_login' && $path != '/' ) {
      // Grab our config settings.
      $config = \Drupal::service('config.factory')->getEditable('siteminder.settings');
      // Set the config variable so we can pick it up again in Controller after auth.
      $config->set('user_initial_url', $path)->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['saveRequestUri', 400];
    $events[KernelEvents::REQUEST][] = ['loginSiteminder', 100];
    return $events;
  }

}
