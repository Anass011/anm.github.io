<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	// Get the video link from the form
	$video_link = $_POST["video-link"];

	// Validate the video link
	if (filter_var($video_link, FILTER_VALIDATE_URL) === FALSE) {
		echo "Invalid video link";
		exit;
	}

	// Extract the video ID from the link
	$video_id = "";
	if (strpos($video_link, "youtube.com") !== FALSE) {
		// YouTube video link
		parse_str(parse_url($video_link, PHP_URL_QUERY), $query_params);
		$video_id = $query_params["v"];
	} elseif (strpos($video_link, "vimeo.com") !== FALSE) {
		// Vimeo video link
		$video_id = substr(parse_url($video_link, PHP_URL_PATH), 1);
	} else {
		echo "Unsupported video source";
		exit;
	}

	// Download the video file
	$file_name = "video-" . $video_id . ".mp4";
	$file_path = "downloads/" . $file_name;
	if (file_put_contents($file_path, file_get_contents($video_link)) === FALSE) {
		echo "Error downloading video";
		exit;
	}

	// Serve the video file for download
	header("Content-Disposition: attachment; filename=\"" . $file_name . "\"");
	header("Content-Type: application/octet-stream");
	header("Content-Length: " . filesize($file_path));
	readfile($file_path);

	// Delete the video file
	unlink($file_path);

	exit;
}
