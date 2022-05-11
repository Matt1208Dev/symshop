<?php

namespace App\EventDispatcher;

use App\Event\ProductViewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ProductViewSubscriber implements EventSubscriberInterface
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'addLog'
        ];
    }

    public function addLog(ProductViewEvent $productViewEvent)
    {
        $this->logger->info('Le produit ' . $productViewEvent->getProduct()->getId() . ' a été consulté');
    }
}