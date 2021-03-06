<?php
  include 'inc/package.php';
  include 'inc/connection.php';
  define('PAGE_TITLE', 'Profiel');
  if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }
  $additionalCSS = ['profile', 'bootstrap-switch'];
  $additionalJS = ['bootstrap-switch.js'];
  $content = '';

  if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true) {
    if(isset($_GET['user'])){
      $getItems = $connection->prepare('SELECT `image`, `public`, `personal_firstName`, `personal_gender`, `personal_birthDay`, `education_education`, `work_function`, `language_language`, `language_skill` FROM `concept` WHERE `user` = :user');
      $getItems->bindValue(':user', $_GET['user'], PDO::PARAM_INT);
      $getItems->execute();
      $arr = $getItems->fetch(PDO::FETCH_ASSOC);
      if($arr['public'] === "true") {
        $firstname = $arr['personal_firstName'];

        $gender = $arr['personal_gender'];

        $profileImage = $arr['image'];

        $age = $arr['personal_birthDay'];
        if(preg_match('/^a:\d+:{.*?}$/', $arr['education_education'])) {
          $education = unserialize($arr['education_education']);
        } else {
          $education = $arr['education_education'];
        }

        if(preg_match('/^a:\d+:{.*?}$/', $arr['work_function'])) {
          $function = unserialize($arr['work_function']);
        } else {
          $work = $arr['work_function'];
        }

        if(preg_match('/^a:\d+:{.*?}$/', $arr['language_language'])) {
          $language = unserialize($arr['language_language']);
        } else {
          $language = $arr['language_language'];
        }

        if(preg_match('/^a:\d+:{.*?}$/', $arr['language_skill'])) {
          $skill = unserialize($arr['language_skill']);
        } else {
          $skill = $arr['language_skill'];
        }

        $message = false;
      } else {
        $message = true;
      }

      $view = 'views/profile.php';
    } else {
      include 'inc/classes/profileImage.php';
      $getItems = $connection->prepare('SELECT * FROM `concept` WHERE `user` = :user');
      $getItems->bindValue(':user', $_SESSION['id'], PDO::PARAM_STR);

      $getItems->execute();
      $arr = $getItems->fetch(PDO::FETCH_ASSOC);
      $public = $arr['public'];
      $profileImage = $arr['image'];
      $arr = array_slice($arr, 4);

      function saveSetting($type, $value) {
        include 'inc/connection.php';

        $setSetting = $connection->prepare('UPDATE `concept` SET `public` = :value WHERE user = :id');
        // $setSetting->bindValue(':type', $type, PDO::PARAM_STR);
        $setSetting->bindValue(':value', $value, PDO::PARAM_STR);
        $id = $_SESSION['id'];
        $setSetting->bindValue(':id', $id, PDO::PARAM_STR);

        try {
          $setSetting->execute();
        } catch (PDOexception $e) {

        }
        header("Location: profile.php");

      }

      if(isset($_POST['submit'])) {
        if(isset($_POST['public'])){
          saveSetting('public', 'true');
        } else {
          saveSetting('public', 'false');
        }
      }


      $view = 'views/profileSelf.php';
    }


  } else {
    header('Location: login.php');
  }

  include $template;
?>
