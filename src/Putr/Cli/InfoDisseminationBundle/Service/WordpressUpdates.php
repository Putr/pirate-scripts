<?php

namespace Putr\Cli\InfoDisseminationBundle\Service;

class WordpressUpdates extends RssBase {

	/**
	 * Remaps data to something usable with Twig
	 * 
	 * @param  array $items  Array of SimplePie_Item
	 * @return array
	 */
	public function remapItems($items) {

		$data = array();

		foreach ($items as $item) {

			$link = explode("&", $item->get_link());
			$link = $link[0];

			$comments = $item->get_item_tags('http://purl.org/rss/1.0/modules/slash/', 'comments');
			$comments = $comments[0]['data'];

			$data[] = array(
					'title' => $item->get_title(),
					'link' => $link,
					'author' => $item->get_author()->name,
					'date' => $item->get_date(),
					'comments_num' => $comments
				);

		}

		return $data;
	}

}