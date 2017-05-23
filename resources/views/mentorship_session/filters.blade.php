<div class="panel">
    <div class="panel-heading">
        <div class="panel-title">
            <h4>FILTERS</h4>
        </div>
    </div><!--.panel-heading-->
    <div class="panel-body filtersContainer noInputStyles mentorshipSessionFilters" id="mentorshipSessionsFilters" data-url="{{ route('filterMentorshipSessions') }}">
        <div class="row">
            <div class="col-md-2 filterName">Mentors' name</div><!--.col-md-3-->
            <div class="col-md-6">
                <div class="inputer">
                    <div class="input-wrapper">
                        <input name="mentorName" class="form-control" placeholder="Mentor name" type="text" id="mentorName">
                    </div>
                </div>
            </div><!--.col-md-6-->
        </div>
        <div class="row">
            <div class="col-md-2 filterName">Mentees' name</div><!--.col-md-3-->
            <div class="col-md-6">
                <div class="inputer">
                    <div class="input-wrapper">
                        <input class="form-control" id="menteeName" name="menteeName" placeholder="Mentee name" type="text">
                    </div>
                </div>
            </div><!--.col-md-6-->
        </div>
        <div class="row">
            <div class="col-md-2 filterName">Status start range</div>
            <div class="col-md-4">
                <select data-placeholder="Choose session status" name="startStatusId" id="startStatusesSelect" class="chosen-select">
                    <option><!-- Empty option allows the placeholder to take effect. --><option>
                    @foreach($statuses as $status)
                        <option value="{{$status->id}}">{{$status->description}}</option>
                    @endforeach
                </select>
            </div><!--.col-md-6-->
            <div class="col-md-2 filterName">Status end range</div>
            <div class="col-md-4">
                <select data-placeholder="Choose session status" name="endStatusId" id="endStatusesSelect" class="chosen-select">
                    <option><!-- Empty option allows the placeholder to take effect. --><option>
                    @foreach($statuses as $status)
                        <option value="{{$status->id}}">{{$status->description}}</option>
                    @endforeach
                </select>
            </div><!--.col-md-6-->
        </div>
        <div class="row">
            <div class="col-md-2 filterName">Session started</div>
            <div class="col-md-6">
                <div class="inputer">
                    <div class="input-wrapper">
                        <input type="text" class="form-control bootstrap-daterangepicker-basic-range"
                               name="sessionStartedDatesRange" placeholder="{{trans('messages.session_started_between_dates')}}" value="">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2 filterName">Session completed</div>
            <div class="col-md-6">
                <div class="inputer">
                    <div class="input-wrapper">
                        <input type="text" class="form-control bootstrap-daterangepicker-basic-range"
                               name="sessionCompletedDatesRange" placeholder="{{trans('messages.session_completed_between_dates')}}" value="">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            @if(isset($displayAccountManagerFilter))
                @if($displayAccountManagerFilter)
                    <div class="col-md-2 filterName">Account Manager</div>
                    <div class="col-md-4">
                        <select data-placeholder="Choose account manager" name="accountManagerId" id="accountManagersSelect" class="chosen-select">
                            <option><!-- Empty option allows the placeholder to take effect. --><option>
                            @foreach($accountManagers as $accountManager)
                                <option value="{{$accountManager->id}}">{{$accountManager->first_name . " " .
                                    $accountManager->last_name}}</option>
                            @endforeach
                        </select>
                    </div><!--.col-md-6-->
                @endif
            @endif
            @if(isset($displayMatcherFilter))
                @if($displayMatcherFilter)
                    <div class="col-md-2 filterName">Matcher</div>
                    <div class="col-md-4">
                        <select data-placeholder="Choose matcher" name="matcherId" id="matcherSelect" class="chosen-select">
                            <option><!-- Empty option allows the placeholder to take effect. --><option>
                            @foreach($matchers as $matcher)
                                <option value="{{$matcher->id}}">{{$matcher->first_name . " " .
                            $matcher->last_name}}</option>
                            @endforeach
                        </select>
                    </div><!--.col-md-6-->
                @endif
            @endif
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
