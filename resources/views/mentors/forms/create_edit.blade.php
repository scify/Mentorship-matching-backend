@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4 >{{$formTitle}}</h4>
                    </div>

                </div><!--.panel-heading-->
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12 col-lg-8">
                            <form class="jobPairsForm noInputStyles" method="POST"
                                  action="{{($mentor->id == null ? route('createMentor') : route('editMentor', $mentor->id))}}"
                                  enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                @if($loggedInUser != null)
                                    @if($loggedInUser->userHasAccessToCRUDMentorsAndMentees())
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="selecterTitle">{{trans('messages.availability')}}</div>
                                            </div>
                                            <div class="col-md-10 ">
                                                <div class="switcher">
                                                    <input id="switcher1" type="checkbox" hidden="hidden" name="is_available" {{$mentor['is_available'] == true ? 'checked' : ''}}>
                                                    <label for="switcher1"></label>
                                                </div>
                                            </div><!--.switcher-->
                                        </div>
                                    @endif
                                @endif
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- First name-->
                                        <div class="{{ $errors->first('first_name')?'has-error has-feedback':'' }}">
                                            <div class="inputer floating-label">
                                                <div class="input-wrapper">
                                                    <input type="text" class="form-control" name="first_name" value="{{ old('first_name') != '' ? old('first_name') : $mentor['first_name']}}">
                                                    <label for="first_name">{{trans('messages.first_name')}}</label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('first_name') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Last name-->
                                        <div class="{{ $errors->first('last_name')?'has-error has-feedback':'' }}">
                                            <div class="inputer floating-label">
                                                <div class="input-wrapper">
                                                    <input type="text" class="form-control" name="last_name" value="{{ old('last_name') != '' ? old('last_name') : $mentor['last_name']}}">
                                                    <label for="last_name">{{trans('messages.last_name')}}</label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('last_name') }}</span>

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- E-mail -->
                                        <div class="{{ $errors->first('email')?'has-error has-feedback':'' }}">
                                            <div class="inputer floating-label">
                                                <div class="input-wrapper">
                                                    <input type="email" class="form-control" name="email"
                                                           value="{{ old('email') != '' ? old('email') : $mentor['email']}}">
                                                    <label for="email">{{trans('messages.email')}}</label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('email') }}</span>

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Year of birth -->
                                        <div class="{{ $errors->first('')?'has-error has-feedback':'' }}">
                                            <div class="inputer floating-label">
                                                <div class="input-wrapper">
                                                    <input type="number" class="form-control" name="year_of_birth"
                                                           value="{{ old('year_of_birth') != '' ? old('year_of_birth') : $mentor['year_of_birth']}}">
                                                    <label for="year_of_birth">{{trans('messages.year_of_birth')}}</label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('year_of_birth') }}</span>

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- Address -->
                                        <div class="{{ $errors->first('address')?'has-error has-feedback':'' }}">
                                            <div class="inputer floating-label">
                                                <div class="input-wrapper">
                                                    <input type="text" class="form-control" name="address"
                                                           value="{{ old('address') != '' ? old('address') : $mentor['address']}}">
                                                    <label for="email">{{trans('messages.address')}}</label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('address') }}</span>

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Residence Area -->
                                        <div class="margin-bottom-5 selecterTitle">{{trans('messages.residence')}}</div>
                                        <select data-placeholder="select" name="residence_id" class="chosen-select">
                                            <option><!-- Empty option allows the placeholder to take effect. --><option>
                                            @foreach($residences as $residence)
                                                <option value="{{$residence->id}}" {{$mentor['residence_id'] == $residence->id ? 'selected' : ''}}>{{$residence->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- Phone -->
                                        <div class="{{ $errors->first('phone')?'has-error has-feedback':'' }}">
                                            <div class="inputer floating-label">
                                                <div class="input-wrapper">
                                                    <input type="text" class="form-control" name="phone"
                                                           value="{{ old('phone') != '' ? old('phone') : $mentor['phone']}}">
                                                    <label for="email">{{trans('messages.phone')}}</label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('phone') }}</span>

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Cell Phone -->
                                        <div class="{{ $errors->first('cell_phone')?'has-error has-feedback':'' }}">
                                            <div class="inputer floating-label">
                                                <div class="input-wrapper">
                                                    <input type="text" class="form-control" name="cell_phone"
                                                           value="{{ old('cell_phone') != '' ? old('cell_phone') : $mentor['cell_phone']}}">
                                                    <label for="email">{{trans('messages.cell_phone')}}</label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('cell_phone') }}</span>

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- University -->
                                        <div class="{{ $errors->first('university_name')?'has-error has-feedback':'' }}">
                                            <div class="inputer floating-label">
                                                <div class="input-wrapper">
                                                    <input type="text" class="form-control" name="university_name"
                                                           value="{{ old('university_name') != '' ? old('university_name') : $mentor['university_name']}}">
                                                    <label for="email">{{trans('messages.university')}}</label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('university_name') }}</span>

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- University Department -->
                                        <div class="{{ $errors->first('university_department_name')?'has-error has-feedback':'' }}">
                                            <div class="inputer floating-label">
                                                <div class="input-wrapper">
                                                    <input type="text" class="form-control" name="university_department_name"
                                                           value="{{ old('university_department_name') != '' ? old('university_department_name') : $mentor['university_department_name']}}">
                                                    <label for="email">{{trans('messages.university_department')}}</label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('university_department_name') }}</span>

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- Company -->
                                        <div class="{{ $errors->first('company_name')?'has-error has-feedback':'' }}">
                                            <div class="inputer floating-label">
                                                <div class="input-wrapper">
                                                    <input type="text" class="form-control" name="company_name"
                                                           value="{{ old('company_name') != '' ? old('company_name') : $mentor['company_name']}}">
                                                    <label for="email">{{trans('messages.company')}}</label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('company_name') }}</span>

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Company Sector -->
                                        <div class="{{ $errors->first('company_sector')?'has-error has-feedback':'' }}">
                                            <div class="inputer floating-label">
                                                <div class="input-wrapper">
                                                    <input type="text" class="form-control" name="company_sector"
                                                           value="{{ old('company_sector') != '' ? old('company_sector') : $mentor['company_sector']}}">
                                                    <label for="email">{{trans('messages.company_sector')}}</label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('company_sector') }}</span>

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- Job position -->
                                        <div class="{{ $errors->first('job_position')?'has-error has-feedback':'' }}">
                                            <div class="inputer floating-label">
                                                <div class="input-wrapper">
                                                    <input type="text" class="form-control" name="job_position"
                                                           value="{{ old('job_position') != '' ? old('job_position') : $mentor['job_position']}}">
                                                    <label for="email">{{trans('messages.job_position')}}</label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('job_position') }}</span>

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Job Experience years -->
                                        <div class="{{ $errors->first('job_experience_years')?'has-error has-feedback':'' }}">
                                            <div class="inputer floating-label">
                                                <div class="input-wrapper">
                                                    <input type="text" class="form-control" name="job_experience_years"
                                                           value="{{ old('job_experience_years') != '' ? old('job_experience_years') : $mentor['job_experience_years']}}">
                                                    <label for="email">{{trans('messages.job_experience_years')}}</label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('job_experience_years') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="selecterTitle form-full-row">{{trans('messages.specialty_form_description')}}</div>
                                        <select data-placeholder="{{trans('messages.choose_specialties')}}" name="specialties[][id]" class="chosen-select" multiple>
                                            @foreach($specialties as $specialty)
                                                <option value="{{$specialty->id}}" {{in_array($specialty->id, $mentorSpecialtiesIds)? 'selected':''}}>{{$specialty->name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="help-block">{{ $errors->first('skills') }}</span>
                                    </div> <!-- Specialty -->
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Industry -->
                                        <div class="selecterTitle form-full-row">{{trans('messages.mentor_industry_form_description')}}</div>
                                        <select data-placeholder="{{trans('messages.choose_specialties')}}" name="industries[][id]" class="chosen-select" multiple>
                                            @foreach($industries as $industry)
                                                <option value="{{$industry->id}}" {{in_array($industry->id, $mentorIndustriesIds)? 'selected':''}}>{{$industry->name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="help-block">{{ $errors->first('skills') }}</span>
                                    </div>
                                </div>
                                @if($loggedInUser != null)
                                    @if($loggedInUser->userHasAccessToCRUDMentorsAndMentees())
                                        <div class="row">
                                            <div class="col-md-6">
                                                <!-- Industry -->
                                                <div class="selecterTitle form-full-row">{{trans('messages.company')}}</div>
                                                <select data-placeholder="Choose the company the mentor works at" name="company_id" class="chosen-select">
                                                    <option value="">No Company</option>
                                                    @foreach($companies as $company)
                                                        <option value="{{$company->id}}" {{$company->id == $mentor['company_id']? 'selected':''}}>{{$company->name}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="help-block">{{ $errors->first('skills') }}</span>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Skills -->
                                        <div class="selecterTitle form-full-row">{{trans('messages.skills.capitalAll')}}</div>
                                        <div class="inputer">
                                            <div class="input-wrapper">
                                                <textarea class="form-control js-auto-size" rows="1" name="skills">{{ old('skills') != '' ? old('skills') : $mentor['skills']}}</textarea>
                                            </div>
                                        </div>
                                        <span class="help-block">{{ $errors->first('skills') }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Reference -->
                                        <div class="{{ $errors->first('reference')?'has-error has-feedback':'' }}">
                                            <div class="inputer floating-label">
                                                <div class="input-wrapper">
                                                    <input type="text" class="form-control" name="reference"
                                                           value="{{ old('reference') != '' ? old('reference') : $mentor['reference']}}">
                                                    <label for="reference">{{trans('messages.reference_form')}}</label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('reference') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 submitBtnContainer margin-top-100">
                                        <button type="button" class="btn btn-flat-primary">
                                            <a class="cancelTourCreateBtn noStyleLink" href="{{ URL::route('showAllMentors') }}">{{trans('messages.cancel_btn')}}</a>
                                        </button>
                                        <button type="submit" id="gameFlavorSubmitBtn" class="btn btn-primary btn-ripple margin-left-10">
                                            {{($mentor->id == null ? trans('messages.create_btn') : trans('messages.edit_btn'))}}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('additionalFooter')
    <script>
        $( document ).ready(function() {
            var controller = new FormController();
            controller.init();
        });
    </script>
@endsection
