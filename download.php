<?php
if(isset($_GET['url']) && !empty($_GET['url'])) {
    $videoUrl = $_GET['url'];
    
    // Validate YouTube URL
    if (preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)([\w-]{11})$/', $videoUrl, $matches)) {
        $videoId = $matches[4];
        
        // Fetch video information
        $videoInfo = file_get_contents("https://www.youtube.com/get_video_info?video_id={$videoId}");
        
        parse_str($videoInfo, $videoData);
        
        // Check if video is available
        if ($videoData['status'] == 'ok' && isset($videoData['url_encoded_fmt_stream_map'])) {
            // Get the URL of the video stream
            $streamMap = explode(',', $videoData['url_encoded_fmt_stream_map']);
            $streamData = [];
            foreach ($streamMap as $stream) {
                parse_str($stream, $streamData[]);
            }
            
            // Choose the highest quality video stream
            $videoStream = $streamData[0]; // Assuming the first stream is the highest quality
            
            // Set headers to force download
            header("Content-Type: video/mp4");
            header("Content-Disposition: attachment; filename=\"video.mp4\"");
            
            // Download the video
            readfile($videoStream['url']);
            
            exit;
        } else {
            echo "Error: Video is not available for download.";
        }
    } else {
        echo "Error: Invalid YouTube URL.";
    }
} else {
    echo "Error: Video URL parameter is missing.";
}
?>
