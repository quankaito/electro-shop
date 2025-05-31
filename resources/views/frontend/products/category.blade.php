@extends('layouts.app')

@section('title', 'Sản phẩm ' . $category->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Nhúng livewire component ProductList, truyền categorySlug = $category->slug --}}
    @livewire('frontend.product.product-list', ['categorySlug' => $category->slug])
</div>
@endsection
