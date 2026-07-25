<!DOCTYPE html>
<html lang="en">

<head>
    <title>Astrologers</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>
    @php
        $logo = DB::table('systemflag')
            ->where('name', 'AdminLogo')
            ->select('value')
            ->first();
    @endphp
    <div style=" display: grid;
    grid-template-columns: auto auto ;">
        <div style="display: inline-block">
            <div style="height:100px;width:100px;margin-bottom:10px">
                <img alt="AstroGuru image" class="logo__image w-6" src="{{$logo->value}}"
                    style="height:100%;width:100%;object-fit:cover;border-radius:50%">
            </div>
        </div>
        <div style="display: inline-block;float:right">
            <h4>{{ $title }}</h4>
            <p>{{ $date }}</p>
        </div>
    </div>

    <table class="table table-bordered" aria-label="myPdf">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Contact No</th>
            <th>Gender</th>
            <th>Total Call Request</th>
            <th>Total Chat Request</th>
            <th>Status</th>
        </tr>
        @php
            $no = 0;
        @endphp
        @foreach ($astrologers as $ast)
            <tr>
                <td>{{ ++$no }}</td>
                <td>{{ $ast->name }}</td>
                <td>{{ $ast->contactNo }}</td>
                <td> {{ $ast->gender }}
                </td>
                <td>{{ $ast->totalCallRequest }}</td>
                <td>{{ $ast->totalChatRequest }}</td>
                <td>
                    @if ($ast->isVerified)
                        Verified
                    @else
                        UnVerified
                    @endif
                </td>
            </tr>
        @endforeach
    </table>

</body>

</html>
