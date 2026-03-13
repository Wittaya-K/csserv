<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('admin.home') }}" class="brand-link">
        <img src="{{ url('image/logo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-gray">{{ trans('global.system') }}</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('admin.home') }}" class="nav-link">
                        <p>
                            <i class="fad fa-tachometer"></i>
                            <span>{{ trans('global.dashboard') }}</span>
                        </p>
                    </a>
                </li>
                @can('setting_access')
                {{-- <li class="nav-header">ตั้งค่า</li> --}}
                <li
                    class="nav-item has-treeview {{ request()->is('admin/assign_tasks*') ? 'menu-open' : '' }} {{ request()->is('admin/departments*') ? 'menu-open' : '' }} {{ request()->is('admin/service_assigns*') ? 'menu-open' : '' }} {{ request()->is('admin/prioritys*') ? 'menu-open' : '' }} {{ request()->is('admin/service_status*') ? 'menu-open' : '' }} {{ request()->is('admin/manage_links*') ? 'menu-open' : '' }}">
                    <a class="nav-link nav-dropdown-toggle">
                        <i class="fad fa-cog"></i>
                        <p>
                            <span>ตั้งค่า</span>
                            <i class="right fa fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('department_access')
                            <li class="nav-item">
                                <a href="{{ route('admin.departments.index') }}"
                                    class="nav-link {{ request()->is('admin/departments') || request()->is('admin/departments/*') ? 'active' : '' }}">
                                    <i class="fad fa-chevron-circle-right"></i>
                                    <p>
                                        <span>หน่วยงาน</span>
                                    </p>
                                </a>
                            </li>
                        @endcan
                        @can('priority_access')
                            <li class="nav-item">
                                <a href="{{ route('admin.prioritys.index') }}"
                                    class="nav-link {{ request()->is('admin/prioritys') || request()->is('admin/prioritys/*') ? 'active' : '' }}">
                                    <i class="fad fa-chevron-circle-right"></i>
                                    <p>
                                        <span>ชั้นความเร็ว</span>
                                    </p>
                                </a>
                            </li>
                        @endcan
                        @can('service_assign_access')
                            <li class="nav-item">
                                <a href="{{ route('admin.service_assigns.index') }}"
                                    class="nav-link {{ request()->is('admin/service_assigns') || request()->is('admin/service_assigns/*') ? 'active' : '' }}">
                                    <i class="fad fa-chevron-circle-right"></i>
                                    <p>
                                        <span>กำหนดงานบริการ</span>
                                    </p>
                                </a>
                            </li>
                        @endcan
                        @can('status_access')
                            <li class="nav-item">
                                <a href="{{ route('admin.service_status.index') }}"
                                    class="nav-link {{ request()->is('admin/service_status') || request()->is('admin/service_status/*') ? 'active' : '' }}">
                                    <i class="fad fa-chevron-circle-right"></i>
                                    <p>
                                        <span>สถานะงานบริการ</span>
                                    </p>
                                </a>
                            </li>
                        @endcan
                        @can('manage_link_access')
                        <li class="nav-item">
                            <a href="{{ route('admin.manage_links.index') }}"
                                class="nav-link {{ request()->is('admin/manage_links') || request()->is('admin/manage_links/*') ? 'active' : '' }}">
                                <i class="fad fa-chevron-circle-right"></i>
                                <p>
                                    <span>ลิงก์</span>
                                </p>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan
                {{-- <li class="nav-header">บริการ</li> --}}
                <li class="nav-item has-treeview {{ request()->is('admin/service_requests*') ? 'menu-open' : '' }}  {{ request()->is('admin/service_lists*') ? 'menu-open' : '' }} {{ request()->is('admin/view_links*') ? 'menu-open' : '' }}">
                    @can('service_request_access')
                    <a class="nav-link nav-dropdown-toggle">
                        <i class="fad fa-user-headset"></i>
                        <p>
                            <span>บริการ</span>
                            <i class="right fa fa-angle-left"></i>
                        </p>
                    </a>
                     @endcan
                    <ul class="nav nav-treeview">
                        @can('service_request_access')
                            <li class="nav-item">
                                <a href="{{ route('admin.service_requests.index') }}"
                                    class="nav-link {{ request()->is('admin/service_requests') || request()->is('admin/service_requests/*') ? 'active' : '' }}">
                                    <i class="fad fa-chevron-circle-right"></i>
                                    <p>
                                        <span>ขอใช้บริการ</span>
                                    </p>
                                </a>
                            </li>
                        @endcan
                        @can('view_link_access')
                        <li class="nav-item">
                            <a href="{{ route('admin.view_links.index') }}"
                                class="nav-link {{ request()->is('admin/view_links') || request()->is('admin/view_links/*') ? 'active' : '' }}">
                                <i class="fad fa-chevron-circle-right"></i>
                                <p>
                                    <span>ลิงก์หน่วยงานที่เกี่ยวข้อง</span>
                                </p>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @can('search_access')
                {{-- <li class="nav-header">ข้อมูล</li> --}}
                <li class="nav-item has-treeview {{ request()->is('admin/search*') ? 'menu-open' : '' }} {{ request()->is('admin/report_summary*') ? 'menu-open' : '' }}">
                    <a class="nav-link nav-dropdown-toggle">
                        <i class="fad fa-file-search"></i>
                        <p>
                            <span>ข้อมูล</span>
                            <i class="right fa fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('search_access')

                        @endcan
                        @can('search_access')
                            <li class="nav-item">
                                <a href="{{ route('admin.report_summary.index') }}"
                                    class="nav-link {{ request()->is('admin/report_summary') || request()->is('admin/report_summary/*') ? 'active' : '' }}">
                                    <i class="fad fa-chevron-circle-right"></i>
                                    <p>
                                        <span>รายงานสรุป</span>
                                    </p>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
                @endcan
                @can('user_management_access')
                    {{-- <li class="nav-header">จัดการผู้ใช้งาน</li> --}}
                    <li
                        class="nav-item has-treeview {{ request()->is('admin/permissions*') ? 'menu-open' : '' }} {{ request()->is('admin/roles*') ? 'menu-open' : '' }} {{ request()->is('admin/users*') ? 'menu-open' : '' }}">
                        <a class="nav-link nav-dropdown-toggle">
                            <i class="fad fa-users"></i>
                            <p>
                                <span>{{ trans('global.userManagement.title') }}</span>
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('permission_access')
                                <li class="nav-item">
                                    <a href="{{ route('admin.permissions.index') }}"
                                        class="nav-link {{ request()->is('admin/permissions') || request()->is('admin/permissions/*') ? 'active' : '' }}">
                                        <i class="fad fa-user-lock"></i>
                                        <p>
                                            <span>การเข้าถึง</span>
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('role_access')
                                <li class="nav-item">
                                    <a href="{{ route('admin.roles.index') }}"
                                        class="nav-link {{ request()->is('admin/roles') || request()->is('admin/roles/*') ? 'active' : '' }}">
                                        <i class="fad fa-shield"></i>
                                        <p>
                                            <span>สิทธิ์</span>
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('user_access')
                                <li class="nav-item">
                                    <a href="{{ route('admin.users.index') }}"
                                        class="nav-link {{ request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : '' }}">
                                        <i class="fad fa-user"></i>
                                        <p>
                                            <span>ผู้ใช้งาน</span>
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('system_access')

                @endcan
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
