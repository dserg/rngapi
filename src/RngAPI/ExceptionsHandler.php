<?php
namespace RngAPI;
class ExceptionsHandler 
{
	function basicHandle(string $message) {
		Helper::respond([
			"error" => $message
		]);
		exit();
	}

	function statusCodeHandle(int $code) {
		http_response_code($code);
		exit();
	}
}