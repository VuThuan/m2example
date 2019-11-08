<?php
namespace BDC\OrderNotes\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer;

class SaveOrderNotesToOrder implements ObserverInterface {
    public function execute(Observer $observer) {
        $event = $observer->getEvent();
        if ($notes = $event->getQuote()->getOrderNotes()) {
            $event->getOrder()->setOrderNotes($notes)
                ->addStatusHistoryComment('Customer note: ' . $notes);
        }
        return $this;
    }
}
