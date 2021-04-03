<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\UserMovie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $userMovies = Auth::user()->movies;

        return view('home')->with(compact('userMovies'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function search()
    {
        return view('movies.search');
    }


    public function updateUserMovie(Request $request)
    {
        $userID = Auth::user()->id;

        // Find Movie
        $movie = Movie::where('imdbID', $request->imdbID)->first();


        // Delete User Movie
        if ($request->type == 'delete') {
            UserMovie::where('movie_id', $movie->id)->where('user_id', $userID)
                ->delete();
            return response()->json([
                'data' => [
                    'error'   => false,
                    'message' => 'Deleted Successfully!'
                ]
            ], 200);
        }


        if (empty($movie)) {
            $movie = new Movie();
            $movie->imdbID = $request->imdbID;
            $movie->title = $request->title;
            $movie->year = $request->year;
            $movie->type = 'movie';
            $movie->poster = $request->poster;
            $movie->save();
        }


        $type = $request->type;
        $data = [
            'movie_id' => $movie->id,
            'user_id'  => $userID,
            $type      => $request->value
        ];
        // Find User Movie
        $userMovie = UserMovie::where('movie_id', $movie->id)
            ->where('user_id', $userID)->first();

        if (empty($userMovie)) {
            $userMovie = new UserMovie();
            $userMovie->create($data);
        } else {
            $userMovie->update($data);
        }

        return response()->json([
            'data' => [
                'error'   => false,
                'message' => 'Updated Successfully!'
            ]
        ], 200);
    }
}
