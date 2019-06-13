<!DOCTYPE html>

<html>
  <head>
    <meta charset="utf-8">
    <title>Kontaktverwaltung</title>
    <?php
    if(session_id() == '' || !isset($_SESSION))
    {
      // session isn't started
      session_start();
    }
    $server = 'mysql:dbname=kontaktverwaltung;
    host=localhost';
    $user='root';
    $options =array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
    $pdo = new PDO ($server, $user,'',);

    $modifizierungsZaehler = 0;

    if(isset($_GET['login']))
    {
      session_destroy();
      session_start();
      $username = $_POST['username'];
      $password = $_POST['password'];
      $statement = $pdo->prepare("SELECT * FROM logindaten WHERE Benutzername = :username OR Email = :username");
      $statement->execute(array('username' =>$username, 'email' => $username));
      $user = $statement->fetch();
      $hash = $user['Passwort'];

      //ueberpruefung des Passworts
      if($user !== false && password_verify($password, $hash))
      {
        $_SESSION['userid'] = $user['ID'];
        $_SESSION['email'] = $user['Email'];
        $_SESSION['passwort'] = $user['Passwort'];
        $_SESSION['benutzername'] = $user['Benutzername'];
        $_SESSION['loggedIn'] = true;
      }
      else
      {
        if(isset($user['Benutzername']))
        {
          $errorMessage = $user['Benutzername'];
        }
        else
        {
          $errorMessage = "Kein Nutzer vorhanden";
        }
      }
    }

    if(isset($_GET["registrieren"]))
    {
      session_destroy();
      session_start();
      $error = false;
      $email = $_POST['email'];
      $username = $_POST['username'];
      $password = $_POST['password'];

      if(!filter_var($email, FILTER_VALIDATE_EMAIL))
      {
        $errorMessage = "Bitte eine gueltige E-Mail-Adresse eingeben";
      }

      if(strlen($password) <= 7)
      {
        $errorMessage = "Passwort muss 7 zeichen lang sein";
      }

      if(!$error)
      {
        $statement = $pdo->prepare("SELECT * FROM logindaten WHERE Email = :email OR Benutzername = :nutzer");
        $statement->execute(array('email' => $email, 'nutzer' => $username));
        $user = $statement->fetch();

        if($user !== false)
        {
          $errorMessage = "Diese E-Mail-Adresse oder dieser Benutzername ist bereits vergeben";
        }
      }

      if(!$error)
      {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $admin = 0;
        $statement = $pdo->prepare("INSERT INTO logindaten (Benutzername, Passwort, Email) VALUES (:username, :password, :email)");
        $result = $statement->execute(array('username' =>$username, 'password' => $password_hash, 'email' => $email));

        $statement = $pdo->prepare("SELECT * FROM logindaten WHERE Benutzername = :username OR Email = :username");
        $statement->execute(array('username' =>$username, 'email' => $username));
        $user = $statement->fetch();
        $hash = $user['Passwort'];

        if($user !== false && password_verify($password, $hash) && $result == true)
        {
          $_SESSION['userid'] = $user['ID'];
          $_SESSION['email'] = $user['Email'];
          $_SESSION['passwort'] = $user['Passwort'];
          $_SESSION['benutzername'] = $user['Benutzername'];
          $_SESSION['loggedIn'] = true;
        }
      else
      {
        echo 'Beim Registrieren gab es einen Fehler';
      }
      }
    }

    if(isset($_GET['logout']))
    {
      session_destroy();
      session_start();
    }

      $kontaktArray = array();
      $databaseEnd = false;
      $zaehler = 1;

      if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true)
      {
        while($databaseEnd == false)
        {
          $statement = $pdo->prepare("SELECT * FROM kontakt WHERE ID = :zaehler");
          $result = $statement->execute(array('zaehler' => $zaehler));
          $kontakt = $statement->fetch();

          if($kontakt == false)
          {
            $databaseEnd = true;
          }
          $statement = $pdo->prepare("SELECT * FROM ort WHERE ID = :adresse");
          $statement->execute(array('adresse' => $kontakt["Adresse"]));
          $ortDB = $statement->fetch();
          $statement = $pdo->prepare("SELECT * FROM strasse WHERE OrtNr = :adresse");
          $statement->execute(array('adresse' => $kontakt["Adresse"]));
          $straßeDB = $statement->fetch();

          $zaehler += 1;

          if($kontakt['Nutzer'] == $_SESSION['userid'])
          {
            $id = $kontakt["ID"];
            $vorname = $kontakt["Vorname"];
            $nachname = $kontakt["Nachname"];
            $ort = $ortDB["Ort"];
            $plz = $ortDB["Postleitzahl"];
            $straße = $straßeDB["StrassenName"];
            $hausnummer = $straßeDB["Hausnummer"];
            $email = $kontakt["Email"];
            $telefon = $kontakt["Telefonnummer"];

            $kontaktArray[] = "$vorname;$nachname;$ort;$plz;$straße;$hausnummer;$email;$telefon;$id";
          }
        }


        if(isset($_GET['hinzufuegen']))
        {
          $vorname = $_POST["vorname"];
          $nachname = $_POST["nachname"];
          $ort = $_POST["ort"];
          $plz = $_POST["plz"];
          $straße = $_POST["straße"];
          $hausnummer = $_POST["hausnummer"];
          $email = $_POST["email"];
          $telefon = $_POST["telefonnummer"];
          $addresse;


        if($vorname == null || $nachname == null || $ort == null || $plz == null || $straße == null || $hausnummer == null || $email == null || $telefon == null)
        {
          $errorMessage = "Bitte fülle alle Felder aus";
        }
        else
        {
          $statement = $pdo->prepare("SELECT * FROM ort WHERE Ort = :ort");
          $result = $statement->execute(array('ort' => $ort));
          $ortDB = $statement->fetch();

          if($ortDB == true)
          {
            $addresse = $ortDB["ID"];
          }
          else
          {
            $statement = $pdo->prepare("INSERT INTO ort (Ort, Postleitzahl) VALUES(:ort, :plz)");
            $result = $statement->execute(array('ort' => $ort, 'plz' => $plz));

            $statement = $pdo->prepare("SELECT * FROM ort WHERE Ort = :ort");
            $result = $statement->execute(array('ort' => $ort));
            $ortDB = $statement->fetch();
            $addresse = $ortDB["ID"];
          }

          $statement = $pdo->prepare("INSERT INTO kontakt (Vorname, Nachname, Adresse, Telefonnummer, Email, Nutzer) VALUES(:vorname, :nachname, :addresse, :telefon, :email, :nutzer)");
          $statement->execute(array('vorname' => $vorname, 'nachname' => $nachname, 'addresse' => $addresse, 'telefon' => $telefon, 'email' => $email, 'nutzer' => $_SESSION['userid']));

          $statement = $pdo->prepare("SELECT * FROM strasse WHERE StrassenName = :strassenEintrag AND Hausnummer = :hausnummer AND OrtNr = :addresse");
          $statement->execute(array('strassenEintrag' => $straße, 'hausnummer' => $hausnummer, 'addresse' => $addresse));
          $straßeDB = $statement->fetch();

          if($straßeDB == false)
          {
            $statement = $pdo->prepare("INSERT INTO strasse (StrassenName, Hausnummer, OrtNr) VALUES(:strassenEintrag , :hausnummer, :ortNr)");
          $statement->execute(array('strassenEintrag' => $straße, 'hausnummer' => $hausnummer, 'ortNr' => $addresse));

          }

          $i = count($kontaktArray);
          $kontaktArray[] = "$vorname;$nachname;$ort;$plz;$straße;$hausnummer;$email;$telefon";
        }
      }
    }

    if (isset($_GET['aendern']))
    {
      $kontaktId = $_POST['formId'];
      $vorname = $_POST["vorname"];
      $nachname = $_POST["nachname"];
      $ort = $_POST["ort"];
      $plz = $_POST["plz"];
      $straße = $_POST["straße"];
      $hausnummer = $_POST["hausnummer"];
      $email = $_POST["email"];
      $telefon = $_POST["telefonnummer"];

      if($vorname == null || $nachname == null || $ort == null || $plz == null || $straße == null || $hausnummer == null || $email == null || $telefon == null)
      {
        $errorMessage = "Bitte fülle alle Felder aus";
      }
      else
      {
        $statement = $pdo->prepare("SELECT * FROM ort WHERE Ort = :ort AND Postleitzahl = :plz");
        $result = $statement->execute(array('ort' => $ort, 'plz' => $plz));
        $ortDB = $statement->fetch();

        if($ortDB == true)
        {
          $addresse = $ortDB["ID"];
        }
        else
        {
          $statement = $pdo->prepare("INSERT INTO ort (Ort, Postleitzahl) VALUES(:ort, :plz)");
          $result = $statement->execute(array('ort' => $ort, 'plz' => $plz));

          $statement = $pdo->prepare("SELECT * FROM ort WHERE Ort = :ort");
          $result = $statement->execute(array('ort' => $ort));
          $ortDB = $statement->fetch();
          $addresse = $ortDB["ID"];
        }

        $statement = $pdo->prepare("SELECT * FROM strasse WHERE StrassenName = :strassenEintrag AND Hausnummer = :hausnummer AND OrtNr = :addresse");
        $statement->execute(array('strassenEintrag' => $straße, 'hausnummer' => $hausnummer, 'addresse' => $addresse));
        $straßeDB = $statement->fetch();

        if($straßeDB == false)
        {
          $statement = $pdo->prepare("INSERT INTO strasse (StrassenName, Hausnummer, OrtNr) VALUES(:strassenEintrag , :hausnummer, :ortNr)");
        $statement->execute(array('strassenEintrag' => $straße, 'hausnummer' => $hausnummer, 'ortNr' => $addresse));

        }

        $statement = $pdo->prepare("UPDATE kontakt SET Vorname = :vorname, Nachname = :nachname, Adresse = :addresse, Telefonnummer = :telefon, Email = :email WHERE ID = :kontaktId");
        $statement->execute(array('vorname' => $vorname, 'nachname' => $nachname, 'addresse' => $addresse, 'telefon' => $telefon, 'email' => $email, 'kontaktId' => $kontaktId));
      }
    }

    if (isset($_GET['loeschen']))
    {
      $kontaktId = $_POST['formId'];
      $statement = $pdo->prepare("UPDATE kontakt SET Nutzer = 0 WHERE ID = :kontaktId");
      $statement->execute(array('kontaktId' => $kontaktId));
    }
    ?>
  </head>
  <body style="background-color:#606060;">
    <?php if(isset($errorMessage)) { echo $errorMessage; } ?>
    <?php if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true) { ?>
    <form action="?logout=1" method="post">
      <input type="submit" value="logout"> <br><br>
    </form>
    <form action="?hinzufuegen=1" method="post">
      Vorname  <input type="text" name="vorname"> Nachname <input type="text" name="nachname"> <br><br>
      Ort <input type="text" name="ort"> PLZ <input type="text" name="plz"> <br><br>
      Straße <input type="text" name="straße"> Hausnummer <input type="text" name="hausnummer"> <br><br>
      Email <input type="email" name="email"> Telefonnummer <input type="text" name="telefonnummer"> <br><br>
      <input type="submit" value="Kontakt hinzufuegen"> <br><br><br>
    </form>
      <table>
        <tr>
          <th>&nbsp&nbspVorname</th>  <th>&nbsp&nbspNachname</th>  <th>&nbsp&nbspOrt</th>  <th>&nbsp&nbspStraße</th>  <th>&nbsp&nbspEmail</th>  <th>&nbsp&nbspTelefonnummer</th>
        </tr>
        <?php
          foreach ($kontaktArray as $key => $value) {
            ?>
            <form action="?modifizieren=1" method="post">
            <?php
              $array = explode(";", $value);
              echo "<tr><th>&nbsp&nbsp".$array[0]."&nbsp&nbsp</th><th>&nbsp&nbsp".$array[1]."&nbsp&nbsp</th><th>&nbsp&nbsp".$array[2]." ".$array[3]."&nbsp&nbsp</th><th>&nbsp&nbsp".$array[4]." ".$array[5]."&nbsp&nbsp</th><th>&nbsp&nbsp".$array[6]."&nbsp&nbsp</th><th>&nbsp&nbsp".$array[7]."</th></tr>";
            ?>
              <input type="hidden" name="formId" value="<?php echo htmlspecialchars($array[8]) ?>">
              <th><input type="submit" value="Aendern oder Loeschen"></th>
            </form>
            <?php
          }
         ?>
      </table>
    <?php } else { ?>
      <form action="?login=1" method="post">
        Benutzername <input type="text" name="username"> <br><br>
        Passwort <input type="password" name="password"> <br><br>
        <input type="submit" value="Einloggen"> <br><br><br>
      </form>
      <form action="?registrieren=1" method="post">
        Benutzername <input type="text" name="username"> <br><br>
        Email <input type="email" name="email"> <br><br>
        Passwort <input type="password" name="password"> <br><br>
        <input type="submit" value="Registrieren">
      </form>
    <?php } ?>

    <?php
      if(isset($_GET['modifizieren']))
      {
          ?>
            <form action="?aendern=1" method="post">
              *Bei einer Änderung müssen alle Felder ausgefüllt werden. Bei löschen muss kein Feld * <br><br>
              Vorname  <input type="text" name="vorname"> Nachname <input type="text" name="nachname"> <br><br>
              Ort <input type="text" name="ort"> PLZ <input type="text" name="plz"> <br><br>
              Straße <input type="text" name="straße"> Hausnummer <input type="text" name="hausnummer"> <br><br>
              Email <input type="email" name="email"> Telefonnummer <input type="text" name="telefonnummer"> <br><br>
              <input type="hidden" name="formId" value="<?php echo htmlspecialchars($_POST["formId"]) ?>">
              <input type="submit" value="Ändern">
            </form>
            <form action="?loeschen=1" method="post">
              <input type="hidden" name="formId" value="<?php echo htmlspecialchars($_POST["formId"]) ?>">
              <input type="submit" value="Löschen">
            </form>
          <?php
      }
    ?>
  </body>
</html>
