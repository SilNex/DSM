<?php
class SynoDLMSearchTorrentKim {
	private $qurl;

	public function __construct() {
		$context = stream_context_create(
			array(
				'http' => array(
					'follow_location' => false
				)
			)
		);
		file_get_contents("https://torrentkim.net/", false, $context);
		$this->qurl=substr($http_response_header[5], 10)."/bbs/rss.php?k=";
	}

	public function prepare($curl, $query) {
		$url = $this->qurl . urlencode($query);
		curl_setopt($curl, CURLOPT_URL, $url);
	}

	public function parse($plugin, $response) {
		$response = preg_replace("/<pubDate>/i", "<pubDate>" . date("r"), $response);
		$response = preg_replace("/<\/pubDate>/i", "</pubDate><category>All</category>", $response);
		$response = preg_replace("/<description><\/description>/i", "<description><![CDATA[Category: All<br />Subcategory: All]]></description>", $response);
		$response = preg_replace("/\&dn=.*?(?=&tr)/i", "", $response);
		$response = preg_replace("/&/i", "%26", $response);
		return $plugin->addRSSResults($response);
	}
}
?>
