<div class="profileCard companyCard card_{{$company->id}}">
    <ul class="nav nav-tabs nav-justified" role="tablist">
        <li class="active"><a data-href="tab_1_{{$company->id}}" data-toggle="tab" data-id="{{$company->id}}" >{{trans('messages.details')}}</a></li>
        <li><a data-href="tab_2_{{$company->id}}" data-toggle="tab" data-id="{{$company->id}}" >{{trans('messages.mentors')}}</a></li>
    </ul>
    <div class="card card-user card-clickable card-clickable-over-content">
        <div class="profileCardBody">
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1_{{$company->id}}">
                    <h4 class="userDetail">{{$company->name}}
                    </h4>
                    @if($company->website != null)
                        <a href="{{$company->website}}" target="_blank">{{trans('messages.website')}}</a>
                    @endif
                    @if($company->description != null)
                        <p class="userDetail">{{$company->description}}</p>
                    @endif
                    @if($loggedInUser != null)
                        @if($loggedInUser->userHasAccessToCRUDSystemUser())
                            <div class="clickable-button">
                                <div class="layer bg-orange"></div>
                                <a class="btn btn-floating btn-orange initial-position floating-open"><i class="fa fa-cog"
                                                                                                         aria-hidden="true"></i></a>
                            </div>

                            <div class="layered-content bg-orange">
                                <div class="overflow-content">
                                    <ul class="borderless float-left">

                                        <li><a href="{{route('showEditCompanyForm', $company->id)}}"
                                               class="btn btn-flat btn-ripple"><i class="fa fa-pencil"
                                                                                  aria-hidden="true"></i> Edit</a></li>
                                        <li>
                                            <a data-toggle="modal"
                                               data-companyName="{{$company->name}}"
                                               data-companyId="{{$company->id}}"
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
                <div class="tab-pane" id="tab_2_{{$company->id}}">
                    @if($company->hasMentors())
                        <div class="companyAttrsList"><b>Mentors:</b>
                            <ul class="borderless">
                            @foreach($company->mentors as $mentor)
                                <li>{{$mentor->first_name . ' ' . $mentor->last_name}}</li>
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
            @if($company->accountManager != null)
                <div class="companyAttrsList"><b>{{trans('messages.company_account_manager')}}: </b>
                    {{$company->accountManager->first_name . ' ' . $company->accountManager->last_name}}
                </div>
            @endif
        </div><!--.card-footer-->
    </div><!--.card-->
</div>