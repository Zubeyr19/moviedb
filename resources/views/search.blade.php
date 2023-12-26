<header class="search">
  <a class="menu">menu</a>
  <div>
    <form id="form" type="submit" action="/test" method="get">
      <input id="input" type="search" class="searchinput" name="query" autocomplete="off" />
      <button class="searchbutton" type="submit"></button>
    </form>
    <div id="dropdowndiv" class="dropdown"></div>
  </div>
  <?php
  if (!session()->has('user')) {
    echo '<button id="openModal" class="menu" type="button">Log in</button>';
  } else {
    echo '<button id="logout" class="menu" type="button">Log out</button>';
  }
  ?>
</header>
<div>
  <div id="login-modal">
    <div id="myModal" class="modal">
      <div class="modal-content">
        <div>
          <span class="close">&times;</span>
          <h1 id="modal-title">Log in</h1>
        </div>
        <?php
        if ($errors->any()) {
          echo '<div style="position: absolute">
        <ul>';
          foreach ($errors->all() as $error) {
            echo $error;
            echo '<br></br>';
          }
          echo '</ul></div>';
        ?>
          <script>
            document.querySelector('.modal').style.visibility = "visible";
          </script>
        <?php
        }
        ?>

        <div class="signup-content">
          <form id="signup-form" action="{{url('signup')}}" method="POST">
            <label for="signupemail">Email</label>
            <input type="email" name="signupemail" id="signupemail" required />
            <label for="signupusername">Username</label>
            <input type="text" name="signupusername" id="signupusername" required />
            <label for="signuppassword">Password</label>
            <input type="password" name="signuppassword" id="signuppassword" required />
            <label for="signuppassword_confirmation">Confirm password</label>
            <input type="password" name="signuppassword_confirmation" id="signuppassword_confirmation" />
            <button id="signup-button" type="submit">Sign up</button>
          </form>
        </div>
        <div class="login-content">
          <form id="login-form" action="{{url('authuser')}}" method="POST">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required />
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required />
            <button id="login-button" type="submit">Log in</button>
          </form>
          <div id="sign-up">
            <a>Sign up</a>
          </div>
          <div id="forgot-password">
            <a href="">Forgot password</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php
  if ($errors->has('signupemail') | $errors->has('signupusername') | $errors->has('signuppassword')) {
  ?>
    <script>
      document.querySelector('.login-content').style.display = "none";
      document.querySelector('.signup-content').style.display = "block";
    </script>
  <?php
  }
  ?>
  <script>
    <?php if (!session()->has('user')) { ?>
      document.querySelector('#openModal').addEventListener('click', (event) => {
        document.querySelector('.modal').style.visibility = "visible";
      })
    <?php } else {
    ?>
      document.querySelector('#logout').addEventListener('click', (event) => {
        $.ajax({
          url: "/logout",
          method: 'GET',
          success: (result) => {
            console.log(result);
          }
        }).then(() => {
          window.location.reload();
        })
      })
    <?php } ?>

    document.querySelector("#sign-up").addEventListener('click', (event) => {
      document.querySelector('.login-content').style.display = "none";
      document.querySelector('.signup-content').style.display = "block";
    })
    document.querySelector('.close').addEventListener('click', (event) => {
      document.querySelector('.login-content').style.display = "block";
      document.querySelector('.signup-content').style.display = "none";
      document.querySelector('.modal').style.visibility = "hidden";
    })

    async function signUp(email, name, password, password_confirmation) {
      let url = "/signup"
      await fetch(url, {
        method: "POST",
        body: JSON.stringify({
          name,
          email,
          password,
          password_confirmation
        }),
        headers: {
          "Content-Type": "application/json"
        }
      })
    }

    function login(email, password) {
      let url = "/api/login"
      fetch(url, {
        method: "POST",
        body: JSON.stringify({
          email,
          password
        }),
        headers: {
          "Content-type": "application/json"
        }
      }).then((res) => {
        console.log(res)
        return res.status == 302 ? true : false;
      }).catch((err) => {
        console.error(err)
      })
    }

    const APIKEY = "7356f6c781f842026367b8baa225abdb";

    function setupDropdown() {
      let temp = document.querySelector("#dropdowndiv");
      let searchinput = document.querySelector(".searchinput");
      const whatever = (event) => {
        if (searchinput.value != "") {
          temp.style.display = "grid";
          temp.style.gridTemplateRows = 3;
        } else {
          temp.style.display = "none";
        }
      };
      searchinput.addEventListener("input", (event) => {
        temp.innerHTML = "";
        console.log(searchinput.value);
        $.ajax({
          url: `https://api.themoviedb.org/3/search/movie?query=${searchinput.value}&api_key=${APIKEY}`,
          type: "GET",
          success: (result) => {
            if (result) {
              result.results.slice(0, 5).forEach((element) => {
                addMovieToDropdown(element.id);
              });
            }
          },
          error: (err) => {
            console.log(err);
          },
        });
        if (searchinput.value != "") {
          temp.style.display = "grid";
          temp.style.gridTemplateRows = 3;
        } else {
          temp.style.display = "none";
        }
      });
      window.addEventListener("load", whatever);
    }

    function addMovieToDropdown(id) {
      let accessToken =
        "Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI3MzU2ZjZjNzgxZjg0MjAyNjM2N2I4YmFhMjI1YWJkYiIsInN1YiI6IjY1MDFjOTdkNTU0NWNhMDBhYjVkYmRkOSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.zvglGM1QgLDK33Dt6PpMK9jeAOrLNnxClZ6mkLeMgBE";
      $.ajax({
        url: `https://api.themoviedb.org/3/movie/${id}`,
        type: "GET",
        beforeSend: (req) => {
          req.setRequestHeader("Authorization", accessToken);
        },
        success: (result) => {
          let a = document.createElement("a");
          let title = document.createElement("h3");
          let year = document.createElement("h3");
          let actors = document.createElement("h3");
          let img = document.createElement("img");
          let div = document.createElement("div");
          let div2 = document.createElement("div");
          if (result.poster_path == null) {
            img.setAttribute(
              "src",
              "https://img.freepik.com/free-photo/abstract-luxury-plain-blur-grey-black-gradient-used-as-background-studio-wall-display-your-products_1258-63747.jpg?w=2000"
            );
          } else {
            img.setAttribute(
              "src",
              `https://image.tmdb.org/t/p/w500${result.poster_path}`
            );
          }
          img.setAttribute("class", "dropdownimage");
          div.appendChild(img);
          a.addEventListener("click", (event) => {
            var data = result;
            sessionStorage.setItem("posterpath", data.poster_path);
            sessionStorage.setItem("title", data.title);
            sessionStorage.setItem("overview", data.overview);
            sessionStorage.setItem("id", data.id);
            var genredata = "";
            data.genres.forEach((element) => {
              genredata += element.id + ",";
            });
            sessionStorage.setItem("genres", genredata);
            window.location.href = `/movie/${data.id}`;
          });
          actors.setAttribute("id", "undertitle");
          year.setAttribute("id", "undertitle");
          year.textContent = result.release_date.substr(0, 4);
          title.textContent = result.title;
          actors.textContent = "Leonardo DiCaprio";
          div2.setAttribute("id", "div2");
          div2.appendChild(title);
          div2.appendChild(year);
          div2.appendChild(actors);
          div.appendChild(div2);
          div.setAttribute("class", "dropdownitem");
          div.setAttribute("id", "dropdowndiv2");
          a.appendChild(div);
          document.querySelector("#dropdowndiv").appendChild(a);
        },
      });
    }
    setupDropdown();
  </script>
  <style scoped>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');

    * {
      font-family: Montserrat, sans-serif;
    }

    #dropdowndiv {
      width: 91rem;
      height: fit-content;
      display: flex;
      flex-direction: column;
      position: absolute;
      background-color: #999;
    }

    header {
      display: flex;
      position: relative;
      background-color: #333;
      max-width: 100%;
      padding: 2rem 0 2rem 0;
      z-index: 1;
    }

    .menu {
      text-transform: capitalize;
      text-decoration: underline;
      font-size: large;
      padding-top: 0.5vh;
      margin: 0 4rem 0 4rem;
    }

    #form {
      color: #555;
      display: flex;
      border: 1px solid currentColor;
    }


    input[type="search"] {
      border: none;
      background: white;
      margin: 0;
      padding: 7px 8px;
      font-size: 14px;
      color: inherit;
      width: 91rem;
      border-radius: 2px;
    }

    .searchbutton {
      text-indent: -999px;
      overflow: hidden;
      width: 40px;
      padding: 0;
      margin: 0;
      border: 1px solid transparent;
      border-radius: inherit;
      background: transparent url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' class='bi bi-search' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'%3E%3C/path%3E%3C/svg%3E") no-repeat center;
      cursor: pointer;
      opacity: 1;
      background-color: white;
    }

    #login-button,
    #signup-button {
      height: 2rem;
      background-color: #999;
      border: none;
      border-radius: 2px;
      cursor: pointer;
    }

    button[type="submit"]:hover {
      opacity: 0.7;
    }

    form.nosubmit {
      border: none;
      padding: 0;
    }

    .dropdownitem {
      height: fit-content;
      border-bottom: 1px solid grey;
    }

    .dropdownimage {
      width: 4.5rem;
      height: 7rem;
      padding: 0.5rem;
    }

    #dropdowndiv2 {
      display: flex;
    }

    .dropdowntitle {
      display: flex;
      justify-content: left;
    }

    #undertitle {
      font-weight: normal;
    }

    .modal {
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgb(0, 0, 0);
      background-color: rgba(0, 0, 0, 0.4);
      visibility: hidden;
    }

    .modal-content {
      position: fixed;
      left: 0;
      bottom: 0;
      top: 0;
      right: 0;
      background-color: #fefefe;
      margin: 5rem 30rem 5rem 30rem;
      width: auto;
      height: auto;
      padding: 20px;
      border-radius: 20px;

    }

    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }

    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }

    #openModal,
    #logout {
      border: none;
      background: none;
      color: white;
    }

    #login-modal {
      color: black;
    }

    #login-form,
    #signup-form {
      display: flex;
      flex-direction: column;
      width: 25%;
      margin: auto;
      margin-top: 10rem;
    }

    #sign-up,
    #forgot-password {
      display: flex;
      justify-content: center;
      margin-top: 1rem;
      color: #555;
    }

    #login-form>input,
    #signup-form>input {
      height: 2rem;
      margin-bottom: 1rem;
    }

    .signup-content {
      display: none;
    }

    #modal-title {
      display: flex;
      justify-content: center;
    }
  </style>
