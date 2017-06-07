<div class="panel">
    <div class="panel-heading">
        <div class="panel-title">
            <h4>FILTERS</h4>
        </div>
    </div><!--.panel-heading-->
    <div class="panel-body filtersContainer noInputStyles" id="mentorsFilters" data-url="{{ route('filterMentors') }}">
        <div class="row">
            <div class="col-md-2 filterName">Name</div><!--.col-md-3-->
            <div class="col-md-6">
                <div class="inputer">
                    <div class="input-wrapper">
                        <input name="mentorName" class="form-control" placeholder="Mentor name" type="text" id="mentorName">
                    </div>
                </div>
            </div><!--.col-md-6-->
        </div>
        <div class="row">
            <div class="col-md-2 filterName">Age</div><!--.col-md-3-->
            <div class="col-md-6">
                <input id="age" name="age" placeholder="age" type="text">
            </div><!--.col-md-6-->
        </div>
        <div class="row">
            <div class="col-md-2 filterName">Specialty</div>
            <div class="col-md-4">
                <select data-placeholder="Choose specialty" name="specialty" id="specialtiesSelect" class="chosen-select">
                    <option><!-- Empty option allows the placeholder to take effect. --><option>
                    @foreach($specialties as $specialty)
                        <option value="{{$specialty->id}}">{{$specialty->name}}</option>
                    @endforeach
                </select>
            </div><!--.col-md-6-->
            <div class="col-md-2 filterName">Company</div>
            <div class="col-md-4">
                <select data-placeholder="Choose a company" name="company" id="companiesSelect" class="chosen-select">
                    <option><!-- Empty option allows the placeholder to take effect. --><option>
                    @foreach($companies as $company)
                        <option value="{{$company->id}}">{{$company->name}}</option>
                    @endforeach
                </select>
            </div><!--.col-md-6-->
        </div>
        <div class="row">
            <div class="col-md-2 filterName">Availability</div>
            <div class="col-md-4">
                <select data-placeholder="Choose availability status" name="availability" id="availabilitySelect" class="chosen-select" @if(Route::currentRouteName() == 'showMenteeProfilePage') disabled="disabled" @endif>
                    <option><!-- Empty option allows the placeholder to take effect. --><option>
                    @foreach($statuses as $status)
                        <option value="{{$status->id}}" {{ $status->id == 1 && Route::currentRouteName() == 'showMenteeProfilePage'? 'selected' : '' }}>{{$status->description}}</option>
                    @endforeach
                </select>
            </div><!--.col-md-6-->
            <div class="col-md-2 filterName">Residence</div>
            <div class="col-md-4">
                <select data-placeholder="Choose a residence" name="residence" id="residencesSelect" class="chosen-select">
                    <option><!-- Empty option allows the placeholder to take effect. --><option>
                    @foreach($residences as $residence)
                        <option value="{{$residence->id}}">{{$residence->name}}</option>
                    @endforeach
                </select>
            </div><!--.col-md-6-->
        </div>
        <div class="row">
            <div class="col-md-2 filterName">Completed sessions</div>
            <div class="col-md-4">
                <select data-placeholder="Choose number of completed sessions" name="completedSessionsCount" id="completedSessionsCountSelect" class="chosen-select">
                    <option><!-- Empty option allows the placeholder to take effect. --><option>
                    @for($i = 0; $i < 5; $i++)
                        <option value="{{  $i + 1 }}">@if($i === 4) {{ $i . "+" }} @else {{ $i + 1 }} @endif completed @if($i === 0) session @else sessions @endif</option>
                    @endfor
                </select>
            </div><!--.col-md-6-->
            <div class="col-md-2 filterName">Average rating</div>
            <div class="col-md-4">
                <select data-placeholder="Choose average rating" name="averageRating" id="averageRatingSelect" class="chosen-select">
                    <option><!-- Empty option allows the placeholder to take effect. --><option>
                    @for($i = 0; $i < 5; $i++)
                        <option value="{{  $i + 1 }}">{{ $i + 1 }} / 5</option>
                    @endfor
                </select>
            </div><!--.col-md-6-->
        </div>
        <div class="row">
            <div class="col-md-2 filterName">
                <label for="only-externally-subscribed">Subscribed from external form</label>
            </div>
            <div class="col-md-4">
                <div class="icheckbox">
                    <input type="checkbox" name="only_externally_subscribed" id="only-externally-subscribed">
                </div>
            </div>
            <div class="col-md-2 filterName">
                <label for="available-with-cancelled-session">Mentors with cancelled last session</label>
            </div>
            <div class="col-md-4">
                <div class="icheckbox">
                    <input type="checkbox" name="available_with_cancelled_session" id="available-with-cancelled-session">
                </div>
            </div>
        </div>
        <div class="form-buttons">
            <div class="row">
                <div class="col-md-offset-3 col-md-9">
                    <button id="searchBtn" class="searchBtn btn btn-primary btn-ripple margin-right-10">
                        {{trans('messages.search')}} <i class="fa fa-search" aria-hidden="true"></i>
                    </button>

                    <button id="clearSearchBtn" class="searchBtn btn btn-flat-primary btn-ripple margin-right-10">
                        {{trans('messages.clear_filters')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="errorMsg" class="alert alert-danger stickyAlert margin-top-20 margin-bottom-20 margin-left-100 hidden" role="alert"></div>
