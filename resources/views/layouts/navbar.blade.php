<nav class="navbar navbar-expand-md shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            
            <picture class="logo-container">
                <img class="rounded-circle" src="{{ asset('images/pasq-eat.jpg') }}" alt="Deliveboo" id="logo">
            </picture>
            
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse align-items-baseline" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link on-hover" href="{{url('/') }}">{{ __('Home') }}</a>
                </li>
                @guest()

                @elseif (Auth::user()->restaurant)
                <li class="nav-item">
                    <a class="nav-link on-hover" href="{{route('admin.restaurants.show', Auth::user()->restaurant)}}">Ristorante</a>
                </li>
                @else
                <li class="nav-item">
                    <a class="nav-link on-hover" href="{{route('admin.restaurants.create')}}">Crea il tuo ristorante</a>
                </li>
                @endguest
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="glass-dropdown list-unstyled d-flex gap-3 mb-0">
                <!-- Authentication Links -->
                @guest
                <li class="nav-item">
                    <a class="nav-link on-hover" href="{{ route('login') }}">{{ __('Login') }}</a>
                </li>
                @if (Route::has('register'))
                <li class="nav-item">
                    <a class="nav-link on-hover" href="{{ route('register') }}">{{ __('Register') }}</a>
                </li>
                @endif
                @else
                <li class="active">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle on-hover" href="#" role="button" tabindex="0">
                        {{ Auth::user()->name }}
                    </a>
                    <div class="glass-dropdown-content" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ url('dashboard') }}">{{__('Dashboard')}}</a>
                        <a class="dropdown-item my-2" href="{{ url('profile') }}">{{__('Profile')}}</a>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>