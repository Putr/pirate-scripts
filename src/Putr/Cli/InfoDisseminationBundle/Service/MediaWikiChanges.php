<?php

namespace Putr\Cli\InfoDisseminationBundle\Service;

class MediaWikiChanges extends RssBase {

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

			$data[] = array(
					'title' => $item->get_title(),
					'link' => $link,
					'author' => $item->get_author()->name,
					'date' => $item->get_date()
				);

		}

		return $data;
	}

}