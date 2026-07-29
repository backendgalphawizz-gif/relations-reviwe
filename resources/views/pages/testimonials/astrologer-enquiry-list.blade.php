@extends('../layout/' . $layout)

@section('subcontent')

<h2 class="text-lg font-medium mt-10">Advisor Enquiry</h2>

<form method="GET" action="{{ route('astrologer.enquiry.list') }}" class="box p-4 mb-4">
    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-3">
            <input type="text" name="name" value="{{ request('name') }}"
                   class="form-control" placeholder="Name">
        </div>

        <div class="col-span-3">
            <input type="text" name="mobile" value="{{ request('mobile') }}"
                   class="form-control" placeholder="Mobile">
        </div>

        <div class="col-span-3">
            <input type="date" name="from_date" value="{{ request('from_date') }}"
                   class="form-control">
        </div>

        <div class="col-span-3">
            <input type="date" name="to_date" value="{{ request('to_date') }}"
                   class="form-control">
        </div>

        <div class="col-span-12 sm:col-span-3 flex gap-2">
            <button class="btn btn-primary w-full">Search</button>
            <a href="{{ route('astrologer.enquiry.list') }}" class="btn btn-secondary w-full">
                Reset
            </a>
        </div>
    </div>
</form>

@if(count($enquiries))
<table class="table table-report">
    <thead>
        <tr>
            <th>ID</th>
            <th>User Name</th>
            <th>Enquiry Name</th>
            
            <th>Mobile</th>
            <th>Profession</th>
            <th>Created</th>
            <th>Attachment</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($enquiries as $enquiry)
        <tr>
            <td>{{ $start + $loop->index }}</td>

            <td>
                {{ $enquiry->user->name ?? '—' }}
            </td>
            <td>
                {{ $enquiry->name ?? '—' }}
            </td>       
            <td>{{ $enquiry->mobile }}</td>
            <td>{{ $enquiry->profession }}</td>
            <td>{{ $enquiry->created_at->format('d M Y') }}</td>
            <td>
                @if (!empty($enquiry->file))
                    @php
                        $ext = pathinfo($enquiry->file, PATHINFO_EXTENSION);
                    @endphp

                    @if (in_array(strtolower($ext), ['jpg','jpeg','png','gif','webp']))
                        <a href="{{ asset($enquiry->file) }}" target="_blank">
                            <img src="{{ asset($enquiry->file) }}"
                                style="width:60px;height:60px;object-fit:cover;border-radius:6px;">
                        </a>

                    @elseif (strtolower($ext) === 'pdf')
                        <a href="{{ asset($enquiry->file) }}"
                        target="_blank"
                        class="text-blue-600 underline">
                            View PDF
                        </a>

                    @else
                        <span class="text-gray-400">Unsupported</span>
                    @endif
                @else
                    <span class="text-gray-400">—</span>
                @endif
            </td>
            
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="text-center p-5">No Data Found</div>
@endif

@endsection
