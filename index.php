<?php
/*
    Welcome to some speed coded php code, aka, spaghetti.
*/

//
// Connecting, selecting database
//
$mysqli = new mysqli(xxx);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL";
}

//
// Get FORM/POST parameters
//
$inputName = htmlspecialchars($_POST["inputName"]);
$inputPhone = htmlspecialchars($_POST["inputPhone"]);
$inputEmail = htmlspecialchars($_POST["inputEmail"]);
$inputDesc = htmlspecialchars($_POST["inputDesc"]);
if (array_key_exists("inputTerms", $_POST))
    $inputTerms = htmlspecialchars($_POST["inputTerms"]);
else
    $inputTerms = False;

$error = array();
$inputName = filter_var($inputName, FILTER_SANITIZE_STRING);
$inputPhone = filter_var($inputPhone, FILTER_SANITIZE_STRING);
$inputDesc = filter_var($inputDesc, FILTER_SANITIZE_STRING);

if (empty($inputName))
    $error['inputName'] = "Du måste ange ett namn.";

if (empty($inputPhone))
    $error['inputPhone'] = "Du måste ange ett telefonnummer.";

if (!filter_var($inputEmail, FILTER_VALIDATE_EMAIL))
    $error['inputEmail'] = "Du måste ange en korrekt epost address.";

if (empty($inputDesc))
    $error['inputDesc'] = "Du måste ange en objket beskrivning.";

if (!filter_var($inputTerms, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE))
    $error['inputTerms'] = "Du måste acceptera villkoren.";


//
// Handle the storage of the new object
//
if ($inputTerms)
{
    if (!($stmt = $mysqli->prepare("INSERT INTO object(name, phone, email, description) VALUES (?, ?, ?, ?)"))) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    else
    {
        $stmt->bind_param('ssss', $inputName, $inputPhone, $inputEmail, $inputDesc);
        $stmt->execute();
        $stmt->close();
    }

}


function has_error($name)
{
    global $error;
    if (!empty($error[$name]))
        echo "has-error";
}

function print_error($name)
{
    global $error;
    if (!empty($error[$name]))
        echo '<span class="help-inline">' . $error[$name] . '</span>';
}


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Auktion, Kalvenäs kött, högsby, berga, oskarshamn">
    <meta name="author" content="Daniel Lindh - Amivono AB - www.renter.se">

    <title>Auktion på Kalvenäs Kött</title>

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="dashboard.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" href="#">Auktion på Kalvenäs Kött</a>

            <ul class="nav navbar-nav">
              <li><a href="https://www.google.se/maps/place/Sjönäsgatan+25A/@57.2119511,16.0575106,17z" target="_blank">Hitta hit</a></li>
            </ul>

        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row">

        <div class="col-sm-9 col-md-10 main">
          <div class="jumbotron">
            <img src="tractor.jpg" class="img-thumbnail">
            <h1>Välkommen till inlämningsauktion på Kalvenäs Kött</h1>
            <p>Auktionen sker hos Kalvenäs Kött på Sjönäsgatan 25A Berga,
              lördag 26 april 10.00 - 15.00. Auktionsutropare är Kenneth Sjökvist.
              Vi tar emot anmälan av objekt via denna websida fram tilll fredag 18 april 24:00.
              Alla objekt ska finnas på Sjönäsgatan 25A senast onsdag 23 april 18:00,
              transport ansvarar objektägaren för.
            </p>
            <p><small>För mer information kontakta Patrik på 070-515 30 11 eller skicka ett epost till info@kalvenas.se</small></p>
          </div>



          <h2 class="sub-header">Objekt</h2>

          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Namn</th>
                  <th>Telefon</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($res = $mysqli->query("SELECT * FROM object ORDER BY object_id ASC")) {?>
                    <?php while ($row = $res->fetch_assoc()) { ?>

                        <tr>
                          <td><?= $row['object_id'] ?></td>
                          <td><?= $row['name'] ?></td>
                          <td><?= $row['phone'] ?></td>
                        </tr>
                        <tr>
                          <td colspan="5"  class="header-desc">
                            <?= $row['description'] ?>
                          </td>
                        </tr>

                    <?php }  ?>
                <?php } else {printf("Error: %s\n", $mysqli->error);} ?>

              </tbody>
            </table>
          </div>
          <hr>
        </div>

        <div class="col-sm-3 col-md-2 sidebar">
          <h2 class="sub-header">Nytt objekt</h2>
          <form role="form" method="post">
            <div class="form-group <?= has_error('inputName') ?>">
              <input type="text" class="form-control" name="inputName" placeholder="För och efternamn" value="<?= $inputName ?>">
              <?= print_error('inputName') ?>
            </div>

            <div class="form-group <?= has_error('inputPhone') ?>">
              <input type="text" class="form-control" name="inputPhone" placeholder="Telefonnummer" value="<?= $inputPhone ?>">
              <?= print_error('inputPhone') ?>
            </div>

            <div class="form-group <?= has_error('inputEmail') ?>">
              <input type="text" class="form-control" name="inputEmail" placeholder="Epost" value="<?= $inputEmail ?>">
              <?= print_error('inputEmail') ?>
            </div>

            <div class="form-group <?= has_error('inputDesc') ?>">
              <textarea class="form-control" rows="10" name="inputDesc" placeholder="Beskrivning"><?= $inputDesc ?></textarea>
              <?= print_error('inputinputDescName') ?>
            </div>

            <div class="form-group <?= has_error('inputTerms') ?>">
              <textarea class="form-control" rows="5" disabled>
AUKTIONSVILLKOR

3 mån räntefri kredit till alla av oss kreditgodkända köpare. Övriga som önskar kredit skall senast 3 dagar före auktionen kontakta oss och därefter översända kreditvärdighetsintyg från Er bank. I övrigt gäller kontant betalning, 25% moms tillkommer. Köparen ansvarar för varan från och med klubbslaget. Inga nya kreditansökningar auktionsdagen. Äganderättsförebehåll och återtagningsrättsförebehåll gäller tills fullo betalning skett. Vid fakturering tillkommer 75:-
              </textarea>
              <div class="checkbox">
                <label><input name="inputTerms" type="checkbox"> Jag godkänner villkoren.</label>
              </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-default">Skicka</button>
            </div>
          </form>
        </div>

      </div>

    </div>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./assets/js/docs.min.js"></script>
  </body>
</html>


<?php

//
// Tear down
//

$mysqli->close();
?>