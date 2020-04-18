<?php

namespace App\Twig;

use App\Service\MarkdownHelper;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension implements ServiceSubscriberInterface
{

    private $container;


    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('wigit', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('wigit', [$this, 'doSomething']),
        ];
    }

    public function doSomething($value)
    {
        return $this->container->get(MarkdownHelper::class)->parse($value)."|fuckme ";
    }

    public static function getSubscribedServices()
    {
        return [
            MarkdownHelper::class
        ];
    }
}
