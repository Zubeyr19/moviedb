<?php

namespace App\Http\Controllers;


use App\Models\Movie;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Input;


class movieController extends Controller
{
    public function loadPopular(Request $request)
    {
        $accessToken = "Bearer eyJhbGciOiJIUzI1NiJ9...."; // Your access token
        $page = $request->query('page', 1);

        // Fetch movies with pagination and sorting options
        $url = "https://api.themoviedb.org/3/discover/movie?include_adult=false&include_video=false&language=en-US&page=$page&sort_by=popularity.desc";

        $res = Http::withHeaders(["Authorization" => $accessToken])->get($url);
        $apiResponse = json_decode($res->body(), false);

        // Check if API response includes pagination attributes
        $data = $apiResponse->results ?? [];
        $currentPage = $apiResponse->page ?? 1;
        $totalResults = $apiResponse->total_results ?? 0;
        $totalPages = min(3, $apiResponse->total_pages ?? 1); // Limit to a maximum of 3 pages

        // Adjust perPage based on the desired total number of items
        $perPage = max(1, min(10, $totalResults)); // Ensure $perPage is at least 1

        // Fetch the poster path for each movie individually
        $posterPaths = [];
        foreach ($data as $movie) {
            $posterPaths[] = $this->fetchPosterPath($movie->id);
        }

        // Combine movie data with poster paths
        $dataWithPosters = array_map(function ($movie, $posterPath) {
            $movie->poster_path = $posterPath;
            return $movie;
        }, $data, $posterPaths);

        // Create a paginator instance using the modified data
        $paginatedData = new LengthAwarePaginator($dataWithPosters, $totalResults, $perPage, $currentPage);

        $paginatedData->setPath('/loadPopular');


        return view('test');
    }

    private function fetchPosterPath($movieId)
    {
        $apiKey = '53d52df88365271faa5c553841e4f780';
        $url = "https://api.themoviedb.org/3/movie/{$movieId}?api_key={$apiKey}";

        $response = Http::get($url);
        $movieData = json_decode($response->body());

        return $movieData->poster_path ?? null;
    }
    public function setupSearch($query)
    {
        $APIKEY = "7356f6c781f842026367b8baa225abdb";
        $url = 'https://api.themoviedb.org/3/search/movie?query=' . $query . '&api_key=' . $APIKEY;
        $res = Http::get($url);
        $decode2 = json_decode($res->body(), false);
        if (isset($decode2->results[0]->poster_path)) {
            session(['poster' => $decode2->results[0]->poster_path]);
        }
        return $decode2->results[0]->poster_path;
        //return view('test')->with('poster', $decode2->results[0]->poster_path);
    }

    public function getDetails($id)
    {
        $accessToken =
            "Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI3MzU2ZjZjNzgxZjg0MjAyNjM2N2I4YmFhMjI1YWJkYiIsInN1YiI6IjY1MDFjOTdkNTU0NWNhMDBhYjVkYmRkOSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.zvglGM1QgLDK33Dt6PpMK9jeAOrLNnxClZ6mkLeMgBE";
        $url = 'https://api.themoviedb.org/3/movie/' . $id . '?language=en-US';
        $res = Http::withHeaders(["Authorization" => $accessToken])->get($url);
        $data = json_decode($res->body(), false);
        $url2 = 'https://api.themoviedb.org/3/movie/' . $id . '/videos';
        $res2 = Http::withHeaders(["Authorization" => $accessToken])->get($url2);
        $decode = json_decode($res2->body(), false);
        return view('movie', ['data' => $data, 'key' => $decode]);
    }

    public function getPosterpath()
    {
        $id = request()->id;
        $accessToken =
            "Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI3MzU2ZjZjNzgxZjg0MjAyNjM2N2I4YmFhMjI1YWJkYiIsInN1YiI6IjY1MDFjOTdkNTU0NWNhMDBhYjVkYmRkOSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.zvglGM1QgLDK33Dt6PpMK9jeAOrLNnxClZ6mkLeMgBE";
        $url = 'https://api.themoviedb.org/3/movie/' . $id . '?language=en-US';
        $res = Http::withHeaders(["Authorization" => $accessToken])->get($url);
        $data = json_decode($res->body(), false);

        return $data;
    }
}
