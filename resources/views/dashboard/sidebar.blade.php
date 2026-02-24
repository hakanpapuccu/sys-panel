        <!--**********************************
            Sidebar start
        ***********************************-->
        <div class="dlabnav">
            <div class="dlabnav-scroll">
				<ul class="metismenu" id="menu">
                    <li><a class="" href="{{route('dashboard')}}" aria-expanded="false">
							<i class="fas fa-home"></i>
							<span class="nav-text">Anasayfa</span>
						</a>

                    </li>

                    @if(Auth::user()->hasPermission('view_vacations'))
                    <li><a class="" href="{{route('vacations')}}" aria-expanded="false">
                        <i class="fas fa-location-arrow"></i>
                        <span class="nav-text">İzinler</span>
                    </a>
                    </li>
                    @endif

                    @if(Auth::user()->hasPermission('view_tasks'))
                    <li><a class="" href="{{route('tasks.index')}}" aria-expanded="false">
                            <i class="fas fa-tasks"></i>
                            <span class="nav-text">Görevler</span>
                        </a>
                    </li>
                    @endif

                    @if(Auth::user()->hasPermission('view_announcements'))
                    <li><a class="" href="{{route('announcements.index')}}" aria-expanded="false">
                            <i class="fas fa-bullhorn"></i>
                            <span class="nav-text">Duyurular</span>
                        </a>
                    </li>
                    @endif

                    @if(Auth::user()->hasPermission('access_chat'))
                    <li><a class="" href="{{route('chat.index')}}" aria-expanded="false">
                            <i class="fas fa-comments"></i>
                            <span class="nav-text">Mesajlar</span>
                        </a>
                    </li>
                    @endif

                    @if(Auth::user()->hasPermission('view_files'))
                    <li><a class="" href="{{route('files.index')}}" aria-expanded="false">
                            <i class="fas fa-folder"></i>
                            <span class="nav-text">Dosya Paylaşımı</span>
                        </a>
                    </li>
                    @endif

                    @if(Auth::user()->hasPermission('view_polls'))
                    <li><a class="" href="{{route('polls.index')}}" aria-expanded="false">
                            <i class="fas fa-poll"></i>
                            <span class="nav-text">Anketler</span>
                        </a>
                    </li>
                    @endif

                    <li><a class="" href="{{route('calendar.index')}}" aria-expanded="false">
                            <i class="fas fa-calendar-alt"></i>
                            <span class="nav-text">İş Takvimi</span>
                        </a>
                    </li>

                    @if(Auth::user()->hasPermission('view_users'))
                    <li><a class="" href="{{route('admin.users.index')}}" aria-expanded="false">
                            <i class="fas fa-users"></i>
                            <span class="nav-text">Kullanıcı Yönetimi</span>
                        </a>
                    </li>
                    @endif

                    @if(Auth::user()->hasPermission('view_departments'))
                    <li><a class="" href="{{route('admin.departments.index')}}" aria-expanded="false">
                            <i class="fas fa-building"></i>
                            <span class="nav-text">Departmanlar</span>
                        </a>
                    </li>
                    @endif

                    @if(Auth::user()->hasPermission('manage_roles'))
                    <li><a class="" href="{{route('admin.roles.index')}}" aria-expanded="false">
                            <i class="fas fa-user-tag"></i>
                            <span class="nav-text">Rol Yönetimi</span>
                        </a>
                    </li>
                    @endif

                    @if(Auth::user()->is_admin)
                    <li><a class="" href="{{route('admin.polls.index')}}" aria-expanded="false">
                            <i class="fas fa-poll-h"></i>
                            <span class="nav-text">Anket Yönetimi</span>
                        </a>
                    </li>
                    @endif

                    @if(Auth::user()->hasPermission('view_meetings'))
                    <li><a class="" href="{{route('meetings.index')}}" aria-expanded="false">
                            <i class="fas fa-video"></i>
                            <span class="nav-text">Toplantılar</span>
                        </a>
                    </li>
                    @endif

                    @if(Auth::user()->hasPermission('create_meetings'))
                    <li><a class="" href="{{route('admin.meetings.index')}}" aria-expanded="false">
                            <i class="fas fa-video"></i>
                            <span class="nav-text">Toplantı Yönetimi</span>
                        </a>
                    </li>
                    @endif

                    @if(Auth::user()->hasPermission('manage_platform_settings'))
                    <li><a class="" href="{{route('admin.settings.index')}}" aria-expanded="false">
                            <i class="fas fa-cogs"></i>
                            <span class="nav-text">Platform Ayarları</span>
                        </a>
                    </li>
                    @endif
	                </ul>



			</div>
        </div>
        <!--**********************************
            Sidebar end
        ***********************************-->
