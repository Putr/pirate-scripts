<?php

namespace Putr\Cli\InfoDisseminationBundle\Service;

abstract class RssBase {

	public $feed;

	protected $logger;

	protected $container;

	protected $timeInterval;

	/**
	 * Constructor
	 */
	public function __construct($feed, $logger, $container, $time) {

		$this->feed         = $feed;
		$this->logger       = $logger;
		$this->container    = $container;
		$this->timeInterval = $time;

	}

	public function getDaily() {
		$itemsFrom = time() - $this->timeInterval;

		$items = $this->getItems($itemsFrom);

		return $items;
	}

	/**
	 * Retrives subste of items in 
	 * @param  integer $itemsFrom
	 * @return array            	Array of SimplePie_Item
	 */
	protected function getItems($itemsFrom) {
		$feed = $this->container->get('fkr_simple_pie.rss');

		$feed->set_feed_url($this->feed);
		$feed->force_feed(true);

		$feed->init();

		$items = $feed->get_items();

		$newItems = array();

		foreach ($items as $item) {
			$date = new \DateTime($item->get_date());
			$ts = $date->getTimestamp();

			if ($ts >= $itemsFrom) {
			$newItems[] = $item;
			}
		}

		return $newItems;
	}

}