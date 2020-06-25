<?php
  $tweetID = $_POST['linkInput'];
  require("twitteroauth/autoload.php");
  use Abraham\TwitterOAuth\TwitterOAuth;

  define ('CONSUMER_KEY', '');
  define('CONSUMER_SECRET', '');
  $access_token = '';
  $access_token_secret = '';

  $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token, $access_token_secret);
  $content = $connection->get("account/verify_credentials");
  $statuses = $connection->get("statuses/show", ["id" => $tweetID]);
  $jsonArray = json_decode(json_encode($statuses), true);
  $videoURL = "";
  $errorMessage = "";
  $videoValid = true;

  try {
    if (isset($jsonArray["errors"])){
      throw new Exception($jsonArray["errors"]["0"]["message"]);
    } elseif (!isset($jsonArray["extended_entities"]["media"][0]["video_info"]["variants"])){
      throw new Exception("not a video");
    } else {
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

  if ($videoValid){
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
