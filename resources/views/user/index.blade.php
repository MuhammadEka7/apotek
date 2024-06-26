@extends('layouts.template')

@section('content')
@if (Session::get('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
@endif
@if (Session::get('deleted'))
<div class="alert alert-warning">{{ Session::get('deleted') }}</div>
@endif
    <a href="{{  route('user.create')  }}" class="btn btn-secondary" style="float: right; margin-bottom: 10px;">Tambah Pengguna</a>
    <table class="table mt-5 table-striped table-bordered table-hovered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($user as $item)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $item['name'] }}</td>
                <td>{{ $item['email'] }}</td>
                <td>{{ $item['role'] }}</td>
                <td class="d-flex">
                    <a href="{{ route('user.edit', $item['id']) }}" class="btn btn-primary">Edit</a>
                    {{-- method::delete tidak bisa digunakan pada a href, harus melalui form action --}}
                    <form action="{{ route('user.delete', $item['id']) }}" method="post" class="ms-2">
                        @csrf
                        {{-- menimpa/mengubah methos="post" agar menjadi methos="delete" sesuai dengan method route (::delete) --}}
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-end">
        @if ($user->count())
        {{ $user->links() }}           
        @endif
    </div>
@endsection