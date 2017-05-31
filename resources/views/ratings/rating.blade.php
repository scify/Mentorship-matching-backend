@extends('layouts.auth')

@section('content')
    <div class="bg-login printable">
        <div class="login-screen">
            <div class="panel-login blur-content">
                <div class="panel-heading"><img src="{{asset('/assets/img/jobpairs_logo.png')}}" height="120" alt="">
                </div><!--.panel-heading-->

                <div id="pane-login" class="panel-body active">
                    <h2>{{ $ratedRole === 'mentor' ? "Rate your mentor" : ($ratedRole === 'mentee' ? "Rate your mentee" : '') }}</h2>
                    <form class="jobPairsForm noInputStyles" method="POST"
                          action="{{ $ratedRole === 'mentor' ? route('rateMentor') : ($ratedRole === 'mentee' ? route('rateMentee') : '') }}"
                          enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="session_id" value="{{ $sessionId }}">
                        <input type="hidden" name="mentor_id" value="{{ $mentorId }}">
                        <input type="hidden" name="mentee_id" value="{{ $menteeId }}">
                        <input type="hidden" name="rating">
                        <div id="rating-container" class="col-md-12 text-center">
                        @for($i = 0; $i < 5; $i++)
                            <span id="star{{ $i + 1 }}" class="glyphicon glyphicon-star-empty rating-item"></span>
                        @endfor
                        </div>
                        <div class="col-md-12 text-center">
                            <input type="submit" class="btn btn-primary btn-ripple" value="Rate">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="bg-blur dark active">
            <div class="overlay"></div><!--.overlay-->
        </div><!--.bg-blur-->
    </div>
@endsection

@section('additionalFooter')
    <script>
        (function() {
            var ratingController = new window.RatingController();
            ratingController.init();
        })();
    </script>
@endsection
