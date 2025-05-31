@extends('layouts.app')

@section('title', 'Tất Cả Sản Phẩm')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Nhúng livewire component ProductList, không truyền categorySlug => hiển thị full list --}}
    @livewire('frontend.product.product-list')
</div>
@endsection
