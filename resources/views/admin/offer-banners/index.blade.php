@extends('layouts.app')

@section('header')
    <h2 class="page-title">Offer Banners</h2>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Banners</h3>
                    <div class="card-actions">
                        <a href="{{ route('business.offer-banners.create') }}" class="btn btn-primary"><i class="ti ti-plus"></i> Add</a>
                    </div>
                </div>
                <div class="card-body border-0 p-0">
                    <div class="table-responsive">
                        <table class="table card-table">
                            <thead>
                                <tr>
                                    <th class="w-1">Image</th>
                                    <th>Title</th>
                                    <th>Link</th>
                                    <th>Status</th>
                                    <th>Sort</th>
                                    <th class="w-1">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($banners as $banner)
                                    <tr>
                                        <td>
                                            @if ($banner->image_path)
                                                <img src="{{ Storage::url($banner->image_path) }}" alt="" style="max-height:36px">
                                            @else
                                                <span class="text-secondary">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $banner->title }}</td>
                                        <td class="text-secondary">{{ $banner->link }}</td>
                                        <td><span class="badge bg-{{ $banner->status === 'active' ? 'green' : 'yellow' }}-lt">{{ $banner->status }}</span></td>
                                        <td>{{ $banner->sort_order }}</td>
                                        <td>
                                            <a href="{{ route('business.offer-banners.edit', $banner) }}" class="btn btn-sm btn-ghost-secondary">Edit</a>
                                            <form method="POST" action="{{ route('business.offer-banners.destroy', $banner) }}" class="d-inline" onsubmit="return confirm('Delete?');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-ghost-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-secondary">No banners.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($banners->hasPages())
                    <div class="card-footer">{{ $banners->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
