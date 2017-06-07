@extends('layouts.app')
@section('content')
    <div class="row" id="createMentor">
        <div class="col-md-12">
            <?php
            $selectedSpecialties = array();
            $selectedIndustries = array();
            ?>
            @if(!empty(old('industries')))
                @foreach( old('industries') as $key => $selectedIndustry )
                        <?php array_push($selectedIndustries, $selectedIndustry["id"])
                        ?>
                @endforeach
            @endif
            @if(!empty(old('specialties')))
                @foreach( old('specialties') as $key => $selectedSpecialty )
                    <?php array_push($selectedSpecialties, $selectedSpecialty["id"])
                    ?>
                @endforeach
            @endif
            <div class="panel">
                @if(!$publicForm)
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>{{$formTitle}}</h4>
                        </div>
                    </div><!--.panel-heading-->
                @endif
                <div class="panel-body">
                    <div class="row">
                        @if($publicForm)
                            <div class="col-lg-2">
                            </div>
                        @endif
                        <div class="col-lg-8">
                            <div class="requiredExplanation margin-bottom-10">(<span class="requiredIcon">*</span> {{trans('messages.required_field')}})</div>
                            <form class="jobPairsForm noInputStyles" method="POST"
                                  action="{{($mentor->id == null ? route('createMentor') : route('editMentor', $mentor->id))}}"
                                  enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                @if($loggedInUser != null && !$publicForm)
                                    @if($loggedInUser->userHasAccessToCRUDMentorsAndMentees())
                                        <div class="row">
                                            <div class="col-md-6">
                                                <!-- Mentor status -->
                                                <div class="margin-bottom-5 selecterTitle">{{trans('messages.mentor_status')}} <span class="requiredIcon">*</span></div>
                                                <select data-placeholder="select" name="status_id" class="chosen-select"
                                                        data-original-value="{{ $mentor['status_id'] }}"
                                                        data-enable-follow-up-date="2,4">
                                                    @foreach($mentorStatuses as $mentorStatus)
                                                        <option value="{{$mentorStatus->id}}" {{$mentor['status_id'] == $mentorStatus->id || old('status_id') == $mentorStatus->id ? 'selected' : ''}}>{{$mentorStatus->description}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6" id="status-change-comment" style="display: none;">
                                                <!-- Status change comment -->
                                                <div class="{{ $errors->first('status_history_comment')?'has-error has-feedback':'' }}">
                                                    <div class="inputer floating-label">
                                                        <div class="input-wrapper">
                                                            <input type="text" class="form-control" name="status_history_comment"
                                                                   value="{{ old('status_history_comment')}}">
                                                            <label for="status_history_comment">{{trans('messages.status_history_comment')}} <span class="requiredIcon">*</span></label>
                                                        </div>
                                                    </div>
                                                    <span class="help-block">{{ $errors->first('status_history_comment') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" id="status-change-follow-up-date" style="display: none;">
                                            <div class="col-md-6">
                                                <!-- Mentor follow up date -->
                                                <div class="{{ $errors->first('follow_up_date')?'has-error has-feedback':'' }}">
                                                    <div class="inputer floating-label">
                                                        <div class="input-wrapper">
                                                            <input type="text" class="form-control bootstrap-daterangepicker-basic"
                                                                   name="follow_up_date" value="{{ old('follow_up_date')}}">
                                                            <label for="follow_up_date">{{trans('messages.follow_up_date')}}</label>
                                                        </div>
                                                    </div>
                                                    <span class="help-block">{{ $errors->first('follow_up_date') }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <!-- Do not contact again -->
                                                <div class="icheckbox" style="margin-top: 35px;">
                                                    <label>
                                                        <input type="checkbox" name="do_not_contact">
                                                        <label>{{trans('messages.do_not_contact')}} <span class="requiredIcon">*</span></label>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- First name-->
                                        <div class="{{ $errors->first('first_name')?'has-error has-feedback':'' }}">
                                            <div class="inputer floating-label">
                                                <div class="input-wrapper">
                                                    <input required type="text" class="form-control" name="first_name" value="{{ old('first_name') != '' ? old('first_name') : $mentor['first_name']}}">
                                                    <label for="first_name">{{trans('messages.first_name')}} <span class="requiredIcon">*</span></label>
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
                                                    <input required type="text" class="form-control" name="last_name" value="{{ old('last_name') != '' ? old('last_name') : $mentor['last_name']}}">
                                                    <label for="last_name">{{trans('messages.last_name')}} <span class="requiredIcon">*</span></label>
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
                                                    <input required type="email" class="form-control" name="email"
                                                           value="{{ old('email') != '' ? old('email') : $mentor['email']}}">
                                                    <label for="email">{{trans('messages.email')}} <span class="requiredIcon">*</span></label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('email') }}</span>

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Year of birth -->
                                        <div class="{{ $errors->first('year_of_birth')?'has-error has-feedback':'' }}">
                                            <div class="inputer floating-label">
                                                <div class="input-wrapper">
                                                    <input required type="number" class="form-control" name="year_of_birth"
                                                           min="{{ date('Y') - 75 }}" max="{{ date('Y') - 18 }}"
                                                           value="{{ old('year_of_birth') != '' ? old('year_of_birth') : $mentor['year_of_birth']}}">
                                                    <label for="year_of_birth">{{trans('messages.year_of_birth')}} <span class="requiredIcon">*</span></label>
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
                                                    <input required type="text" class="form-control" name="address"
                                                           value="{{ old('address') != '' ? old('address') : $mentor['address']}}">
                                                    <label for="address">{{trans('messages.address')}} <span class="requiredIcon">*</span></label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('address') }}</span>

                                        </div>
                                    </div>
                                    <div class="col-md-6 inputer {{ $errors->first('residence_id')?'has-error has-feedback':'' }}">
                                        <!-- Residence Area -->
                                        <div class="margin-bottom-5 selecterTitle">{{trans('messages.residence')}} <span class="requiredIcon">*</span></div>
                                        <select data-placeholder="select" name="residence_id" class="chosen-select" data-show-name-on-id="4">
                                            <option><!-- Empty option allows the placeholder to take effect. --><option>
                                            @foreach($residences as $residence)
                                                <option value="{{$residence->id}}" {{$mentor['residence_id'] == $residence->id || old('residence_id') == $residence->id ? 'selected' : ''}}>{{$residence->name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="help-block">{{ $errors->first('residence_id') }}</span>
                                    </div>

                                </div>
                                <div class="row residenceName" @if(empty($mentor['residence_name']) &&
                                    empty(old('residence_name')) && empty($errors->first('residence_name'))) style="display: none;" @endif>
                                    <div class="col-md-6">
                                        <!-- Residence -->
                                        <div class="{{ $errors->first('residence_name')?'has-error has-feedback':'' }}">
                                            <div class="inputer floating-label">
                                                <div class="input-wrapper">
                                                    <input type="text" class="form-control" name="residence_name"
                                                           value="{{ old('residence_name') != '' ? old('residence_name') : $mentor['residence_name']}}">
                                                    <label for="residence_name">{{trans('messages.residence_name')}} <span class="requiredIcon">*</span></label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('residence_name') }}</span>

                                        </div>
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
                                                    <label for="phone">{{trans('messages.phone')}}</label>
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
                                                    <label for="cell_phone">{{trans('messages.cell_phone')}}</label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('cell_phone') }}</span>

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 inputer {{ $errors->first('education_level_id')?'has-error has-feedback':'' }}">
                                        <!-- Education Level -->
                                        <div class="margin-bottom-5 selecterTitle">{{trans('messages.education_level')}} <span class="requiredIcon">*</span></div>
                                        <select data-placeholder="select" name="education_level_id" class="chosen-select">
                                            <option><!-- Empty option allows the placeholder to take effect. --><option>
                                            @foreach($educationLevels as $educationLevel)
                                                <option value="{{$educationLevel->id}}" {{$mentor['education_level_id'] == $educationLevel->id || old('education_level_id') == $educationLevel->id ?
                                                    'selected' : ''}}>{{$educationLevel->name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="help-block">{{ $errors->first('education_level_id') }}</span>
                                    </div>
                                    <div class="col-md-6 inputer {{ $errors->first('university_id')?'has-error has-feedback':'' }}">
                                        <!-- University -->
                                        <div class="margin-bottom-5 selecterTitle">{{trans('messages.university')}} <span class="requiredIcon">*</span></div>
                                        <select data-placeholder="select" name="university_id" class="chosen-select" data-show-name-on-id="12">
                                            <option><!-- Empty option allows the placeholder to take effect. --><option>
                                            @foreach($universities as $university)
                                                <option value="{{$university->id}}" {{$mentor['university_id'] == $university->id || old('university_id') == $university->id ? 'selected' : ''}}>{{$university->name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="help-block">{{ $errors->first('university_id') }}</span>
                                    </div>
                                </div>
                                <div class="row universityName" @if(empty($mentor['university_name']) &&
                                    empty(old('university_name')) && empty($errors->first('university_name'))) style="display: none;" @endif>
                                    <div class="col-md-6">
                                        <!-- University -->
                                        <div class="{{ $errors->first('university_name')?'has-error has-feedback':'' }}">
                                            <div class="inputer floating-label">
                                                <div class="input-wrapper">
                                                    <input type="text" class="form-control" name="university_name"
                                                           value="{{ old('university_name') != '' ? old('university_name') : $mentor['university_name']}}">
                                                    <label for="university_name">{{trans('messages.university_name')}} <span class="requiredIcon">*</span></label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('university_name') }}</span>

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- University Department -->
                                        <div class="{{ $errors->first('university_department_name')?'has-error has-feedback':'' }}">
                                            <div class="inputer floating-label">
                                                <div class="input-wrapper">
                                                    <input type="text" class="form-control" name="university_department_name"
                                                           value="{{ old('university_department_name') != '' ? old('university_department_name') : $mentor['university_department_name']}}">
                                                    <label for="university_department_name">{{trans('messages.university_department')}} <span class="requiredIcon">*</span></label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('university_department_name') }}</span>

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Linkedin Profile -->
                                        <div class="{{ $errors->first('linkedin_url')?'has-error has-feedback':'' }}">
                                            <div class="inputer floating-label">
                                                <div class="input-wrapper">
                                                    <input type="text" class="form-control" name="linkedin_url"
                                                           value="{{ old('linkedin_url') != '' ? old('linkedin_url') : $mentor['linkedin_url']}}">
                                                    <label for="linkedin_url">{{trans('messages.linkedin')}}</label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('linkedin_url') }}</span>

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 inputer {{ $errors->first('company_id')?'has-error has-feedback':'' }}">
                                        <!-- Company -->
                                        <div class="selecterTitle" style="margin-top:9px">{{trans('messages.company')}} <span class="requiredIcon">*</span></div>
                                        <select name="company_id" class="select2-company col-md-12">
                                            <option></option>
                                            @foreach($companies as $company)
                                                <option value="{{$company->id}}" {{$company->id == $mentor['company_id'] || old('company_id') == $company->id ? 'selected':''}}>{{$company->name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="help-block">{{ $errors->first('company_id') }}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Company Sector -->
                                        <div class="{{ $errors->first('company_sector')?'has-error has-feedback':'' }}">
                                            <div class="inputer floating-label">
                                                <div class="input-wrapper">
                                                    <input type="text" class="form-control" name="company_sector"
                                                           value="{{ old('company_sector') != '' ? old('company_sector') : $mentor['company_sector']}}">
                                                    <label for="company_sector">{{trans('messages.company_sector')}} <span class="requiredIcon">*</span></label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('company_sector') }}</span>

                                        </div>
                                    </div>
                                    {{--<div class="col-md-6">--}}
                                        {{--<!-- Company -->--}}
                                        {{--<div class="{{ $errors->first('company_name')?'has-error has-feedback':'' }}">--}}
                                            {{--<div class="inputer floating-label">--}}
                                                {{--<div class="input-wrapper">--}}
                                                    {{--<input type="text" class="form-control" name="company_name"--}}
                                                           {{--value="{{ old('company_name') != '' ? old('company_name') : $mentor['company_name']}}">--}}
                                                    {{--<label for="company_name">OR ENTER YOUR OWN COMPANY:</label>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                            {{--<span class="help-block">{{ $errors->first('company_name') }}</span>--}}

                                        {{--</div>--}}
                                    {{--</div>--}}
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- Job position -->
                                        <div class="{{ $errors->first('job_position')?'has-error has-feedback':'' }}">
                                            <div class="inputer floating-label">
                                                <div class="input-wrapper">
                                                    <input type="text" class="form-control" name="job_position"
                                                           value="{{ old('job_position') != '' ? old('job_position') : $mentor['job_position']}}">
                                                    <label for="job_position">{{trans('messages.job_position')}} <span class="requiredIcon">*</span></label>
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
                                                    <input type="number" class="form-control" name="job_experience_years"
                                                           value="{{ old('job_experience_years') != '' ? old('job_experience_years') : $mentor['job_experience_years']}}">
                                                    <label for="job_experience_years">{{trans('messages.job_experience_years')}} <span class="requiredIcon">*</span></label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('job_experience_years') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 inputer {{ $errors->first('specialties')?'has-error has-feedback':'' }}">
                                        <div class="selecterTitle form-full-row">{{trans('messages.specialty_form_description')}} <span class="requiredIcon">*</span></div>
                                        <select data-placeholder="{{trans('messages.choose_specialties')}}" name="specialties[][id]" class="chosen-select" multiple>
                                            @foreach($specialties as $specialty)
                                                <option value="{{$specialty->id}}" {{in_array($specialty->id, $mentorSpecialtiesIds) || in_array($specialty->id, $selectedSpecialties) ? 'selected':''}}>{{$specialty->name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="help-block">{{ $errors->first('specialties') }}</span>
                                    </div> <!-- Specialty -->
                                </div>

                                <div class="row">
                                    <div class="col-md-12 inputer {{ $errors->first('industries')?'has-error has-feedback':'' }}">
                                        <!-- Industry -->
                                        <div class="selecterTitle form-full-row">{{trans('messages.mentor_industry_form_description')}} <span class="requiredIcon">*</span></div>
                                        <select data-placeholder="{{trans('messages.choose_specialties')}}" name="industries[][id]" class="chosen-select" multiple>

                                            @foreach($industries as $industry)
                                                <option value="{{$industry->id}}" {{in_array($industry->id, $mentorIndustriesIds) || in_array($industry->id, $selectedIndustries) ? 'selected':''}}>{{$industry->name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="help-block">{{ $errors->first('industries') }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 {{ $errors->first('skills')?'has-error has-feedback':'' }}">
                                        <!-- Skills -->
                                        <div class="inputer floating-label">
                                            <div class="input-wrapper">
                                                <textarea required class="form-control js-auto-size" rows="2" name="skills">{{ old('skills') != '' ? old('skills') : $mentor['skills']}}</textarea>
                                                <label for="skills">{{trans('messages.skills.capitalAll')}} <span class="requiredIcon">*</span></label>
                                            </div>
                                        </div>
                                        <span class="help-block">{{ $errors->first('skills') }}</span>
                                    </div>
                                </div>
                                @if($mentor->id == null)
                                <div class="row">
                                    <div class="col-md-12 inputer {{ $errors->first('reference_id')?'has-error has-feedback':'' }}">
                                        <!-- Reference (where did you hear about us) -->
                                        <div class="margin-bottom-5 selecterTitle">{{trans('messages.reference_form')}} <span class="requiredIcon">*</span></div>
                                        <select data-placeholder="select" name="reference_id" class="chosen-select" data-show-name-on-id="7">
                                            <option><!-- Empty option allows the placeholder to take effect. --><option>
                                            @foreach($references as $reference)
                                                <option value="{{$reference->id}}" {{$mentor['reference_id'] == $reference->id || old('reference_id') == $reference->id ? 'selected' : ''}}>{{$reference->name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="help-block">{{ $errors->first('reference_id') }}</span>
                                    </div>
                                </div>
                                <div class="row referenceText" @if(empty($mentor['reference_text'])) style="display: {{ $mentor['reference_id'] == 7 || old('reference_id') == 7 ? '' : 'none' }};" @endif>
                                    <div class="col-md-12">
                                        <!-- Reference -->
                                        <div class="{{ $errors->first('reference_text')?'has-error has-feedback':'' }}">
                                            <div class="inputer floating-label">
                                                <div class="input-wrapper">
                                                    <textarea class="form-control js-auto-size" rows="2" name="reference_text">{{ old('reference_text') != '' ? old('reference_text') : $mentor['reference_text']}}</textarea>
                                                    <label for="reference_text">{{trans('messages.reference_text')}} <span class="requiredIcon">*</span></label>
                                                </div>
                                            </div>
                                            <span class="help-block">{{ $errors->first('reference_text') }}</span>

                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if($loggedInUser == null)
                                    <div class="row font-size-smaller margin-top-20">
                                        <div class="col-md-6">
                                            <div class="icheckbox">
                                                <label>
                                                    <input type="checkbox" name="terms" required>
                                                    {{trans('messages.i_accept_the')}} <a target="_blank" href="http://www.job-pairs.gr/terms/">{{trans('messages.terms_and_conditions')}}</a>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="row margin-top-40">
                                    <!-- CV upload -->
                                    <div class="col-md-3">
                                        <div class="file-input-label">
                                            <label for="cv_file">{{trans('messages.upload_cv')}} (.pdf, .doc, .docx):</label>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <span class="btn btn-orange btn-file">
                                                <span class="fileinput-new">Select file</span>
                                                <span class="fileinput-exists">Change</span>
                                                <input type="file" name="cv_file" accept=".doc,.docx,.pdf">
                                            </span>
                                            <span class="fileinput-filename">
                                                @if(!empty($mentor['cv_file_name']))
                                                    <a href="{{ url('/') . '/uploads/cv_files/' . $mentor['cv_file_name']}}" target="_blank">
                                                        {{ $mentor['cv_file_name'] }}
                                                    </a>
                                                @endif
                                            </span>
                                            <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</a>
                                        </div>
                                    </div>
                                </div>
                                <input name="public_form" type="hidden" value="{{ $publicForm ? 'true' : 'false'  }}">
                                <input name="lang" type="hidden" value="{{ $language  }}">
                                <div class="row">
                                    <div class="col-md-12 submitBtnContainer margin-top-60">
                                        @if(!$publicForm)
                                            <button type="button" class="btn btn-flat-primary">
                                                <a class="cancelTourCreateBtn noStyleLink" href="{{ URL::route('showAllMentors') }}">{{trans('messages.cancel_btn')}}</a>
                                            </button>
                                        @endif
                                        <button type="submit" id="gameFlavorSubmitBtn" class="btn btn-primary btn-ripple {{ !$publicForm ? 'margin-left-10' : '' }}">
                                            {{($mentor->id == null ? trans('messages.create_btn') : trans('messages.edit_btn'))}}
                                        </button>
                                        @if($publicForm)
                                            @if(session('flash_message_success'))
                                                <div class="alert alert-success alert-dismissable">
                                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                                    <h4><i class="icon fa fa-check"></i> {{ session('flash_message_success') }}</h4>
                                                </div>
                                            @endif

                                            @if(session('flash_message_failure'))
                                                <div class="alert alert-danger alert-dismissable">
                                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                                    <h4><i class="icon fa fa-ban"></i> {{ session('flash_message_failure') }}</h4>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                        @if($publicForm)
                            <div class="col-lg-2">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('additionalFooter')
    @if($publicForm)
        <script src="{{asset(elixir('js/iframe-contentWindow.js'))}}"></script>
    @endif
    <script>
        $( document ).ready(function() {
            var controller = new FormController();
            controller.init();
            var availabilityStatusChangeHandler = new AvailabilityStatusChangeViewHandler();
            availabilityStatusChangeHandler.init("#createMentor");
            var universityHandler = new UniversityHandler();
            universityHandler.initHandler();
            var residenceHandler = new ResidenceHandler();
            residenceHandler.initHandler();
            var referenceHandler = new ReferenceHandler();
            referenceHandler.initHandler();
        });
    </script>
@endsection
