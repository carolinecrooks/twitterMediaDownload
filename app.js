//get submit button, input bar, and php code
var submit = document.getElementById("submitButton");
var inputBar = document.getElementById("linkInput");
var phpLink = "twitterRequest.php";
var tweetURL;
var tweetID;

//when submit is clicked
submit.addEventListener("click", function (e) {
  console.log("submit");
  e.preventDefault();
  tweetURL = inputBar.value;
  inputBar.value = ""; //clear input bar text
  if (tweetURL.includes("twitter") && tweetURL.includes("status")) {
    //check if a valid twitter link
    tweetID = getID(tweetURL);
    console.log("tweetID: " + tweetID);
    var hr = new XMLHttpRequest();
    hr.onreadystatechange = function () {
      if (this.readyState === 4 && this.status === 200) {
        inputBar.value = "";
        var response = JSON.parse(this.response); //handle php response
        console.log(response);
        if (response.valid) {
          var formattedURL = formatURL(response.message);
          console.log(formattedURL);
          downloadMedia(formattedURL, tweetID);
          inputBar.setAttribute("placeholder", ""); //clear text
        } else {
          errorMessage(response.message);
        }
      }
    };

    //handle request with php
    hr.open("POST", phpLink, true);
    hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    hr.responseType = "text";
    hr.send("linkInput=" + tweetID);
  } else {
    errorMessage("input a video link");
  }
});

function errorMessage(message) {
  //error if not a twitter video
  console.log(message);
  inputBar.setAttribute("placeholder", "input a twitter video link");
}

function getID(url) {
  //get tweet ID from the link
  var firstIndex = url.indexOf("status") + 7;
  var lastIndex = firstIndex;
  while (!isNaN(url[lastIndex])) {
    lastIndex++;
  }
  return url.slice(firstIndex, lastIndex);
}

function formatURL(url) {
  return url.replace("/", "/");
}

async function downloadMedia(twitterURL, fileName) {
  //create Blob from the video link and download to computer
  const response = await fetch(twitterURL, {
    method: "GET",
    mode: "cors",
  });
  const myBlob = await response.blob();
  const url = window.URL.createObjectURL(new Blob([myBlob]));
  const a = document.createElement("a");
  a.href = url;
  a.setAttribute("download", fileName + ".mp4");
  document.body.appendChild(a);
  a.click();
  a.parentNode.removeChild(a);
}
