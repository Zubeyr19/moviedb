<!DOCTYPE html>
<html lang="en">

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
                $counter = 0;
                foreach ($data as $movie) {
                    if ($counter >= 10) {
                        break; // Limit to 10 movies
                    }

                    // Adjust the following line to your styling needs
                    echo '<div class="redposter" style="margin: 0.5rem;"><img class="redposterimg poster" src="https://image.tmdb.org/t/p/w500' . $movie->poster_path . '"></div>';

                    // Increment the counter after displaying a movie
                    $counter++;
                }
            }
            ?>
        </div>
        <div style="display: flex; justify-content: space-between; width: 100%;">
            <!-- Add the new buttons here -->
            <button id="previous-page" class="pagination-button">Previous</button>
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


    // Event listener for the "Previous" button
    previousButton.addEventListener("click", async (event) => {
        counter--;
        if (counter < 0) {
            // If counter is negative, set it to the last page
            counter = Math.floor(data.length / itemsPerPage) - 1;
        }
        await loadMovies();
    });

    // Event listener for the "Next" button
    nextButton.addEventListener("click", async (event) => {
        counter++;
        await loadMovies();
    });

    // Function to load movies based on the current counter
    async function loadMovies() {
        // Specify the number of items to display per page
        const itemsPerPage = 10;

        // Calculate the starting index for the current page
        const startIndex = counter * itemsPerPage;

        // Check if the starting index is within the bounds of the data
        if (startIndex >= 0 && startIndex < data.length) {
            posterdiv.forEach(async (element, i) => {
                const dataIndex = startIndex + i;

                if (data[dataIndex]) {
                    let posterpath = await getPosterPath(data[dataIndex].id);
                    element.setAttribute('src', `https://image.tmdb.org/t/p/w500${posterpath.poster_path}`);
                } else {
                    element.setAttribute('src', 'path/to/placeholder-image.jpg');
                }
            });

            // Update visibility of buttons
            previousButton.style.visibility = "visible";
            nextButton.style.visibility = "visible";
        } else {
            // Reset the counter if it goes beyond the available data
            counter = 0;
            loadMovies();
            return;
        }

        // Update visibility of buttons
        previousButton.style.visibility = "visible"; // Always set the "Previous" button to visible

        if (counter >= Math.floor(data.length / itemsPerPage)) {
            nextButton.style.visibility = "hidden";
        } else {
            nextButton.style.visibility = "visible";
        }
    }

    // Initial load
    loadMovies();

    async function getPosterPath(movie_id) {
        try {
            const response = await fetch(`/api/getPosterPath/${movie_id}`, {
                method: "GET"
            });

            if (!response.ok) {
                throw new Error(`Error: ${response.status} - ${response.statusText}`);
            }

            return response.json();
        } catch (error) {
            console.error('Error fetching poster path:', error);
            // Handle the error, such as returning a default value or rethrowing the error
            throw error;
        }
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


</html>
<style>
    body {
        background-color: #000;
        color: white;
        margin: 0;
        padding: 0;
        min-height: 100vh;
        height: 100%;
        font-family: 'Arial', sans-serif;
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
        margin-top: 1rem;
    }

    #title-div,
    #watchlist-title {
        margin-top: 2rem;
        margin-left: 9rem;
        font-size: 1.5rem;
    }

    #emptywatchlist {
        position: absolute;
        margin-top: 7rem;
        font-size: 1rem;
    }

    /* Pagination Styles */
    .pagination-button {
        background-color: #333;
        color: white;
        padding: 8px 16px;
        border: none;
        cursor: pointer;
        margin: 0 10px;
        font-size: 14px;
    }

    .pagination-button:hover {
        background-color: #555;
    }
</style>
