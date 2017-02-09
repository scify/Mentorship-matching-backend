@extends('layouts.app')
@section('content')
    <div class="col-md-12 centeredVertically">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="col-md-12">{{$formTitle}}</h3>
            </div><!--.panel-heading-->
            <div class="panel-body">
                <form class="jobPairsForm noInputStyles" method="POST"
                      action="{{($mentee->id == null ? route('createMentee') : route('editMentee', $mentee->id))}}"
                      enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="row">
                        <div class="col-md-6">
                            <!-- First name-->
                            <div class="{{ $errors->first('first_name')?'has-error has-feedback':'' }}">
                                <div class="inputer floating-label">
                                    <div class="input-wrapper">
                                        <input required type="text" class="form-control" name="first_name" value="{{ old('first_name') != '' ? old('first_name') : $mentee['first_name']}}">
                                        <label for="first_name">First Name</label>
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('first_name') }}</span>

                            </div>
                            <!-- Last name-->
                            <div class="{{ $errors->first('last_name')?'has-error has-feedback':'' }}">
                                <div class="inputer floating-label">
                                    <div class="input-wrapper">
                                        <input required type="text" class="form-control" name="last_name" value="{{ old('last_name') != '' ? old('last_name') : $mentee['last_name']}}">
                                        <label for="last_name">Last Name</label>
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('last_name') }}</span>

                            </div>
                            <!-- E-mail -->
                            <div class="{{ $errors->first('email')?'has-error has-feedback':'' }}">
                                <div class="inputer floating-label">
                                    <div class="input-wrapper">
                                        <input required type="email" class="form-control" name="email"
                                               value="{{ old('email') != '' ? old('email') : $mentee['email']}}">
                                        <label for="email">Email</label>
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('email') }}</span>

                            </div>
                            <!-- Age -->
                            <div class="{{ $errors->first('age')?'has-error has-feedback':'' }}">
                                <div class="inputer floating-label">
                                    <div class="input-wrapper">
                                        <input required type="number" class="form-control" name="age"
                                               value="{{ old('age') != '' ? old('age') : $mentee['age']}}">
                                        <label for="email">Age</label>
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('age') }}</span>

                            </div>
                            <!-- Residence Area -->
                            <h5 class="margin-top-40">Residence Area:</h5>
                            <select required data-placeholder="Choose residence area" name="residence_id" class="chosen-select">
                                <option></option>
                                @foreach($residences as $residence)
                                    <option value="{{$residence->id}}" {{$mentee['residence_id'] == $residence->id ? 'selected' : ''}}>{{$residence->name}}</option>
                                @endforeach
                            </select>
                            <!-- Address -->
                            <div class="{{ $errors->first('address')?'has-error has-feedback':'' }}">
                                <div class="inputer floating-label">
                                    <div class="input-wrapper">
                                        <input required type="text" class="form-control" name="address"
                                               value="{{ old('address') != '' ? old('address') : $mentee['address']}}">
                                        <label for="email">Address</label>
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('address') }}</span>

                            </div>
                            <!-- Phone -->
                            <div class="{{ $errors->first('phone')?'has-error has-feedback':'' }}">
                                <div class="inputer floating-label">
                                    <div class="input-wrapper">
                                        <input type="text" class="form-control" name="phone"
                                               value="{{ old('phone') != '' ? old('phone') : $mentee['phone']}}">
                                        <label for="email">Phone</label>
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('phone') }}</span>

                            </div>
                            <!-- Cell Phone -->
                            <div class="{{ $errors->first('cell_phone')?'has-error has-feedback':'' }}">
                                <div class="inputer floating-label">
                                    <div class="input-wrapper">
                                        <input type="text" class="form-control" name="cell_phone"
                                               value="{{ old('cell_phone') != '' ? old('cell_phone') : $mentee['cell_phone']}}">
                                        <label for="email">Cell phone</label>
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('cell_phone') }}</span>

                            </div>
                            <!-- University -->
                            <div class="{{ $errors->first('university_name')?'has-error has-feedback':'' }}">
                                <div class="inputer floating-label">
                                    <div class="input-wrapper">
                                        <input required type="text" class="form-control" name="university_name"
                                               value="{{ old('university_name') != '' ? old('university_name') : $mentee['university_name']}}">
                                        <label for="email">University</label>
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('university_name') }}</span>

                            </div>
                            <!-- University Department -->
                            <div class="{{ $errors->first('university_department_name')?'has-error has-feedback':'' }}">
                                <div class="inputer floating-label">
                                    <div class="input-wrapper">
                                        <input required type="text" class="form-control" name="university_department_name"
                                               value="{{ old('university_department_name') != '' ? old('university_department_name') : $mentee['university_department_name']}}">
                                        <label for="email">University Department</label>
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('university_department_name') }}</span>

                            </div>
                            <!-- University graduation year -->
                            <div class="{{ $errors->first('university_graduation_year')?'has-error has-feedback':'' }}">
                                <div class="inputer floating-label">
                                    <div class="input-wrapper">
                                        <input required type="number" class="form-control" name="university_graduation_year"
                                               value="{{ old('university_graduation_year') != '' ? old('university_graduation_year') : $mentee['university_graduation_year']}}">
                                        <label for="email">University graduation year</label>
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('university_department_name') }}</span>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Linkedin -->
                            <div class="{{ $errors->first('linkedin_url')?'has-error has-feedback':'' }}">
                                <div class="inputer floating-label">
                                    <div class="input-wrapper">
                                        <input type="text" class="form-control" name="linkedin_url"
                                               value="{{ old('linkedin_url') != '' ? old('linkedin_url') : $mentee['linkedin_url']}}">
                                        <label for="email">Linkedin Profile</label>
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('linkedin_url') }}</span>

                            </div>
                            <!-- Is employed -->
                            <div class="row example-row margin-top-30">
                                <div class="col-md-3">Are you employed?</div><!--.col-md-3-->
                                <div class="col-md-9">
                                    <div class="switcher">
                                        <input id="switcher1" type="checkbox" hidden="hidden" name="is_employed" {{$mentee['is_employed'] == true ? 'checked' : ''}}>
                                        <label for="switcher1"></label>
                                    </div><!--.switcher-->
                                </div><!--.col-md-9-->
                            </div><!--.row-->
                            <!-- Job description -->
                            <div class="{{ $errors->first('job_description')?'has-error has-feedback':'' }}">
                                <div class="inputer floating-label">
                                    <div class="input-wrapper">
                                        <input type="text" class="form-control" name="job_description"
                                               value="{{ old('job_description') != '' ? old('job_description') : $mentee['job_description']}}">
                                        <label for="email">Job Description</label>
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('job_description') }}</span>

                            </div>
                            <div class="row margin-top-40">
                                <div class="col-md-3 margin-top-5">Select mentee specialties</div><!--.col-md-3-->
                                <div class="col-md-9">
                                    <select data-placeholder="Choose specialties" name="specialty_id" class="chosen-select" required>
                                        <option></option>
                                        @foreach($specialties as $specialty)
                                            <option value="{{$specialty->id}}" {{$specialty->id == $mentee['specialty_id']? 'selected':''}}>{{$specialty->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- Specialty Experience -->
                            <div class="{{ $errors->first('specialty_experience')?'has-error has-feedback':'' }}">
                                <div class="inputer floating-label">
                                    <div class="input-wrapper">
                                        <textarea required class="form-control" name="specialty_experience" placeholder="Specialty Experience">{{ old('specialty_experience') != '' ? old('specialty_experience') : $mentee['specialty_experience']}}</textarea>
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('specialty_experience') }}</span>

                            </div>
                            <!-- Expectations -->
                            <div class="{{ $errors->first('expectations')?'has-error has-feedback':'' }}">
                                <div class="inputer floating-label">
                                    <div class="input-wrapper">
                                        <textarea required class="form-control" name="expectations" placeholder="What do you expect to aquire from this program?">{{ old('expectations') != '' ? old('expectations') : $mentee['expectations']}}</textarea>
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('expectations') }}</span>

                            </div>
                            <!-- Career goals -->
                            <div class="{{ $errors->first('career_goals')?'has-error has-feedback':'' }}">
                                <div class="inputer floating-label">
                                    <div class="input-wrapper">
                                        <textarea required class="form-control" name="career_goals" placeholder="What are your career goals?">{{ old('career_goals') != '' ? old('career_goals') : $mentee['career_goals']}}</textarea>
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('career_goals') }}</span>

                            </div>
                            <!-- Reference -->
                            <div class="{{ $errors->first('reference')?'has-error has-feedback':'' }}">
                                <div class="inputer floating-label">
                                    <div class="input-wrapper">
                                        <input type="text" class="form-control" name="reference"
                                               value="{{ old('reference') != '' ? old('reference') : $mentee['reference']}}">
                                        <label for="email">Where did you hear about jobpairs?</label>
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('reference') }}</span>

                            </div>
                        </div>
                    </div>
                    <div class="submitBtnContainer margin-top-100">
                        <button type="button" class="btn btn-flat-primary">
                            <a class="cancelTourCreateBtn noStyleLink" href="{{ URL::route('home') }}">Cancel</a>
                        </button>
                        <button type="submit" id="gameFlavorSubmitBtn" class="btn btn-primary btn-ripple margin-left-10">
                            {{($mentee->id == null ? 'Create' : 'Edit')}}
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
