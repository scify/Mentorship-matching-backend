@extends('layouts.app')
@section('content')
    <div class="col-md-12 centeredVertically">
        <div class="panel">
            <div class="panel-heading">
                <h4 class="col-md-12">Filters</h4>
            </div><!--.panel-heading-->
            <div class="panel-body filtersContainer noInputStyles">
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-2 filterName">Specialty</div><!--.col-md-3-->
                        <div class="col-md-6">
                            <select data-placeholder="Choose specialty" name="specialty" id="specialtiesSelect" class="chosen-select">
                                <option value="">No specialty selected<option>
                                @foreach($specialties as $specialty)
                                    <option value="{{$specialty->id}}">{{$specialty->name}}</option>
                                @endforeach
                            </select>
                        </div><!--.col-md-6-->
                    </div>
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
                </div>
                <div class="col-md-3">
                    <div class="row">
                        <button id="searchBtn" class="searchBtn btn btn-primary btn-ripple margin-right-10">
                            {{trans('messages.search')}}
                        </button>
                    </div>
                    <div class="row">
                        <button id="clearFiltersBtn" class="searchBtn btn btn-flat-primary btn-ripple margin-right-10">
                            {{trans('messages.clear_filters')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div><!--.row-->
    <div class="col-md-12 centeredVertically">
        <div class="loading-bar indeterminate margin-top-10 hidden loader"></div>

        <div id="errorMsg" class="alert alert-danger stickyAlert margin-top-20 margin-bottom-20 margin-left-100 hidden" role="alert"></div>

        <div id="usersList">
            @include('mentors.list')
        </div>
    </div>
@endsection

