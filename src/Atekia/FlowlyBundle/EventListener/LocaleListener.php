<?php

namespace Atekia\FlowlyBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LocaleListener implements EventSubscriberInterface
{
    private $defaultLocale;

    public function __construct($defaultLocale, array $availableLocales)
    {
        $this->defaultLocale = $defaultLocale;
        $this->availableLocales = $availableLocales;
    }

    private static function pickLocale($fallbackLocale, array $preferredLocales = null, array $availableLocales = null)
    {
        if (empty($availableLocales)) {
            return $fallbackLocale;
        }

        if (!$preferredLocales) {
            return $fallbackLocale;
        }

        $extendedPreferredLocales = array();
        foreach ($preferredLocales as $language) {
            $extendedPreferredLocales[] = $language;
            if (false !== $position = strpos($language, '_')) {
                $superLanguage = substr($language, 0, $position);
                if (!in_array($superLanguage, $preferredLocales)) {
                    $extendedPreferredLocales[] = $superLanguage;
                }
            }
        }

        $availableLocales = array_values(array_intersect($extendedPreferredLocales, $availableLocales));

        return isset($availableLocales[0]) ? $availableLocales[0] : $fallbackLocale;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $request->setLocale($this->pickLocale($this->defaultLocale, $request->getLanguages(), $this->availableLocales));
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }
}