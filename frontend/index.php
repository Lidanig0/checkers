<?php
  session_start();
  include 'header.php';

  if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    die();
  }
  $user = (array)$_SESSION["user"];
?>

<div id="profile" class="center">
  <h5>Seja bem vindo {{user[1]}}</h5>
  <h5>Você possui {{user[4]}} vitórias.</h5>
  <br />

  <div id="addFriend" class="modal">
    <div class="modal-content">
      <div class="input-field">
        <input type="text" v-model="nameOfFriend" id="nameOfFriend" required/>
        <label id="lbl" for="nameOfFriend">Nome completo ou apelido do amigo</label>
      </div>
      <div class="input-field">
        <a href="#" class="btn blue-grey darken-2" v-on:click="search($event)">Pesquisar</a>
      </div>
      <div v-if="query != null" class="">
        <table v-if="query.length > 0">
          <thead>
            <tr>
              <th>Nome</th>
              <th>Add</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="result in query">
              <td>{{result[1]}}</td>
              <td><a href="#" v-on:click="addFriend($event, result[0], result[1])">Adicionar</a></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else class="">
        <h5>Nenhum registro encontrado</h5>
      </div>
    </div>
  </div>

  <ul id="friends" class="dropdown-content">
    <li><a class="modal-trigger" href="#addFriend"><i class="material-icons center">add</i></a></li>
    <li v-for="friend in friends">
      <a :href="`profile.php?id=${friend[0]}`">{{friend[1]}}</a>
    </li>
  </ul>

  <a class="btn blue-grey darken-2" href="newGame.php">Criar um jogo</a> <br><br>
  <a class="btn blue-grey darken-2" href="lobby.php">Lista de jogos</a><br><br><hr><br>
  <a class="dropdown-button btn-large blue-grey darken-2" href="#" data-activates="friends">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Amigos&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
</div>

<?php include 'footer.php'; ?>

<script type="text/javascript">
   var Profile = new Vue({
    el: "#profile",
    data: {
      user: [],
      friends: [],
      nameOfFriend: '',
      query: '',
      id: null,
      name: null,
    },
    mounted() {
      const me = this;
      $.ajax({
        url: "../backend/getUserLogged.php",
        method: "GET",
        dataType: "json",
        success(data) {
          me.user = data[0];
          if (data[0][5] != null) {
            me.friends = JSON.parse(data[0][5]);
          }
        },
        error(args) {
          console.error(args);
          toastr.error(args.responseText);
        }
      });
    },
    methods: {
      search(event) {
        event.preventDefault();
        const me = this;
        $.ajax({
          url: "../backend/searchUsers.php",
          method: "GET",
          dataType: "json",
          data: {
            name: this.nameOfFriend,
          },
          success(data) {
            console.log(data);
            if (data != 'Nenhum registro encontrado.') {
              me.query = data;
            } else {
              me.query = null;
            }
          },
          error(args) {
            console.error(args);
          }
        });
      },
      addFriend(event, id, name) {
        event.preventDefault();
        const me = this;
        $.ajax({
          url: "../backend/addFriend.php",
          method: "POST",
          dataType: "json",
          data: {
            friendId: id,
            friendName: name
          },
          success(data) {
            toastr.success(data);
            setTimeout(function() { location.assign("index.php") }, 500);
          },
          error(args) {
            toastr.error(args.responseText);
            console.error(args);
          }
        });
      }
    },
  });
</script>
