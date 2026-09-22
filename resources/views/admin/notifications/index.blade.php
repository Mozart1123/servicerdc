@extends('layouts.admin')

@section('title', 'Notifications')
@section('header_title', 'Notifications')
@section('page_title', 'Centre de Notifications')
@section('page_subtitle', 'Retrouvez toute l\'activité administrative qui vous concerne.')

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
        index-route="admin.notifications.index"
        read-all-route="admin.notifications.read-all"
        read-route="admin.notifications.read"
        variant="rdc"
        subtitle="Suivez les alertes, actions et messages liés à l'administration."
    />
@endsection
