<nav class="navbar navbar-expand-md navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('img/NovulutionsLogo.png') }}" alt="Novulution Logo">
        </a>

        <!-- Right Side Of Navbar -->
        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    <!-- Displaying initials in a circle with a gray background -->
                    <div class="profile_display" style="border-radius: 50%; border: 1px solid white; height:40px; width:40px">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->name)[1] ?? '', 0, 1)) }}
                    </div>
                    {{ Auth::user()->name }}
                </a>                

                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <!-- Logout Link -->
                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    </div>
</nav>

<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav navbar-nav-custom">
                @role('admin')
                <li class=" nav-item dropdown {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                @endrole
                <li class=" nav-item dropdown {{ request()->routeIs('booking.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('booking.index') }}">
                        <i class="fas fa-calendar-alt"></i> Booking
                    </a>
                </li>
                @role('driver')
                <li class=" nav-item dropdown {{ request()->routeIs('my-rides-bookings.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('my-rides-bookings.index') }}">
                        <i class="fa-solid fa-car-side"></i> My Ride
                    </a>
                </li>
                @endrole
                @role('admin')
                <li class=" nav-item dropdown {{ request()->routeIs('booking-approval.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('booking-approval.index') }}">
                        <i class="fa-solid fa-check-to-slot"></i> Approval
                    </a>
                </li>
                <li class=" nav-item dropdown {{ request()->routeIs('user-management.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('user-management.index') }}">
                        <i class="fas fa-users"></i> User Management
                    </a>
                </li>
                @endrole
            </ul>
        </div>
    </div>
</nav>
