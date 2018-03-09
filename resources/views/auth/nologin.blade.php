@extends('layouts.app')

@section('content')
<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading">Non connecté</div>
        <div class="panel-body">
          Il semblerait que vous ne soyez pas connecté au site <a href="http://intranet.cpnv.ch">intranet.cpnv.ch</a>.</br>
          Veuillez vous <a href="{{ route('saml_login') }}">logguer</a>.
        </div>
    </div>
</div>
@endsection
