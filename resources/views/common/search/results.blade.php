<div class="row">
    <div class="col-md-4">
        <div class="result result-users">
            <h4>Mentors <small>({{ $mentors->count() }})</small></h4>

            <ul class="list-material">
                @foreach($mentors as $mentor)
                <li class="has-action-left">
                    @include('common.search.mentor-single', ['mentor' => $mentor])
                </li>
                @endforeach
            </ul>

        </div><!--.results-user-->
    </div><!--.col-->
    <div class="col-md-4">
        <div class="result result-users">
            <h4>Mentees <small>({{ $mentees->count() }})</small></h4>

            <ul class="list-material">
                @foreach($mentees as $mentee)
                <li class="has-action-left">
                    @include('common.search.mentee-single', ['mentee' => $mentee])
                </li>
                @endforeach
            </ul>

        </div><!--.col-->
    </div>
    <div class="col-md-4">
        <div class="result result-users">
            <h4>Users <small>({{ $users->count() }})</small></h4>

            <ul class="list-material">
                @foreach($users as $user)
                <li class="has-action-left">
                    @include('common.search.user-single', ['user' => $user])
                </li>
                @endforeach
            </ul>
        </div><!--.col-->
    </div><!--.col-->

</div><!--.row-->
