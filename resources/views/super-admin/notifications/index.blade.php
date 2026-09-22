@extends('layouts.super-admin')

@section('header_title', 'Centre de Notifications')

@section('content')
    <x-notification-center
        :notifications="$notifications"
        :total="$total"
        :unread-total="$unreadTotal"
        :read-total="$readTotal"
        :active-status="$activeStatus"
        :active-search="$activeSearch"
        :active-category="$activeCategory"
        :category-options="$categoryOptions"
        index-route="super-admin.notifications.index"
        variant="super"
        subtitle="Gardez une vue claire sur les notifications système et les actions sensibles."
    />
@endsection
