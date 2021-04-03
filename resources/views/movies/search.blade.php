@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">

                    <div class="card-header">{{ __('Search Movie') }} <a href="{{url('/')}}" class="btn btn-primary float-right">My Movies</a></div>

                    <div class="card-body">
                        <!-- filter_list -->
                        <div id="search_movies">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-text">Title</span>
                                        <input type="text" aria-label="First name" name="" id="searchTitle"
                                               class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <span class="input-group-text">Year</span>
                                        <input type="number" maxlength="4" minlength="4" aria-label="First name" name=""
                                               id="searchYear" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-2 text-right">
                                    <button type="submit" class="btn btn-primary" v-on:click="getMovies">Search</button>
                                </div>
                            </div>
                            <div class="filterMovies">
                                <div v-if="loading" class="spinner-border text-primary mt-3" role="status"><span
                                        class="sr-only">Loading...</span></div>
                                <div v-else class="moviesList">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Image</th>
                                            <th>Title</th>
                                            <th>Year</th>
                                            <th>Actions</th>
                                        </tr>

                                        <tr v-if="Object.keys(movies).length === 0">
                                            <td colspan="4">No movie found!</td>
                                        </tr>

                                        <tr v-for='item in movies'>
                                            <td><img style="height: 100px" v-if="item.Poster" :src="item.Poster"></td>
                                            <td>@{{item.Title}}</td>
                                            <td>@{{item.Year}}</td>
                                            <td class="action-icons">
                                                <i v-on:click="updateUserMovie('liked', 1, item)" :title="likedTitle"
                                                   class="far fa-heart"></i>
                                                <i v-on:click="updateUserMovie('watched', 1, item)"
                                                   :title="watchedTitle" class="far fa-eye"></i>
                                            </td>
                                        </tr>

                                        <tr v-if="nextPage >= 1">
                                            <td colspan="4">
                                                <button v-on:click="getMoreData(nextPage)" class="btn btn-success">Load
                                                    More
                                                </button>
                                            </td>
                                        </tr>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('after-scripts')
    <script>
        const app = new Vue({
            el: '#search_movies',
            data: {
                loading: false,
                title: null,
                year: null,
                currentPage: 1,
                nextPage: 0,
                limit: 10,
                movies: {},
                apiUrl: 'https://www.omdbapi.com/?apikey=aa001a5d&type=movie',
                likedTitle: 'Do you like this movie?',
                watchedTitle: 'Have you watched this movie?',
            },
            methods: {
                getMovies: function (el) {
                    $('#searchTitle').removeClass('border-danger');
                    this.title = $('#searchTitle').val();
                    this.year = $('#searchYear').val();

                    if (this.title == "" || this.title == null) {
                        $('#searchTitle').addClass('border border-danger');
                        $('#searchTitle').focus();
                        return false;
                    }
                    this.getInitialData();

                },
                getInitialData: function (el) {
                    this.loading = true;
                    var that = this;
                    $.ajax({
                        url: that.apiUrl,
                        type: "get",
                        data: {s: this.title, y: this.year, page: that.currentPage},
                        success: function (response) {

                            that.loading = false;

                            if (response.Response == "True") {
                                that.movies = response.Search;
                                if (response.totalResults > (that.limit * that.currentPage)) {
                                    that.nextPage = that.currentPage + 1;
                                }
                            }

                            console.log(response);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
                    });
                },

                getMoreData: function (currentPage) {
                    var that = this;
                    that.currentPage = currentPage;
                    $.ajax({
                        url: that.apiUrl,
                        type: "get",
                        data: {s: this.title, y: this.year, page: that.currentPage},
                        success: function (response) {
                            if (response.Response == "True") {
                                that.movies = that.movies.concat(response.Search);
                                if (response.totalResults > (that.limit * that.currentPage)) {
                                    that.nextPage = that.currentPage + 1;
                                } else {
                                    that.nextPage = 0;
                                }
                            }
                            console.log(response);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
                    });
                },
                updateUserMovie: function (type, value, movie) {
                    Swal.fire({
                        title: (type == 'liked') ? this.likedTitle : this.watchedTitle,
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
                                    })
                                }
                                console.log(response.data.data.error);
                            });
                        }
                    })
                }

            }
        });
    </script>
@endpush
