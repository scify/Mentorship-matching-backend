<div class="search-layer">
    <div id="common-search" class="search" data-search-url="{{ route('search') }}">
        <form>
            <div class="form-group">
                <input type="text" id="input-search" class="form-control" placeholder="type something">
                <button type="submit" class="btn btn-default disabled btn-ripple"><i class="ion-search"></i></button>
            </div>
        </form>
    </div><!--.search-->
    <div class="loading-bar-wrapper">
        <div class="loading-bar indeterminate loader hidden"></div>
    </div>

    <div id="search-error-messages" class="alert alert-danger stickyAlert margin-top-20 margin-bottom-20 margin-right-50
        margin-left-50 hidden" role="alert"></div>

    <div id="search-results" class="results">

    </div><!--.results-->
</div>
