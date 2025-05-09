@extends('dashboard.layouts.adminlayout')

@section('title', $title)

@section('content')
    <div>
        <x-button-add href="{{ route('users.create') }}">
            Tambah {{ $buttonText }}
        </x-button-add>

        @if (session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif

        @if (session('error'))
            <x-alert type="error" :message="session('error')" />
        @endif

        @php
            $tableHeadings = ['Nama', 'Email', 'Role', 'No. Telepon', 'Aksi'];
            $userProperties = ['nama', 'email', 'role.nama_role', 'no_telp'];
        @endphp

        <x-table>
            <x-slot name="head">
                @foreach ($tableHeadings as $heading)
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $heading }}
                    </th>
                @endforeach
            </x-slot>
            <x-slot name="body">
                @foreach ($users as $user)
                    <tr>
                        @foreach ($userProperties as $property)
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ data_get($user, $property) }}
                            </td>
                        @endforeach
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <x-button-edit :href="route('users.edit', $user->id)" />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                            <x-button-delete :action="route('users.destroy', $user->id)" />
                        </td>
                    </tr>
                @endforeach
            </x-slot>
        </x-table>
    </div>
@endsection