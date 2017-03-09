<div class="profileCard companyCard card_{{$companyViewModel->company->id}}">
    <ul class="nav nav-tabs nav-justified" role="tablist">
        <li class="active"><a data-href="tab_1_{{$companyViewModel->company->id}}" data-toggle="tab" data-id="{{$companyViewModel->company->id}}" >{{trans('messages.details')}}</a></li>
        <li><a data-href="tab_2_{{$companyViewModel->company->id}}" data-toggle="tab" data-id="{{$companyViewModel->company->id}}" >{{trans('messages.mentors')}}</a></li>
    </ul>
    <div class="card card-user card-clickable card-clickable-over-content">
        <div class="profileCardBody">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1_{{$companyViewModel->company->id}}">
                    <h4 class="userDetail">{{$companyViewModel->company->name}}
                        @if($companyViewModel->company->website != null)
                            <small><a href="{{$companyViewModel->company->website}}" target="_blank">{{$companyViewModel->company->website}}</a></small>
                        @endif
                    </h4>
                    <div class="companyMentorsInfo">
                        {{$companyViewModel->totalMentorsNum}} total mentors ({{$companyViewModel->availableMentorsNum}} available, {{$companyViewModel->matchedMentorsNum}} matched)
                    </div>
                    @if($companyViewModel->company->description != null)
                        <p class="userDetail">{{$companyViewModel->company->description}}</p>
                    @endif
                    @if($loggedInUser != null)
                        @if($loggedInUser->userHasAccessToCRUDSystemUsers())
                            <div class="clickable-button">
                                <div class="layer bg-orange"></div>
                                <a class="btn btn-floating btn-orange initial-position floating-open"><i class="fa fa-cog"
                                                                                                         aria-hidden="true"></i></a>
                            </div>

                            <div class="layered-content bg-orange">
                                <div class="overflow-content">
                                    <ul class="borderless float-left">

                                        <li><a href="{{route('showEditCompanyForm', $companyViewModel->company->id)}}"
                                               class="btn btn-flat btn-ripple"><i class="fa fa-pencil"
                                                                                  aria-hidden="true"></i> Edit</a></li>
                                        <li>
                                            <a data-toggle="modal"
                                               data-companyName="{{$companyViewModel->company->name}}"
                                               data-companyId="{{$companyViewModel->company->id}}"
                                               class="btn btn-flat btn-ripple deleteCompanyBtn">
                                                <i class="fa fa-trash-o" aria-hidden="true"></i> Delete
                                            </a>
                                        </li>
                                    </ul>
                                    <ul class="borderless float-right">
                                    </ul>
                                </div><!--.overflow-content-->
                                <div class="clickable-close-button">
                                    <a class="btn btn-floating initial-position floating-close"><i class="fa fa-times"
                                                                                                   aria-hidden="true"></i></a>
                                </div>
                            </div>
                        @endif
                    @endif

                </div>
                <div class="tab-pane" id="tab_2_{{$companyViewModel->company->id}}">
                    @if($companyViewModel->company->hasMentors())
                        <div class="companyAttrsList"><b>Mentors:</b>
                            <ul class="borderless">
                            @foreach($companyViewModel->company->mentors as $mentor)
                                    <li><a href="{{route('showMentorProfilePage', $mentor->id)}}">{{$mentor->first_name . ' ' . $mentor->last_name}}</a>
                                    @if($mentor->status != null)
                                        | <small class="{{$mentor->status->status}}">{{$mentor->status->description}}</small>
                                    @endif
                                </li>
                            @endforeach
                            </ul>
                        </div>
                        @else
                        <p>{{trans('messages.company_no_mentors')}}</p>
                    @endif

                </div>

            </div>
        </div><!--.card-body-->
        <div class="card-footer">
            @if($companyViewModel->company->accountManager != null)
                <div class="companyAttrsList"><b>{{trans('messages.company_account_manager')}}: </b>
                    <a href="{{route('showUserProfile', $companyViewModel->company->accountManager->id)}}">{{$companyViewModel->company->accountManager->first_name . ' ' . $companyViewModel->company->accountManager->last_name}}</a>
                </div>
            @else
                <h6>{{trans('messages.company_no_account_manager')}}</h6>
            @endif
        </div><!--.card-footer-->
    </div><!--.card-->
</div>