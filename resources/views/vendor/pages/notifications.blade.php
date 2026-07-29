@extends('vendor.app')

@push('css_or_js')
    <title>Notifications</title>
@endpush

@section('content')
    <div class="loader"></div>
    <div class="grid grid-cols-11 gap-x-6">
        <div class="intro-y col-span-12 2xl:col-span-12">
            <div class="intro-y box">
                <div class="flex flex-col sm:flex-row items-center p-2 border-b border-slate-200/60 dark:border-darkmode-400">
                    <h2 class="font-medium text-base mr-auto">Notifications</h2>
                </div>
            </div>

            @if ($notifications->count() > 0)
                <div class="intro-y box mt-4 overflow-auto">
                    <table class="table table-stripped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notifications as $notification)
                                <tr>
                                    <td>{{ $notifications->firstItem() + $loop->index }}</td>
                                    <td>
                                        @if (!empty($notification->image))
                                            <img src="{{ asset($notification->image) }}" alt="notification"
                                                style="width:48px;height:48px;object-fit:cover;border-radius:6px;">
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $notification->title ?? '—' }}</td>
                                    <td>{!! $notification->description ?? '—' !!}</td>
                                    <td>
                                        {{ $notification->created_at ? date('d M, Y h:i A', strtotime($notification->created_at)) : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5">
                                    {!! $notifications->links('vendor.pagination.advisor') !!}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="intro-y box mt-4 p-10 text-center">
                    <h3 class="text-slate-500">No notifications yet</h3>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        });
    </script>
@endpush
