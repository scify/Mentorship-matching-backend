@extends('layouts.app')
@section('content')
    <div id="companies-filters-panel" class="panel">
        <div class="panel-heading">
            <div class="panel-title">
                <h4>FILTERS</h4>
            </div>
        </div><!--.panel-heading-->
        <div class="panel-body filtersContainer noInputStyles" id="companiesFilters" data-url="{{ route('filterCompanies') }}">
            <div class="row">
                <div class="col-md-2">Name</div><!--.col-md-2-->
                <div class="col-md-6">
                    <select data-placeholder="Choose company" name="company_id" class="chosen-select">
                        <option><!-- Empty option allows the placeholder to take effect. --><option>
                        @foreach($companyViewModels as $companyViewModel)
                            <option value="{{$companyViewModel->company->id}}">{{$companyViewModel->company->name}}</option>
                        @endforeach
                    </select>
                </div><!--.col-md-8-->
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
    <div class="col-md-12 centeredVertically">
        <div id="companiesList">
            @include('companies.list')
        </div>
    </div>

@endsection

