@props(['pageTitle' => 'Admin Panel', 'pageSubtitle' => null])
<x-admin-layout :pageTitle="$pageTitle" :pageSubtitle="$pageSubtitle">
    {{ $slot }}
</x-admin-layout>
