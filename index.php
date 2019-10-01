<?php
require 'app/autoload.php';
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
   $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/fb/storage/pictures/";
   $target_file = $target_dir . basename($_FILES["image"]["name"]);
   $uploadOk = 1;
   $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["image"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <?php include 'app/modules/essentials/header.php'; ?>
   <title>SFS</title>
</head>
<body onload="loadPage()">
   <div id="app-container"></div>
   <form action="index.php" method="post" id="upload-image" enctype="multipart/form-data">
      <input type="file" name="image" id="image">
      <button type="submit" name="submit">send</button>
   </form>
   <script>
      $("#upload-image").on('submit', function(e) {
         e.preventDefault();
         $.ajax({
            url: "/fb/app/ajax/upload.php",
            type: 'POST',
            processData: false,
            contentType: false,
            data: new FormData(this),
            beforeSend: function() {
               $("#app-container").html('Loading...');
            },
            success: function(data) {
               $('#app-container').html(data)
            }
         });
      });

      function loadPage() {
         let url = window.location.href.substring(24);
         // console.log('url before: ' + url)
         // console.log('url last element: ' + url.slice(-1))
         if (url.slice(-1) == "/" && window.location.href.substring(24) != "/") {
            url = url.slice(0, -1);
         }
         // console.log('url after: ' + url)
         $.ajax({
            url: "/fb/app/ajax/page.php?p="+url+"&specials=loadNav",
            processData: false,
            contentType: "application/json",
            type: 'GET',
            beforeSend: function() {
               $("#app-container").html('Loading...');
            },
            success: function(data) {
               $('#app-container').html(data)
               refreshLinks(url);
            }
         });
      }

      function refreshLinks(url) {
         $('[data-link]').click(function(e) {
            e.preventDefault();
            let button = $(this).attr('data-link');
            // console.log('last element: ' + button.slice(-1))
            if (button.slice(-1) == "/") {
               button.substring(-1);
            }
            // console.log('last element after: ' + button.slice(-1))
            if (button != url) {
               window.history.pushState(null, 'SFS', '/fb'+button);
               url = window.location.href.substring(24);
               // console.log(url + "\n" + button)
               $.ajax({
                  url: "/fb/app/ajax/page.php?p="+button,
                  processData: false,
                  contentType: "application/json",
                  type: 'GET',
                  success: function(data) {
                     $("#body-container").html(data);
                     refreshLinks(url);
                  }
               })
            }
         })
      }

   </script>
</body>
</html>
