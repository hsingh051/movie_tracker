@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">

                    <div class="card-header">{{ __('My Movies') }} <a href="{{route('search')}}" class="btn btn-primary float-right">Add Movie</a> </div>
                    <div class="card-body" id="user_movies">
                        @if($userMovies->count() >= 1)
                            <table class="table table-bordered">
                                <tr>
                                    <th></th>
                                    <th>Title</th>
                                    <th>Year</th>
                                    <th></th>
                                </tr>
                                @foreach($userMovies as $movie)
                                    <tr>
                                        <td>
                                            @if(!empty($movie->poster))
                                                <img style="height: 100px" src="{{$movie->poster}}">
                                            @endif
                                        </td>
                                        <td>{{$movie->title}}</td>
                                        <td>{{$movie->year}}</td>
                                        <td class="action-icons">
                                            <i v-on:click="updateUserMovie('liked', {{ $movie->pivot->liked == 1 ? 0 : 1 }} ,{{$movie}})"
                                               class="far fa-heart {{ @$movie->pivot->liked ? 'active' : '' }}"></i>
                                            <i v-on:click="updateUserMovie('watched', {{ $movie->pivot->watched == 1 ? 0 : 1 }} ,{{$movie}})"
                                               class="far fa-eye {{ @$movie->pivot->watched ? 'active' : '' }}"></i>
                                            <i v-on:click="updateUserMovie('delete', 1 , {{$movie}})"
                                               class="far fa-trash-alt"></i>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('after-scripts')
    <script>
        const app = new Vue({
            el: '#user_movies',
            methods: {
                updateUserMovie: function (type, value, movie) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.post('{{ route('update.user.movie') }}', {
                                type: type,
                                value: value,
                                title: movie.Title,
                                year: movie.Year,
                                imdbID: movie.imdbID,
                                poster: movie.Poster
                            }).then(response => {
                                if (response.data.data.error === false) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: response.data.data.message,
                                        text: '',
                                        footer: '<a href="{{url('/')}}">View My Movies</a>'
                                    }).then((result) => {
                                        location.reload();
                                    })
                                }
                            });
                        }
                    })
                }
            }
        });
    </script>
@endpush
