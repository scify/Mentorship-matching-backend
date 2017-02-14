@extends('layouts.app')
@section('content')
    <div class="col-md-12 centeredVertically">
        <div class="panel">
            <div class="panel-heading">
                <h4 class="col-md-12">{{$formTitle}}</h4>
            </div><!--.panel-heading-->
            <div class="panel-body">
                <form class="jobPairsForm noInputStyles" method="POST"
                      action="{{($mentor->id == null ? route('createMentor') : route('editMentor', $mentor->id))}}"
                      enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
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
                            <!-- Age -->
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
                            <!-- Residence Area -->
                            <h5 class="margin-top-40">{{trans('messages.residence')}}</h5>
                            <select data-placeholder="Choose residence area" name="residence_id" class="chosen-select">
                                <option><!-- Empty option allows the placeholder to take effect. --><option>
                                @foreach($residences as $residence)
                                    <option value="{{$residence->id}}" {{$mentor['residence_id'] == $residence->id ? 'selected' : ''}}>{{$residence->name}}</option>
                                @endforeach
                            </select>
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
                            <div class="formRow row">
                                <div class="col-md-3 formElementName">{{trans('messages.specialty_form_description')}}</div><!--.col-md-3-->
                                <div class="col-md-9">
                                    <select data-placeholder="{{trans('messages.choose_specialties')}}" name="specialties[][id]" class="chosen-select" multiple>
                                        @foreach($specialties as $specialty)
                                            <option value="{{$specialty->id}}" {{in_array($specialty->id, $mentorSpecialtiesIds)? 'selected':''}}>{{$specialty->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="formRow row">
                                <div class="col-md-3 formElementName">{{trans('messages.mentor_industry_form_description')}}</div><!--.col-md-3-->
                                <div class="col-md-9">
                                    <select data-placeholder="{{trans('messages.choose_specialties')}}" name="industries[][id]" class="chosen-select" multiple>
                                        @foreach($industries as $industry)
                                            <option value="{{$industry->id}}" {{in_array($industry->id, $mentorIndustriesIds)? 'selected':''}}>{{$industry->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @if($loggedInUser != null)
                                @if($loggedInUser->userHasAccessToCRUDMentorsAndMentees())
                                    <div class="formRow row">
                                        <div class="col-md-3 formElementName">Select Company</div><!--.col-md-3-->
                                        <div class="col-md-9">
                                            <select data-placeholder="Choose the company the mentor works at" name="company_id" class="chosen-select">
                                                <option value="">No Company</option>
                                                @foreach($companies as $company)
                                                    <option value="{{$company->id}}" {{$company->id == $mentor['company_id']? 'selected':''}}>{{$company->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                        <div class="col-md-6">
                            <!-- Linkedin -->
                            <div class="{{ $errors->first('linkedin_url')?'has-error has-feedback':'' }}">
                                <div class="inputer floating-label">
                                    <div class="input-wrapper">
                                        <input type="text" class="form-control" name="linkedin_url"
                                               value="{{ old('linkedin_url') != '' ? old('linkedin_url') : $mentor['linkedin_url']}}">
                                        <label for="email">{{trans('messages.linkedin')}}</label>
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('linkedin_url') }}</span>

                            </div>
                        @if($loggedInUser != null)
                            @if($loggedInUser->userHasAccessToCRUDMentorsAndMentees())
                                <!-- Is available -->
                                    <div class="formRow row">
                                        <div class="col-md-3 formElementName">Availability</div><!--.col-md-3-->
                                        <div class="col-md-9">
                                            <div class="switcher">
                                                <input id="switcher1" type="checkbox" hidden="hidden" name="is_available" {{$mentor['is_available'] == true ? 'checked' : ''}}>
                                                <label for="switcher1"></label>
                                            </div><!--.switcher-->
                                        </div><!--.col-md-9-->
                                    </div><!--.row-->
                            @endif
                        @endif
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
                            <!-- Skills -->
                            <div class="{{ $errors->first('skills')?'has-error has-feedback':'' }}">
                                <div class="inputer floating-label">
                                    <div class="input-wrapper">
                                        <textarea class="form-control" name="skills" placeholder="{{trans('messages.skills')}}">{{ old('skills') != '' ? old('skills') : $mentor['skills']}}</textarea>
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('skills') }}</span>

                            </div>
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
                    <div class="submitBtnContainer margin-top-100">
                        <button type="button" class="btn btn-flat-primary">
                            <a class="cancelTourCreateBtn noStyleLink" href="{{ URL::route('home') }}">{{trans('messages.cancel_btn')}}</a>
                        </button>
                        <button type="submit" id="gameFlavorSubmitBtn" class="btn btn-primary btn-ripple margin-left-10">
                            {{($mentor->id == null ? trans('messages.create_btn') : trans('messages.edit_btn'))}}
                        </button>
                    </div>
                </form>
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
