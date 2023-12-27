<?php

namespace App\Http\Controllers;


use App\Models\Movie;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Input;


class movieController extends Controller
{
    public function loadPopular()
    {
        $accessToken =
            "Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI3MzU2ZjZjNzgxZjg0MjAyNjM2N2I4YmFhMjI1YWJkYiIsInN1YiI6IjY1MDFjOTdkNTU0NWNhMDBhYjVkYmRkOSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.zvglGM1QgLDK33Dt6PpMK9jeAOrLNnxClZ6mkLeMgBE";
        $url = "https://api.themoviedb.org/3/discover/movie?include_adult=false&include_video=false&language=en-US&page=1&sort_by=popularity.desc";
        $res = Http::withHeaders(["Authorization" => $accessToken])->get($url);
        $decode = json_decode($res->body(), false);
        session(['data' => $decode->results]);
        return view('test');
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
