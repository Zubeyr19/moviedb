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
        <button id="left-arrow" style="visibility: hidden"><i class="fa-solid fa-chevron-left"></i></button>

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

        <button id="right-arrow"><i class="fa-solid fa-chevron-right"></i></button>
    </div>

    <h1 id="watchlist-title">Your Watchlist</h1>
    <div id="watchlist-div"></div>
</div>

<!-- Pagination Links -->
<div class="pagination">
    {{ $paginatedData->onEachSide(5)->links('pagination::bootstrap-4') }}
    <!-- Previous Page Button -->
    @if ($paginatedData->currentPage() > 1)
        <a href="{{ url('/loadPopular', ['page' => $paginatedData->currentPage() - 1, 'order' => $order]) }}" class="pagination-link">Previous</a>
    @endif

    <!-- Next Page Button -->
    @if ($paginatedData->hasMorePages())
        <a href="{{ $paginatedData->nextPageUrl() }}" class="pagination-link">Next</a>
    @endif

    <!-- Sort Buttons -->
    <a href="{{ url('/loadPopular', ['page' => 1, 'order' => 'desc']) }}" class="pagination-link">Older to Newer</a>
    <a href="{{ url('/loadPopular', ['page' => 1, 'order' => 'asc']) }}" class="pagination-link">Newer to Older</a>
</div>
</div>
    <script>
        let counter = 0;
        let data = <?php echo json_encode($data); ?>;

        let posterdiv = document.querySelectorAll('.redposterimg');
        let rightarrow = document.querySelector('#right-arrow');
        let leftarrow = document.querySelector('#left-arrow');
        rightarrow.addEventListener('click', (event) => {
            counter++;
            posterdiv.forEach((element, i) => {
                element.setAttribute('src', 'https://image.tmdb.org/t/p/w500' + data[i + counter]
                    .poster_path);
            })
            if (counter > 4) rightarrow.style.visibility = "hidden";
            else rightarrow.style.visibility = "visible";
            if (counter > 0) leftarrow.style.visibility = "visible";
            else leftarrow.style.visibility = "hidden";
        })

        leftarrow.addEventListener('click', (event) => {
            counter--;
            posterdiv.forEach((element, i) => {
                element.setAttribute('src', 'https://image.tmdb.org/t/p/w500' + data[i + counter]
                    .poster_path);
            })
            if (counter > 0) leftarrow.style.visibility = "visible";
            else leftarrow.style.visibility = "hidden"
            if (counter > 4) rightarrow.style.visibility = "hidden"
            else rightarrow.style.visibility = "visible";
        })

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
    .pagination a.pagination-link {
        color: black;
        float: left;
        padding: 8px 16px;
        text-decoration: none;
        border: 1px solid #ddd;
        margin-right: 5px; /* Adjust margin as needed */
    }

    .pagination a.pagination-link:hover:not(.active) {
        background-color: #ddd;
    }

    .pagination a.pagination-link.active {
        background-color: #4CAF50;
        color: white;
        border: 1px solid #4CAF50;
    }

    .pagination a.pagination-link:first-child {
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
    }

    .pagination a.pagination-link:last-child {
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
    }
</style>
