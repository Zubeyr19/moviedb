<!DOCTYPE html>
<body lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/d826f0fb4b.js" crossorigin="anonymous"></script>
</head>

<body>
@include('search')

<div id="innerbody">
    <div id="title-div">
        <h1>Popular movies</h1>
        <?php
        if (session()->has('user')) {
            $temp = session('user');
            echo $temp->name;
        }
        ?>
    </div>

    <div id="searchPoster"></div>

    <div id="poster-div" style="display: flex; flex-direction: column; align-items: center;">

        <div style="display: flex; flex-wrap: wrap; justify-content: center;">
            <?php
            $data = session('data');
            $poster = session('poster');
            if (isset($data)) {
                // Display up to 10 movies in a 5x2 grid
                for ($i = 0; $i < min(10, count($data)); $i++) {
                    echo '<div class="redposter" style="margin: 0.5rem;"><img class="redposterimg poster" src="https://image.tmdb.org/t/p/w500' . $data[$i]->poster_path . '"></div>';
                }
            }
            ?>
        </div>
        <div style="display: flex; justify-content: space-between; width: 100%;">
            <!-- Add the new buttons here -->
            <button id="previous-page" class="pagination-button">Previous</button>
            <button id="Older-page" class="pagination-button">Older</button>
            <button id="Newer-page" class="pagination-button">Newer</button>
            <button id="next-page" class="pagination-button">Next</button>
        </div>

    </div>

    <h1 id="watchlist-title">Your Watchlist</h1>
    <div id="watchlist-div"></div>
</div>

</div>


<script>
    let counter = 0;
    let data = <?php echo json_encode($data); ?>;
    let posterdiv = document.querySelectorAll('.redposterimg');
    let nextButton = document.querySelector('#next-page');
    let previousButton = document.querySelector('#previous-page');

    // Function to update posters based on the current counter
    function updatePosters() {
        posterdiv.forEach((element, i) => {
            // Update only if there is data available for the current index
            if (data[i + counter]) {
                element.setAttribute('src', 'https://image.tmdb.org/t/p/w500' + data[i + counter].poster_path);
            } else {
                // If no data available, you can set a placeholder image or clear the src attribute
                element.setAttribute('src', 'path/to/placeholder-image.jpg');
            }
        });

        // Update visibility of buttons
        if (counter > 1) nextButton.style.visibility = "hidden";
        else nextButton.style.visibility = "visible";
        if (counter > 0) previousButton.style.visibility = "visible";
        else previousButton.style.visibility = "hidden";
    }



    // Event listener for the "Previous" button
    previousButton.addEventListener("click", async (event) => {
        event.preventDefault();
        if (currentPage > 1) {
            // Decrement the currentPage to go to the previous page
            currentPage = currentPage - 1;
            await fetchAndDisplayMovies(currentPage, order);
        }
    });

    // Event listener for the "Next" button
    nextButton.addEventListener("click", async (event) => {
        event.preventDefault();
        await loadMoreMovies();
    });

        async function getPosterPath(movie_id) {
            return fetch(`/api/getPosterPath/${movie_id}`, {
                method: "GET"
            }).then(async (result) => {
                return result.json();
            })
        }

        async function getWatchlist(id) {
            return fetch(`/api/getUserWatchlist/${id}`, {
                method: "GET"
            }).then(async (result) => {
                return result.json();
            })
        }


        getWatchlist(
            @if (session()->has('user'))
                {{ session('user')->id }}
            @else
                -1
            @endif
        ).then(async (response) => {
            let div = document.querySelector('#watchlist-div');
            if (response.length == 0) {
                let text = document.createElement('p');
                @if (session()->has('user'))
                    text.innerHTML = "Your watchlist is empty. Go to a movies page to add it to your watchlist";
                @else
                    text.innerHTML = "Login to access your watchlist"
                @endif

                text.setAttribute('id', 'emptywatchlist')
                div.appendChild(text);
            }
            for (let i = 0; i < 6; i++) {
                let movie = document.createElement('img');
                let a = document.createElement('a')
                a.setAttribute('class', 'redposter')
                if (i < response.length) {
                    let posterpath = await getPosterPath(response[i].movie_id);
                    movie.setAttribute('src', `https://image.tmdb.org/t/p/w500${posterpath.poster_path}`)
                    a.setAttribute('href', `/movie/${response[i].movie_id}`)
                } else {
                    movie.style.visibility = 'hidden';
                }

                movie.setAttribute('class', 'poster')
                a.appendChild(movie)
                div.appendChild(a)
            }
        })

        document.querySelector("#form").addEventListener("submit", (event) => {
            event.preventDefault();
            const input = document.querySelector("#input").value;
            $.ajax({
                url: 'api/test/' + input,
                type: "GET",
                success: (result) => {
                    document.querySelector('#searchPoster').innerHTML +=
                        `<img class="poster" src="https://image.tmdb.org/t/p/w500${result}">`
                }
            })
        });

        var posters = document.querySelectorAll(".redposter");

        posters.forEach((element, i) => {
            element.addEventListener('click', (event) => {
                window.location.href = '/movie/' + data[i + counter].id;
            })
        })
    </script>
</body>


</body>
<style>
    #image {
        display: flex;
    }

    body {
        background-color: #000;
        color: white;
        margin: 0;
        padding: 0;
        min-height: 100vh;
        height: 100%;
    }

    h1 {
        font-size: 26px;
    }

    #innerbody {
        background-color: #111;
        margin: auto;
        min-height: inherit;
        height: inherit;
        padding: 0 2rem 10rem 2rem;
        max-width: 75%;
        display: flex;
        flex-direction: column;
    }

    .poster {
        visibility: visible;
        width: 150px;
        height: 225px;
        padding: 1vh;
        margin: 0 1rem 0 1rem;
    }

    .redposter {
        background-color: #222;
    }

    #poster-div,
    #watchlist-div {
        display: flex;
        place-content: center;
    }

    #title-div,
    #watchlist-title {
        margin-top: 2rem;
        margin-left: 9rem;
    }

    #right-arrow {
        display: flex;
        place-self: center;
    }

    #left-arrow {
        display: flex;
        place-self: center;
    }

    #left-arrow,
    #right-arrow {
        border: none;
        background: none;
    }

    .fa-solid {
        color: white;
    }

    #right-arrow:hover,
    #left-arrow:hover {
        opacity: 0.8;
    }

    #emptywatchlist {
        position: absolute;
        margin-top: 7rem;
    }
    /* Pagination Styles */
    .pagination-button {
        background-color: #333;
        color: white;
        padding: 8px 16px;
        border: none;
        cursor: pointer;
        margin: 0 10px;
    }

    .pagination-button:hover {
        background-color: #555;
    }
</style>
