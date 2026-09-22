@extends($layout)

@section('title', 'Notifications')

@section($contentSection)
    <x-notification-center
        :notifications="$notifications"
        :total="$total"
        :unread-total="$unreadTotal"
        :read-total="$readTotal"
        :active-status="$activeStatus"
        :active-search="$activeSearch"
        :active-category="$activeCategory"
        :category-options="$categoryOptions"
        index-route="user.notifications.index"
        variant="rdc"
        subtitle="Restez informé de l'activité de votre compte et de vos demandes."
    />
@endsection
