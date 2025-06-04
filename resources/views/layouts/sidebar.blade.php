@php
    $user = Auth::user();
    $isHomeroomTeacher = false;
    if ($user && $user->teacher) {
        $isHomeroomTeacher = \App\Models\HomeroomAssignment::where('teacher_id', $user->teacher->nip)
            ->whereHas('academicYear', function($q) { $q->where('is_active', true); })
            ->exists();
    }
@endphp
<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    @foreach (json_decode(MenuHelper::Menu()) as $menu)
        <li class="nav-header">{{ strtoupper($menu->nama_menu) }}</li>
        @foreach ($menu->submenus as $submenu)
            @if (count($submenu->submenus) == '0')
                @php
                    $isLaporPrestasi = strtolower($submenu->nama_menu) === 'lapor prestasi';
                    $isLaporPelanggaran = strtolower($submenu->nama_menu) === 'lapor pelanggaran';
                @endphp
                @if((!$isLaporPrestasi && !$isLaporPelanggaran) || $isHomeroomTeacher)
                <li class="nav-item text-sm">
                    <a href="{{ url($submenu->url) }}"
                        class="nav-link {{ Request::segment(1) == $submenu->url ? 'active' : '' }}">
                        <i class="nav-icon {{ $submenu->icon }}"></i>
                        <p>
                            {{ ucwords($submenu->nama_menu) }}
                        </p>
                    </a>
                </li>
                @endif
            @else
                @foreach ($submenu->submenus as $url)
                    @php
                        $urls[] = $url->url;
                    @endphp
                @endforeach
                <li class="nav-item text-sm {{ in_array(Request::segment(1), $urls) ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ in_array(Request::segment(1), $urls) ? 'active' : '' }}">
                        <i class="nav-icon {{ $submenu->icon }}"></i>
                        <p>
                            {{ ucwords($submenu->nama_menu) }}
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @foreach ($submenu->submenus as $endmenu)
                            @php
                                $isLaporPrestasi = strtolower($endmenu->nama_menu) === 'lapor prestasi';
                                $isLaporPelanggaran = strtolower($endmenu->nama_menu) === 'lapor pelanggaran';
                            @endphp
                            @if((!$isLaporPrestasi && !$isLaporPelanggaran) || $isHomeroomTeacher)
                            <li class="nav-item text-sm">
                                <a href="{{ url($endmenu->url) }}"
                                    class="nav-link {{ Request::segment(1) == $endmenu->url ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>{{ ucwords($endmenu->nama_menu) }}</p>
                                </a>
                            </li>
                            @endif
                        @endforeach
                    </ul>
                </li>
            @endif
        @endforeach
    @endforeach
</ul>
