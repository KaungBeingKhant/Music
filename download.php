<?php
// Function to get video info
function getYouTubeVideoInfo($videoId) {
    $videoInfoUrl = "https://www.youtube.com/get_video_info?video_id={$videoId}";
    $videoInfo = file_get_contents($videoInfoUrl);
    parse_str($videoInfo, $videoData);
    return $videoData;
}

// Function to choose highest quality video stream
function getHighestQualityStream($streamData) {
    $streams = [];
    foreach ($streamData as $stream) {
        parse_str($stream, $streamInfo);
        $streams[] = $streamInfo;
    }
    // Sort streams by quality
    usort($streams, function($a, $b) {
        return $b['quality'] - $a['quality'];
    });
    return $streams[0]; // Return the highest quality stream
}

// Main code
if(isset($_GET['url']) && !empty($_GET['url'])) {
    $videoUrl = $_GET['url'];
    
    // Validate YouTube URL
    if (preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)([\w-]{11})$/', $videoUrl, $matches)) {
        $videoId = $matches[4];
        
        // Get video information
        $videoData = getYouTubeVideoInfo($videoId);
        
        // Check if video is available
        if (isset($videoData['status']) && $videoData['status'] == 'ok' && isset($videoData['url_encoded_fmt_stream_map'])) {
            // Get video streams
            $streamMap = explode(',', $videoData['url_encoded_fmt_stream_map']);
            
            // Choose highest quality stream
            $videoStream = getHighestQualityStream($streamMap);
            
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
