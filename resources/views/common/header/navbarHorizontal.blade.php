<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="{{url('home')}}">Job Pairs</a>
        </div>
        <ul class="nav navbar-nav">
            <li class="{{ (Route::current()->getName() == 'home') ? 'active' : '' }}"><a href="{{url('home')}}"><i class="fa fa-home" aria-hidden="true"></i></a></li>
            <li><a href="http://www.job-pairs.gr" target="_blank">About</a></li>
            <li class="{{ (Route::current()->getName() == 'showContactForm') ? 'active' : '' }}"><a href="{{route('showContactForm')}}">Contact</a></li>
        </ul>
        <div class="pull-right">
            <ul class="nav navbar-nav">
                <li>
                    @if(\Illuminate\Support\Facades\Auth::check())
                        <a class="pull-right" href="{{ url('/logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a>
                        <form id="logout-form" action="{{ url('/logout') }}" method="POST"
                              style="display: none;">{{ csrf_field() }} </form>
                    @else
                        <a class="pull-right" href="{{ url('/login') }}">
                            <i class="fa fa-sign-in" aria-hidden="true"></i> Sign in
                        </a>
                    @endif
                </li>
            </ul>
        </div>
    </div>
</nav>