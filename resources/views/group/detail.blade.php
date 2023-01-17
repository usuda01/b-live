@extends('layouts.app')
@section('title', $group->name . ' - ')
@section('content')
    <group-component
        :group="{{ $group->toJson() }}"
    ></group-component>
@endsection


