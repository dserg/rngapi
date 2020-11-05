<?php
class ExceptionsHandler 
{
	function basicHandle($message) {
		Helper::respond([
			"error" => $message
		]);
		exit();
	}

	function statusCodeHandle($code) {
		http_response_code($code);
		exit();
	}
}