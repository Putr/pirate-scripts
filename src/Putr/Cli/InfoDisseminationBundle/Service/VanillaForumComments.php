<?php

namespace Putr\Cli\InfoDisseminationBundle\Service;

class VanillaForumComments extends RssBase {

	/**
	 * Remaps data to something usable with Twig
	 *
	 * Takes in dataset from getItems and remaps to raw data
	 * so it can be used with twig
	 * 
	 * @param  array $items  Array of SimplePie_Item
	 * @return array
	 */
	public function remapItems($items) {

		$data = array();

		foreach ($items as $item) {
			$ids = explode("-", $item->get_id());

			if (isset($ids[1])) {
				$disId = $ids[1];
				$comId = $ids[0];
			} else {
				$disId = $ids[0];
				$comId = null;
			}

			if (!isset($data[$disId])) {
				$data[$disId] = array(
					'title'    => str_replace("RE: ", "", $item->get_title()),
					'comments' => array()
					);
			}

			$data[$disId]['comments'][] = array (
					'date'   => $item->get_date(),
					'link'   => $item->get_link(),
					'text'   => $item->get_description(),
					'author' => $item->get_author()->name
				);
		}

		return $data;
	}

	/**
	 * Remaps data to something usable with Twig
	 *
	 * Same as remapItems but only outputs simple data
	 * 
	 * @param  array $items  Array of SimplePie_Item
	 * @return array
	 */
	public function remapSimple($items) {

		$data = array();

		foreach ($items as $item) {
			$ids = explode("-", $item->get_id());

			if (isset($ids[1])) {
				$disId = $ids[1];
				$comId = $ids[0];
			} else {
				$disId = $ids[0];
				$comId = null;
			}

			if (!isset($data[$disId])) {
				$data[$disId] = array(
					'title'         => str_replace("RE: ", "", $item->get_title()),
					'commentsTotal' => 1,
					'id_first'      => $comId,
					'link'          => $item->get_link(),
					);
			} else {
				if ($data[$disId]['id_first'] !== false && $comId < $data[$disId]['id_first']) {
					$data[$disId]['id_first'] = $comId;
					$data[$disId]['link']     = $item->get_link();
				}
				$data[$disId]['commentsTotal'] += 1;
			}

		}

		return $data;
	}

}