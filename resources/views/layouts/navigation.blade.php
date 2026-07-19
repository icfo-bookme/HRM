<header class="h-[60px] bg-blue-900 border-b border-blue-300 flex items-center justify-between px-6 flex-shrink-0 z-20">
    <button id="collapseBtn"
        class="w-8 h-8 rounded-full border  bg-blue-900 flex items-center justify-center text-gray-100 hover:bg-blue-700 flex-shrink-0 shadow-sm">
        <i class="fa-solid fa-bars text-[15px]" id="collapseIcon"></i>
    </button>
    <!-- Search -->
    {{-- <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3  w-96">
        <i class="fas fa-search text-gray-200 text-xs"></i>
        <input type="text" placeholder="Search"
            class="bg-transparent border-none outline-none focus:outline-none focus:ring-0 text-sm text-gray-700 placeholder-gray-400 w-full" />
    </div> --}}

    <!-- Right side -->
    <div class="flex items-center gap-3">

        <!-- Notification bell dropdown -->
        <div class="relative group">
            <button id="notificationBell"
                class="relative w-9 h-9 border border-gray-200 rounded-lg flex items-center justify-center text-gray-200 hover:bg-gray-50 transition-colors">
                <i class="fas fa-bell text-[15px]"></i>
                @php
                    $unreadCount = Auth::user()->unreadNotifications->count();
                @endphp
                @if ($unreadCount > 0)
                    <span
                        class="absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center px-1 border-2 border-white">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                @endif
            </button>

            <!-- Notifications dropdown -->
            <div id="notificationDropdown"
                class="absolute right-0 top-10 w-80 bg-white border border-gray-200 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 max-h-96 overflow-y-auto">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <span class="text-sm font-semibold text-gray-800">Notifications</span>
                    @if ($unreadCount > 0)
                        <form method="POST" action="{{ route('notifications.markAllRead') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800 underline">Mark
                                all read</button>
                        </form>
                    @endif
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse(Auth::user()->unreadNotifications->take(10) as $notification)
                        @php $data = $notification->data; @endphp
                        <div class="px-4 py-3 hover:bg-pink-50 transition-colors">
                            <div class="flex items-start gap-2">
                                <div
                                    class="w-7 h-7 rounded-full bg-gradient-to-br from-pink-400 to-rose-500 flex items-center justify-center flex-shrink-0 text-white text-[10px]">
                                    <i class="fas fa-cake-candles"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-gray-700">{{ $data['message'] ?? '' }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">
                                        {{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                                <form method="POST" action="{{ route('notifications.markRead', $notification->id) }}"
                                    class="inline flex-shrink-0">
                                    @csrf
                                    <button type="submit" class="text-[10px] text-indigo-500 hover:text-indigo-700"
                                        title="Mark as read">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-6 text-center">
                            <i class="fas fa-bell-slash text-2xl text-gray-300 mb-2"></i>
                            <p class="text-xs text-gray-500">No notifications</p>
                        </div>
                    @endforelse
                </div>
                @if ($unreadCount > 0)
                    <div class="px-4 py-2 border-t border-gray-100 text-center">
                        <span class="text-[10px] text-gray-200">{{ $unreadCount }} unread notification(s)</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Settings gear -->
        <button
            class="w-9 h-9 border border-gray-200 rounded-lg flex items-center justify-center text-gray-200 hover:bg-gray-50 transition-colors">
            <i class="fas fa-cog text-[15px]"></i>
        </button>

        <!-- Divider -->
        <div class="w-px h-6 bg-gray-200"></div>



        <!-- User chip -->
        <div class="hidden sm:flex sm:items-center sm:ms-6 relative group">

            <!-- Trigger -->
            <button type="button" class="flex items-center gap-2.5 cursor-pointer group focus:outline-none">

                <!-- Avatar -->
                <div
                    class="w-[34px] h-[34px] rounded-full overflow-hidden bg-gradient-to-br from-rose-400 to-pink-600 flex items-center justify-center flex-shrink-0 ring-2 ring-white shadow-sm">
                    <span class="text-white text-xs font-bold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </span>
                </div>

                <!-- Name -->
                <div class="leading-tight text-left">
                    <p class="text-[11px] text-gray-200 font-medium">Users</p>
                    <p class="text-[13px] text-gray-50 font-semibold">
                        {{ Auth::user()->name }}
                    </p>
                </div>

                <!-- Icon -->
                <i
                    class="fas fa-chevron-down text-[10px] text-gray-400 group-hover:text-gray-600 transition-colors"></i>
            </button>

            <!-- Dropdown -->
            <div
                class="absolute right-0 top-12 w-48 bg-white border border-gray-200 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">

                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    Profile
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-gray-50">
                        Log Out
                    </button>
                </form>

            </div>
        </div>

    </div>
</header>
