

var submit = document.getElementById("submitButton");
var phpLink = "twitterRequest.php";
var inputBar = document.getElementById("linkInput");
var downloadIcon = document.getElementById("downloadIcon");
var tweetURL;
var tweetID;

submit.addEventListener("click", function(e){
  console.log('submit');
  e.preventDefault();
  tweetURL = inputBar.value;
  inputBar.value = "";
  if (tweetURL.includes("twitter") && tweetURL.includes("status")){
    tweetID = getID(tweetURL);
    console.log('tweetID: ' + tweetID);
    var hr = new XMLHttpRequest();
    hr.onreadystatechange = function(){
      if (this.readyState === 4 && this.status === 200){
        inputBar.value = "";
        var response = JSON.parse(this.response);
        if (response.valid){
          var formattedURL = formatURL(response.message);
          window.open(formattedURL, '_blank');
          inputBar.setAttribute("placeholder", "");
          downloadIcon.style.display = "block";
          downloadIcon.setAttribute("href", formattedURL);
        } else {
          errorMessage(response.message);
        }
      };
    };
    hr.open("POST", phpLink, true);
    hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    hr.responseType="text";
    hr.send("linkInput=" + tweetID);
  } else {
    errorMessage("input a video link");
  }
});

function errorMessage(message){
  console.log(message);
  inputBar.setAttribute("placeholder", "input a twitter video link");
  downloadIcon.style.display = 'none';
  downloadIcon.setAttribute("href", "");
};

function getID(url){
  var firstIndex = url.indexOf("status") + 7;
  var lastIndex = firstIndex;
  while (!isNaN(url[lastIndex])){
    lastIndex++;
  }
  return url.slice(firstIndex, lastIndex);
}

function formatURL(url){
  return url.replace("\/", "/");
}
