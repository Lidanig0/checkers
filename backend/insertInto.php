<?php
  session_start();
  include 'connection.php';

  $user = (array) $_SESSION['user'];
  $userId = $user["id"];
  $victories = (int) $user["victories"] + 1;

  $board = $_POST["board"];
  $turn = $_POST["turn"];
  $jg1P = $_POST["player1_points"];
  $jg2P = $_POST["player2_points"];
  $winner_id = $_POST["winner_id"];
  $winner = $_POST["winner"];
  $gameId = $_SESSION["gameId"];

  if (array_key_exists("winner_id", $_POST)) {
    if ($winner_id != null) {
      $query = "UPDATE `jogos`
        SET `board` = '$board', `active` = 0,
        `turn` = '$turn', `status` = 2, `winner_id` = '$winner_id', `winner` = '$winner',
        `player1_points` = '$jg1P', `player2_points` = '$jg2P'
        WHERE `id` = '$gameId'";
      $winner = $mysqli->query("UPDATE `users`
        SET `victories` = $victories WHERE `id` = '$userId'");
      if (!$winner) {
        die("Error: (" . $mysqli->errno . ") " . $mysqli->error);
      }
    } else {
      $query = "UPDATE `jogos`
        SET `board` = '$board', `active` = 1,
        `turn` = '$turn', `status` = 1,
        `player1_points` = '$jg1P', `player2_points` = '$jg2P'
        WHERE `id` = '$gameId'";
    }
  }

  if (!$mysqli->query($query)) {
    die("Error: (" . $mysqli->errno . ") " . $mysqli->error);
  }

  $select = $mysqli->query("SELECT * FROM `jogos` WHERE `id` = '$gameId'");

  if (!$select) {
    die("Error: (" . $mysqli->errno . ") " . $mysqli->error);
  } else {
    $result = $select->fetch_all();
    if (count($result) > 0) {
      if ($userId != $res[0][6]) {
        $update = $mysqli->query("UPDATE `jogos` SET `player2_id` = '$userId' WHERE `id` = '$gameId'");
        if (!$update) {
          die("Error: (" . $mysqli->errno . ") " . $mysqli->error);
        }
      }
      $select2 = $mysqli->query("SELECT * FROM `jogos` WHERE `id` = '$gameId'");
      if (!$select2) {
        die("Error: (" . $mysqli->errno . ") " . $mysqli->error);
      }
      $result2 = $select2->fetch_all();
      echo json_encode($result2);
    }
  }
?>
