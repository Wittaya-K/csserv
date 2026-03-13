@extends('layouts.admin')
@section('content')
<style>
    .department-title {
        font-size: 1.1rem;
        color: #1a3a6b !important;
    }

    .link-item {
        color: #1a3a6b;
        font-size: 0.97rem;
        transition: color 0.2s;
    }

    .link-item:hover {
        color: #0d6efd;
        text-decoration: underline !important;
    }

</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-0">
            <div class="col-sm-6">
                {{-- <h3></h3> --}}
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">แดชบอร์ด</a></li>
                    <li class="breadcrumb-item active">ลิงก์หน่วยงานที่เกี่ยวข้อง</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@can('view_link_create')

@endcan
<div class="card">
    <div class="card-header">
        ลิงก์หน่วยงานที่เกี่ยวข้อง
    </div>

    <div class="card-body">
        <div class="container py-5">
            @foreach($data as $department => $items)
            <div class="row mb-5 align-items-start">

                {{-- ชื่อหมวด (ซ้าย) --}}
                <div class="col-md-4">
                    <h4 class="department-title text-primary font-weight-bold">{{ $department }}</h4>
                </div>

                {{-- รายการลิงก์ (ขวา) --}}
                <div class="col-md-8">
                    @foreach($items as $item)
                    <div class="d-flex align-items-center py-2 border-bottom">
                        <span class="mr-3 text-secondary">
                            <i class="fas fa-link"></i>
                        </span>
                        <a href="{{ $item->link }}" target="_blank" class="text-decoration-none link-item mr-3">
                            {{ $item->link_name }}
                        </a>

                        {{-- ปุ่มดาวน์โหลด (แสดงเฉพาะเมื่อมีไฟล์) --}}
                        @if($item->file_name)
                            <a href="{{ asset('storage/manage_links/' . $item->file_name) }}"
                            download
                            class="btn btn-xs btn-outline-primary btn-sm ml-2"
                            title="ดาวน์โหลด {{ $item->file_name }}">
                                <i class="fas fa-download mr-1"></i>ดาวน์โหลด
                            </a>
                        @endif
                    </div>
                    @endforeach
                </div>

            </div>
            <hr class="my-2">
            @endforeach
        </div>
    </div>

    @endsection
    @section('scripts')
    @parent

    @endsection

