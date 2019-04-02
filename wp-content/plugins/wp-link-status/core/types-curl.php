<?php

/**
 * Types CURL class
 *
 * @package WP Link Status
 * @subpackage Core
 */
class WPLNST_Core_Types_CURL {



	/**
	 * Retrieve info for a given error code
	 */
	public static function get_code_info($num) {
		$codes = self::get_codes();
		return isset($codes[$num])? $codes[$num] : false;
	}



	/**
	 * Retrieve all error codes
	 */
	public static function get_codes() {
		static $codes;
		if (!isset($codes)) {
			$codes = self::get_codes_array();
		}
		return $codes;
	}



	/**
	 * Retrieve array of error codes and descriptions
	 * http://curl.haxx.se/libcurl/c/libcurl-errors.html
	 */
	private static function get_codes_array() {
		return array(
			0  => array("code" => "CURLE_OK", 						"title" => __("Ok", "wplnst"),								"desc" => __("All fine. Proceed as usual.", "wplnst")),
			1  => array("code" => "CURLE_UNSUPPORTED_PROTOCOL", 	"title" => __("Unsupported protocol", "wplnst"), 			"desc" => __("The URL you passed to libcurl used a protocol that this libcurl does not support. The support might be a compile-time option that you didn't use, it can be a misspelled protocol string or just a protocol libcurl has no code for.", "wplnst")),
			2  => array("code" => "CURLE_FAILED_INIT", 				"title" => __("Failed initialization", "wplnst"), 			"desc" => __("Very early initialization code failed. This is likely to be an internal error or problem, or a resource problem where something fundamental couldn't get done at init time.", "wplnst")),
			3  => array("code" => "CURLE_URL_MALFORMAT", 			"title" => __("URL malformat", "wplnst"), 					"desc" => __("The URL was not properly formatted.", "wplnst")),
			4  => array("code" => "CURLE_NOT_BUILT_IN", 			"title" => __("Not built-in", "wplnst"), 					"desc" => __("A requested feature, protocol or option was not found built-in in this libcurl due to a build-time decision. This means that a feature or option was not enabled or explicitly disabled when libcurl was built and in order to get it to function you have to get a rebuilt libcurl.", "wplnst")),
			5  => array("code" => "CURLE_COULDNT_RESOLVE_PROXY", 	"title" => __("Couldn't resolve proxy", "wplnst"), 			"desc" => __("The given proxy host could not be resolved. ", "wplnst")),
			6  => array("code" => "CURLE_COULDNT_RESOLVE_HOST", 	"title" => __("Couldn't resolve host", "wplnst"), 			"desc" => __("The given remote host was not resolved.", "wplnst")),
			7  => array("code" => "CURLE_COULDNT_CONNECT", 			"title" => __("Couldn't connect", "wplnst"), 				"desc" => __("Failed to connect() to host or proxy.", "wplnst")),
			8  => array("code" => "CURLE_FTP_WEIRD_SERVER_REPLY", 	"title" => __("FTP weird server reply", "wplnst"), 			"desc" => __("After connecting to a FTP server, libcurl expects to get a certain reply back. This error code implies that it got a strange or bad reply. The given remote server is probably not an OK FTP server.", "wplnst")),
			9  => array("code" => "CURLE_REMOTE_ACCESS_DENIED", 	"title" => __("Remote access denied", "wplnst"), 			"desc" => __("We were denied access to the resource given in the URL. For FTP, this occurs while trying to change to the remote directory.", "wplnst")),
			10 => array("code" => "CURLE_FTP_ACCEPT_FAILED", 		"title" => __("FTP access failed", "wplnst"), 				"desc" => __("While waiting for the server to connect back when an active FTP session is used, an error code was sent over the control connection or similar.", "wplnst")),
			11 => array("code" => "CURLE_FTP_WEIRD_PASS_REPLY", 	"title" => __("FTP weird pass reply", "wplnst"), 			"desc" => __("After having sent the FTP password to the server, libcurl expects a proper reply. This error code indicates that an unexpected code was returned.", "wplnst")),
			12 => array("code" => "CURLE_FTP_ACCEPT_TIMEOUT", 		"title" => __("FTP accept timeout", "wplnst"), 				"desc" => __("During an active FTP session while waiting for the server to connect, the CURLOPT_ACCEPTTIMEOUT_MS (or the internal default) timeout expired.", "wplnst")),
			13 => array("code" => "CURLE_FTP_WEIRD_PASV_REPLY", 	"title" => __("FTP weird pasv reply", "wplnst"),	 		"desc" => __("libcurl failed to get a sensible result back from the server as a response to either a PASV or a EPSV command. The server is flawed.", "wplnst")),
			14 => array("code" => "CURLE_FTP_WEIRD_227_FORMAT", 	"title" => __("FTP weird 227 format", "wplnst"), 			"desc" => __("FTP servers return a 227-line as a response to a PASV command. If libcurl fails to parse that line, this return code is passed back.", "wplnst")),
			15 => array("code" => "CURLE_FTP_CANT_GET_HOST", 		"title" => __("FTP can't get host", "wplnst"), 				"desc" => __("An internal failure to lookup the host used for the new connection.", "wplnst")),
			16 => array("code" => "CURLE_HTTP2", 					"title" => __("HTTP2 framing layer problem", "wplnst"), 	"desc" => __("A problem was detected in the HTTP2 framing layer. This is somewhat generic and can be one out of several problems, see the error buffer for details.", "wplnst")),
			17 => array("code" => "CURLE_FTP_COULDNT_SET_TYPE", 	"title" => __("FTP couldn't set type", "wplnst"), 			"desc" => __("Received an error when trying to set the transfer mode to binary or ASCII.", "wplnst")),
			18 => array("code" => "CURLE_PARTIAL_FILE", 			"title" => __("Partial file", "wplnst"), 					"desc" => __("A file transfer was shorter or larger than expected. This happens when the server first reports an expected transfer size, and then delivers data that doesn't match the previously given size.", "wplnst")),
			19 => array("code" => "CURLE_FTP_COULDNT_RETR_FILE", 	"title" => __("FTP couldn't retrieve file", "wplnst"), 		"desc" => __("This was either a weird reply to a 'RETR' command or a zero byte transfer complete.", "wplnst")),
			21 => array("code" => "CURLE_QUOTE_ERROR", 				"title" => __("Quote error", "wplnst"), 					"desc" => __("When sending custom 'QUOTE' commands to the remote server, one of the commands returned an error code that was 400 or higher (for FTP) or otherwise indicated unsuccessful completion of the command.", "wplnst")),
			22 => array("code" => "CURLE_HTTP_RETURNED_ERROR", 		"title" => __("HTTP returned error", "wplnst"), 			"desc" => __("This is returned if CURLOPT_FAILONERROR is set TRUE and the HTTP server returns an error code that is >= 400.", "wplnst")),
			23 => array("code" => "CURLE_WRITE_ERROR", 				"title" => __("Write error", "wplnst"), 					"desc" => __("An error occurred when writing received data to a local file, or an error was returned to libcurl from a write callback.", "wplnst")),
			25 => array("code" => "CURLE_UPLOAD_FAILED", 			"title" => __("Upload failed", "wplnst"), 					"desc" => __("Failed starting the upload. For FTP, the server typically denied the STOR command. The error buffer usually contains the server's explanation for this.", "wplnst")),
			26 => array("code" => "CURLE_READ_ERROR", 				"title" => __("Read error", "wplnst"), 						"desc" => __("There was a problem reading a local file or an error returned by the read callback.", "wplnst")),
			27 => array("code" => "CURLE_OUT_OF_MEMORY", 			"title" => __("Out of memory", "wplnst"), 					"desc" => __("A memory allocation request failed. This is serious badness and things are severely screwed up if this ever occurs.", "wplnst")),
			28 => array("code" => "CURLE_OPERATION_TIMEDOUT", 		"title" => __("Operation timedout", "wplnst"), 				"desc" => __("The specified time-out period was reached according to the conditions.", "wplnst")),
			30 => array("code" => "CURLE_FTP_PORT_FAILED", 			"title" => __("FTP port failed", "wplnst"), 				"desc" => __("The FTP PORT command returned error. This mostly happens when you haven't specified a good enough address for libcurl to use. See CURLOPT_FTPPORT.", "wplnst")),
			31 => array("code" => "CURLE_FTP_COULDNT_USE_REST", 	"title" => __("FTP couldn't use REST", "wplnst"), 			"desc" => __("The FTP REST command returned error. This should never happen if the server is sane.", "wplnst")),
			33 => array("code" => "CURLE_RANGE_ERROR", 				"title" => __("Range error", "wplnst"), 					"desc" => __("The server does not support or accept range requests.", "wplnst")),
			34 => array("code" => "CURLE_HTTP_POST_ERROR", 			"title" => __("HTTP post error", "wplnst"), 				"desc" => __("This is an odd error that mainly occurs due to internal confusion.", "wplnst")),
			35 => array("code" => "CURLE_SSL_CONNECT_ERROR", 		"title" => __("SSL connect error", "wplnst"), 				"desc" => __("A problem occurred somewhere in the SSL/TLS handshake. You really want the error buffer and read the message there as it pinpoints the problem slightly more. Could be certificates (file formats, paths, permissions), passwords, and others.", "wplnst")),
			36 => array("code" => "CURLE_BAD_DOWNLOAD_RESUME", 		"title" => __("Bad download resume", "wplnst"), 			"desc" => __("The download could not be resumed because the specified offset was out of the file boundary.", "wplnst")),
			37 => array("code" => "CURLE_FILE_COULDNT_READ_FILE", 	"title" => __("File couldn't read file", "wplnst"), 		"desc" => __("A file given with FILE:// couldn't be opened. Most likely because the file path doesn't identify an existing file. Did you check file permissions?", "wplnst")),
			38 => array("code" => "CURLE_LDAP_CANNOT_BIND", 		"title" => __("LDAP cannot bind", "wplnst"), 				"desc" => __("LDAP cannot bind. LDAP bind operation failed.", "wplnst")),
			39 => array("code" => "CURLE_LDAP_SEARCH_FAILED", 		"title" => __("LDAP search failed", "wplnst"), 				"desc" => __("LDAP search failed.", "wplnst")),
			41 => array("code" => "CURLE_FUNCTION_NOT_FOUND", 		"title" => __("Function not found", "wplnst"), 				"desc" => __("Function not found. A required zlib function was not found.", "wplnst")),
			42 => array("code" => "CURLE_ABORTED_BY_CALLBACK", 		"title" => __("Aborted by callback", "wplnst"), 			"desc" => __("Aborted by callback. A callback returned 'abort' to libcurl.", "wplnst")),
			43 => array("code" => "CURLE_BAD_FUNCTION_ARGUMENT", 	"title" => __("Bad function argument", "wplnst"), 			"desc" => __("Internal error. A function was called with a bad parameter.", "wplnst")),
			45 => array("code" => "CURLE_INTERFACE_FAILED", 		"title" => __("Interface failed", "wplnst"), 				"desc" => __("Interface error. A specified outgoing interface could not be used. Set which interface to use for outgoing connections source IP address with CURLOPT_INTERFACE.", "wplnst")),
			47 => array("code" => "CURLE_TOO_MANY_REDIRECTS", 		"title" => __("Too many redirections", "wplnst"), 			"desc" => __("Too many redirects. When following redirects, libcurl hit the maximum amount. Set your limit with CURLOPT_MAXREDIRS.", "wplnst")),
			48 => array("code" => "CURLE_UNKNOWN_OPTION", 			"title" => __("Unknown option", "wplnst"), 					"desc" => __("An option passed to libcurl is not recognized/known. Refer to the appropriate documentation. This is most likely a problem in the program that uses libcurl. The error buffer might contain more specific information about which exact option it concerns.", "wplnst")),
			49 => array("code" => "CURLE_TELNET_OPTION_SYNTAX", 	"title" => __("Telnet option syntax", "wplnst"), 			"desc" => __("A telnet option string was Illegally formatted.", "wplnst")),
			51 => array("code" => "CURLE_PEER_FAILED_VERIFICATION", "title" => __("Peer failed verification", "wplnst"), 		"desc" => __("The remote server's SSL certificate or SSH md5 fingerprint was deemed not OK.", "wplnst")),
			52 => array("code" => "CURLE_GOT_NOTHING", 				"title" => __("Got nothing", "wplnst"), 					"desc" => __("Nothing was returned from the server, and under the circumstances, getting nothing is considered an error.", "wplnst")),
			53 => array("code" => "CURLE_SSL_ENGINE_NOTFOUND", 		"title" => __("SSL engine not found", "wplnst"), 			"desc" => __("The specified crypto engine wasn't found.", "wplnst")),
			54 => array("code" => "CURLE_SSL_ENGINE_SETFAILED", 	"title" => __("SSL engine set failed", "wplnst"), 			"desc" => __("Failed setting the selected SSL crypto engine as default!", "wplnst")),
			55 => array("code" => "CURLE_SEND_ERROR", 				"title" => __("Send error", "wplnst"), 						"desc" => __("Failed sending network data.", "wplnst")),
			56 => array("code" => "CURLE_RECV_ERROR", 				"title" => __("Receive error", "wplnst"), 					"desc" => __("Failure with receiving network data.", "wplnst")),
			58 => array("code" => "CURLE_SSL_CERTPROBLEM", 			"title" => __("Certificate problem", "wplnst"), 			"desc" => __("Problem with the local client certificate. ", "wplnst")),
			59 => array("code" => "CURLE_SSL_CIPHER", 				"title" => __("SSL cipher", "wplnst"), 						"desc" => __("Couldn't use specified cipher.", "wplnst")),
			60 => array("code" => "CURLE_SSL_CACERT", 				"title" => __("SSL CA certificate", "wplnst"), 				"desc" => __("Peer certificate cannot be authenticated with known CA certificates.", "wplnst")),
			61 => array("code" => "CURLE_BAD_CONTENT_ENCODING", 	"title" => __("Bad content encoding", "wplnst"), 			"desc" => __("Unrecognized transfer encoding.", "wplnst")),
			62 => array("code" => "CURLE_LDAP_INVALID_URL", 		"title" => __("LDAP invalid URL", "wplnst"), 				"desc" => __("Invalid LDAP URL.", "wplnst")),
			63 => array("code" => "CURLE_FILESIZE_EXCEEDED", 		"title" => __("File size exceeded", "wplnst"), 				"desc" => __("Maximum file size exceeded.", "wplnst")),
			64 => array("code" => "CURLE_USE_SSL_FAILED", 			"title" => __("Use SSL failed", "wplnst"), 					"desc" => __("Requested FTP SSL level failed. ", "wplnst")),
			65 => array("code" => "CURLE_SEND_FAIL_REWIND", 		"title" => __("Send fail rewind", "wplnst"), 				"desc" => __("When doing a send operation curl had to rewind the data to retransmit, but the rewinding operation failed.", "wplnst")),
			66 => array("code" => "CURLE_SSL_ENGINE_INITFAILED", 	"title" => __("SSL initialization failed", "wplnst"), 		"desc" => __("Initiating the SSL Engine failed.", "wplnst")),
			67 => array("code" => "CURLE_LOGIN_DENIED", 			"title" => __("Login denied", "wplnst"), 					"desc" => __("The remote server denied curl to login (Added in 7.13.1)", "wplnst")),
			68 => array("code" => "CURLE_TFTP_NOTFOUND", 			"title" => __("TFTP file not found", "wplnst"), 			"desc" => __("File not found on TFTP server.", "wplnst")),
			69 => array("code" => "CURLE_TFTP_PERM", 				"title" => __("TFTP permission", "wplnst"), 				"desc" => __("Permission problem on TFTP server.", "wplnst")),
			70 => array("code" => "CURLE_REMOTE_DISK_FULL", 		"title" => __("Remote disk full", "wplnst"), 				"desc" => __("Out of disk space on the server.", "wplnst")),
			71 => array("code" => "CURLE_TFTP_ILLEGAL", 			"title" => __("TFTP illegal", "wplnst"), 					"desc" => __("Illegal TFTP operation.", "wplnst")),
			72 => array("code" => "CURLE_TFTP_UNKNOWNID", 			"title" => __("FTP unknown ID", "wplnst"), 					"desc" => __("Unknown TFTP transfer ID.", "wplnst")),
			73 => array("code" => "CURLE_REMOTE_FILE_EXISTS", 		"title" => __("Remote file exists", "wplnst"), 				"desc" => __("File already exists and will not be overwritten.", "wplnst")),
			74 => array("code" => "CURLE_TFTP_NOSUCHUSER", 			"title" => __("TFTP no such user", "wplnst"), 				"desc" => __("This error should never be returned by a properly functioning TFTP server.", "wplnst")),
			75 => array("code" => "CURLE_CONV_FAILED", 				"title" => __("Conversion failed", "wplnst"), 				"desc" => __("Character conversion failed.", "wplnst")),
			76 => array("code" => "CURLE_CONV_REQD", 				"title" => __("Conversion callbacks", "wplnst"), 			"desc" => __("Caller must register conversion callbacks.", "wplnst")),
			77 => array("code" => "CURLE_SSL_CACERT_BADFILE", 		"title" => __("SSL CA certificate bad file", "wplnst"), 	"desc" => __("Problem with reading the SSL CA cert (path? access rights?)", "wplnst")),
			78 => array("code" => "CURLE_REMOTE_FILE_NOT_FOUND", 	"title" => __("Remote file not found", "wplnst"), 			"desc" => __("The resource referenced in the URL does not exist.", "wplnst")),
			79 => array("code" => "CURLE_SSH", 						"title" => __("SSH error", "wplnst"), 						"desc" => __("An unspecified error occurred during the SSH session.", "wplnst")),
			80 => array("code" => "CURLE_SSL_SHUTDOWN_FAILED", 		"title" => __("SSL shutdown failed", "wplnst"), 			"desc" => __("Failed to shut down the SSL connection.", "wplnst")),
			81 => array("code" => "CURLE_AGAIN", 					"title" => __("Socket not ready", "wplnst"), 				"desc" => __("Socket is not ready for send/recv wait till it's ready and try again. This return code is only returned from curl_easy_recv and curl_easy_send (Added in 7.18.2)", "wplnst")),
			82 => array("code" => "CURLE_SSL_CRL_BADFILE", 			"title" => __("SSL CRL bad file", "wplnst"), 				"desc" => __("Failed to load CRL file (Added in 7.19.0)", "wplnst")),
			83 => array("code" => "CURLE_SSL_ISSUER_ERROR", 		"title" => __("SSL issue error", "wplnst"), 				"desc" => __("Issuer check failed (Added in 7.19.0)", "wplnst")),
			84 => array("code" => "CURLE_FTP_PRET_FAILED", 			"title" => __("FTP PRET failed", "wplnst"), 				"desc" => __("The FTP server does not understand the PRET command at all or does not support the given argument. Be careful when using CURLOPT_CUSTOMREQUEST, a custom LIST command will be sent with PRET CMD before PASV as well. (Added in 7.20.0)", "wplnst")),
			85 => array("code" => "CURLE_RTSP_CSEQ_ERROR", 			"title" => __("RTSP CSEQ error", "wplnst"), 				"desc" => __("Mismatch of RTSP CSeq numbers.", "wplnst")),
			86 => array("code" => "CURLE_RTSP_SESSION_ERROR", 		"title" => __("RTSP session error", "wplnst"), 				"desc" => __("Mismatch of RTSP Session Identifiers.", "wplnst")),
			87 => array("code" => "CURLE_FTP_BAD_FILE_LIST", 		"title" => __("FTP bad file list", "wplnst"), 				"desc" => __("Unable to parse FTP file list (during FTP wildcard downloading).", "wplnst")),
			88 => array("code" => "CURLE_CHUNK_FAILED", 			"title" => __("Chunk failed", "wplnst"), 					"desc" => __("Chunk callback reported error.", "wplnst")),
			89 => array("code" => "CURLE_NO_CONNECTION_AVAILABLE", 	"title" => __("No connection available", "wplnst"), 		"desc" => __("(For internal use only, will never be returned by libcurl) No connection available, the session will be queued. (added in 7.30.0)", "wplnst")),
		);
	}



}