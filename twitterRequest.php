<?php
  $tweetID = $_POST['linkInput'];

  //connect to twitter
  require("vendor/autoload.php");
  use Abraham\TwitterOAuth\TwitterOAuth;
  define ('CONSUMER_KEY', 'KokOVScxygQ5IQNsTxQU2qw4Z');
  define('CONSUMER_SECRET', 'T8ONXHLNWtndPNZRl4alDGwE5AtL32mD2L9Pfm3WJR4djmZvvy');
  $access_token = '1195590715193135105-TMvE3xL0VoGZniJoVX8uefZdjBPeXU';
  $access_token_secret = 'oG32XzUx2mYTvIJTlpNHZoGtpNWbEJFBX4KCzeNly0PMf';
  $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token, $access_token_secret);
  $content = $connection->get("account/verify_credentials");
  $statuses = $connection->get("statuses/show", ["id" => $tweetID]);

  //get json array and parse through it
  $jsonArray = json_decode(json_encode($statuses), true);
  $videoURL = "";
  $errorMessage = "";
  $videoValid = true;
  try {
    if (isset($jsonArray["errors"])){
      throw new Exception($jsonArray["errors"]["0"]["message"]); //stop if twitter oauth error 
    } elseif (!isset($jsonArray["extended_entities"]["media"][0]["video_info"]["variants"])){
      throw new Exception("not a video"); //stop if not a video or gif
    } else { //get the video link
      $videoVariants = $jsonArray["extended_entities"]["media"][0]["video_info"]["variants"];
      $videoVariantsCount = count($videoVariants);
      $mp4Present = false;
      $variantIndex;
      for ($x = 0; $x <= $videoVariantsCount - 1; $x++){
          if ($videoVariants[$x]["content_type"] === 'video/mp4'){
              $mp4Present = true;
              $variantIndex = $x;
              $videoURL = $videoVariants[$variantIndex]["url"];
              break;
          }
      }
      if (!$mp4Present){
        throw new Exception("Incompatible video file type");
      }
    }
  } catch (Exception $e){
    $errorMessage = 'Message: ' .$e->getMessage();
    $videoValid = false;
  }

  if ($videoValid){ //response back to app.js, both if it works or did not work 
    echo json_encode(array(
      "valid" => true,
      "message" => $videoURL
    ));
  } else {
    echo json_encode(array(
      "valid" => false,
      "message" => $errorMessage
    ));
  }

?>
