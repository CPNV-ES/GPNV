<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>GPNV</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="{{ URL::asset('css/font-awesome.4.4.0.min.css') }}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/fonts.google.lato.css') }}"/>

    <!-- Styles -->
    @yield('css')
    <link rel="stylesheet" href="{{ URL::asset('css/bootstrap.3.3.7.min.css') }}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/template.css') }}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/logBook.css') }}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/awesome-bootstrap-checkbox.css') }}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/tasks.css') }}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/checkList.css') }}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/scenario.css') }}"/>
    <link rel="stylesheet" href="{{ URL::asset('css/style.css') }}"/>
    <link rel="stylesheet" href="{{ URL::asset('js/summernote-0.8.2/summernote.css') }}">

    <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-toggle.2.2.2.min.css') }}">

    <style>
        body {
            font-family: 'Lato';
        }

        .fa-btn {
            margin-right: 6px;
        }
    </style>
</head>
<body id="app-layout">
<nav id="top" class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="{{ route('home') }}">GPNV <div class="version">{{getVersion()}}</div></a>

          <!-- Collapsed Hamburger -->
          <button type="button" class="navbar-toggle" data-toggle="collapse"
                  data-target="#nav-collapse">
              <span class="sr-only">Toggle Navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
          </button>
      </div>
      <div class="collapse navbar-collapse" id="nav-collapse">
        <ul class="nav navbar-nav">
        @if (Auth::user())
          @if (Auth::user()->role->id == 2)
            <li class="hidden-xs"><a>|</a></li>
            <li><a href="{{ route('admin') }}">Admin</a></li>
          @endif

          <li class="hidden-xs"><a>|</a></li>
          <li><a href="{{ route('project.create') }}">Nouveau projet</a></li>
          @if(Route::current() ->getName() === 'project.show')
            <li class="hidden-xs"><a>|</a></li>
            <li><a href="{{ route('home') }}">Tous les projets</a></li>
          @endif
        @endif
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <!-- Authentication Links -->
            @if (Auth::guest())
                <li><a href="{{ route('saml_login') }}">Login</a></li>
            @else
                <li><a href="{{route('user.show', Auth::user()->id)}}">{{Auth::user()->fullname}}</a></li>
            @endif
        </ul>
      </div>
    </div>
</nav>
<input type="hidden" name="_token" value="{{ csrf_token() }}">


@yield('content')
<a id="ancre" class="btn btn-default btn-lg" href="#top">
  <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span>
</a>

<script src="{{ URL::asset('js/jquery.2.1.4.min.js') }}"></script>
<script src="{{ URL::asset('js/jquery.ntm.js') }}"></script>
<script src="{{ URL::asset('js/bootbox.min.js') }}"></script>
<script src="{{ URL::asset('js/bootstrap.3.3.7.min.js' )}}"></script>
<script src="{{ URL::asset('js/scripts.js') }}"></script>

<script src="{{ URL::asset('css/jquery-ui.1.12.1.css') }}"></script>
<script src="{{ URL::asset('js/jquery-ui.1.12.1.min.js') }}"></script>
<script src="{{ URL::asset('js/summernote-0.8.2/summernote.min.js') }}"></script>
<script src="{{ URL::asset('js/bootstrap-toggle.2.2.2.min.js') }}"></script>

@stack('scripts')

<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    $(document).ready(function () {
        $('.tree-menu').ntm();
    });
</script>



</body>
</html>
