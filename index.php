<?php

  session_start();

  require_once 'classes/Token_Generator.php';
  require_once 'classes/Identifier_Generator.php';
  require_once 'classes/Database_Handler.php';

  $database = new Database;
  $output = 'Create a link now.';



  // If an ID is provided check the database for a match and forward to the coresponding URL.
  if (isset($_GET['l']) && !empty($_GET['l'])) {

    $ID = $_GET['l'];
    global $database;

    $queryTemplate = 'SELECT * FROM links WHERE id = :ID';
    $queryData = ['ID' => $ID];
    $queryResult = $database->getData($queryTemplate, $queryData);

    $url = $queryResult['url'];

    if(!empty($url)) {
      if (substr($url, 0, 7) === 'http://' || substr($url, 0, 8) === 'https://') { header('Location:'.$url); }
      else { header('Location:http://'.$url); }
    }
    else { header('Location: /'); }

  }



  // On a POST request check if all required fields are populated and compare the provided CSFR token with the session's token.
  if (isset($_POST['url'], $_POST['token']) && !empty($_POST['url'])) {
    if (Token::check($_POST['token'])) {


      $newURL = $_POST['url'];



      function checkForData ($type, $value) {
        switch ($type) {

          case id:
            global $database;
            $queryTemplate = 'SELECT id FROM links WHERE id = :ID';
            $queryData = ['ID' => $value];
            $queryResult = $database->getData($queryTemplate, $queryData);
            if (!empty($queryResult)) { checkForData('id', ID::generate()); }
            else { return $value; }
            break;

          case url:
            global $database;
            $queryTemplate = 'SELECT url FROM links WHERE url = :URL';
            $queryData = ['URL' => $value];
            $queryResult = $database->getData($queryTemplate, $queryData);
            if (!empty($queryResult)) { return TRUE; }
            else { return FALSE; }
            break;

        }
      }



      // If the given URL is already known output the coresponding ID.
      if (checkForData('url', $newURL)) {

        global $database;

        $queryTemplate = 'SELECT id FROM links WHERE url = :URL';
        $queryData = ['URL' => $newURL];
        $queryResult = $database->getData($queryTemplate, $queryData);


        if (!empty($queryResult)) { $output = idn_to_utf8($_SERVER[HTTP_HOST]).'/'.$queryResult['id']; }
        else { $output = 'Something went wrong. Please try again.'; }

      }

      // If the given URL does not yet exist generate a new ID and save it to the database.
      else {

        $newID = checkForData('id', ID::generate());

        global $database;

        $queryTemplate = 'INSERT INTO links (id, url) VALUES (:ID, :URL)';
        $queryData = ['ID' => $newID, 'URL' => $newURL];
        $queryResult = $database->placeData($queryTemplate, $queryData);

        if ($queryResult == TRUE) { $output = idn_to_utf8($_SERVER[HTTP_HOST]).'/'.$newID; }
        else { $output = 'Something went wrong. Please try again.'; }

      }

    }
  }



?><!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>URL Shortener</title>

  <link rel="stylesheet" href="https://necolas.github.io/normalize.css/8.0.1/normalize.css">
  <link rel="stylesheet" href="assets/css/main.css">

</head>
<body>

  <div class="desktop-icon">
    <img src="assets/img/icon.png">
    <p>Shortlink<br>Generator</p>
  </div>

  <a class="desktop-icon" href="https://unseen.ninja">
    <img src="assets/img/icon-2.png">
    <p>unseen<br>ninja</p>
  </a>

  <div class="wrapper">
    <div class="topbar">
      <div class="bar-button minimize">-</div>
      <div class="bar-button close">×</div>
    </div>
    <h1>Shortlink Generator</h1>
    <p class="output"><?php echo $output; ?></p>
    <form action="" method="post">
      <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
      <input id="url" name="url" type="text" placeholder="URL">
      <button type="submit">shorten</button>
    </form>
  </div>

</body>
</html>