
  <header class="main-header">
    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top pl-30">
      <!-- Sidebar toggle button-->
	  <div>
		  <ul class="nav">
			<li class="btn-group nav-item">
				<a href="#" class="waves-effect waves-light nav-link rounded svg-bt-icon" data-toggle="push-menu" role="button">
					<i class="nav-link-icon mdi mdi-menu"></i>
			    </a>
			</li>
			<li class="btn-group nav-item d-none d-sm-inline-block">
				<a href="#" data-provide="fullscreen" class="waves-effect waves-light nav-link rounded svg-bt-icon" title="Full Screen">
					<i class="nav-link-icon mdi mdi-crop-free"></i>
			    </a>
			</li>

			<li class="btn-group nav-item">
				<a href="javascript:void(0)" id="theme-toggle" class="waves-effect waves-light nav-link rounded svg-bt-icon" title="Toggle theme">
					<i id="theme-icon" class="nav-link-icon mdi mdi-weather-night"></i>
			    </a>
			</li>			
			<li class="btn-group nav-item d-none d-xl-inline-block">
				<a href="#" class="waves-effect waves-light nav-link rounded svg-bt-icon" title="">
					<i class="ti-check-box"></i>
			    </a>
			</li>
			<li class="btn-group nav-item d-none d-xl-inline-block">
				<a href="calendar.html" class="waves-effect waves-light nav-link rounded svg-bt-icon" title="">
					<i class="ti-calendar"></i>
			    </a>
			</li>
		  </ul>
	  </div>
		
      <div class="navbar-custom-menu r-side">
        <ul class="nav navbar-nav">

		  {{-- Language Switcher --}}
		  @php
			  $langLabels = ['en' => 'English', 'fr' => 'Français', 'ar' => 'العربية', 'es' => 'Español', 'sw' => 'Kiswahili', 'ha' => 'Hausa', 'yo' => 'Yorùbá', 'ig' => 'Igbo'];
			  $flagMap = ['en' => 'us', 'fr' => 'fr', 'ar' => 'sa', 'es' => 'es', 'sw' => 'ke', 'ha' => 'ng', 'yo' => 'ng', 'ig' => 'ng'];
			  $currentLang = auth()->user()->language ?? 'en';
		  @endphp
		  <li class="dropdown notifications-menu">
			<a href="#" class="waves-effect waves-light rounded dropdown-toggle d-flex align-items-center" data-toggle="dropdown" title="{{ __('ui.language') }}" style="gap: 5px; height: 100%;">
			  <span class="flag-icon flag-icon-{{ $flagMap[$currentLang] ?? 'us' }}" style="border-radius: 2px; font-size: 14px;"></span>
			  <span class="d-none d-md-inline-block" style="font-size: 11px; font-weight: 700; margin-left: 2px;">{{ strtoupper($currentLang) }}</span>
			</a>
			<ul class="dropdown-menu animated bounceIn" style="min-width: 160px;">
			  <li class="header" style="padding: 12px 16px;">
				<h5 class="mb-0" style="font-size: 14px; font-weight: 700;">{{ __('ui.language') }}</h5>
			  </li>
			  <li>
				<ul class="menu sm-scrol">
				  @foreach($langLabels as $code => $label)
				  <li>
					<form action="{{ route('language.switch') }}" method="POST" class="mb-0">
					  @csrf
					  <input type="hidden" name="language" value="{{ $code }}">
					  <button type="submit" style="width:100%;text-align:left;padding:10px 16px;border:none;background:{{ $currentLang === $code ? '#f3e5f5' : 'transparent' }};cursor:pointer;font-size:13px;font-weight:{{ $currentLang === $code ? '700' : '400' }};color:{{ $currentLang === $code ? '#764ba2' : '#555' }};display:flex;align-items:center;gap:8px;">
						@if($currentLang === $code)<i class="ti-check" style="color:#764ba2; font-size:10px; width:12px;"></i>@else<span style="width:12px;display:inline-block;"></span>@endif
						<span class="flag-icon flag-icon-{{ $flagMap[$code] ?? 'us' }}" style="border-radius: 1px; font-size: 14px;"></span>
						<span>{{ $label }}</span>
					  </button>
					</form>
				  </li>
				  @endforeach
				</ul>
			  </li>
			</ul>
		  </li>
		  <li class="btn-group nav-item d-none d-xl-inline-block">
				<form action="{{ route('academic.section.switch') }}" method="POST" class="nav-link p-0">
                    @csrf
                    <select name="section_id" onchange="this.form.submit()" class="form-control form-control-sm mt-1 section-switcher-select">
                        <option value="all" {{ !isset($activeSectionId) ? 'selected' : '' }}>All Sections</option>
                        @foreach($headerSections as $s)
                            <option value="{{ $s->id }}" {{ (isset($activeSectionId) && $activeSectionId == $s->id) ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
                <style>
                    .section-switcher-select {
                        background: rgba(0,0,0,0.05) !important;
                        border: 1px solid rgba(0,0,0,0.1) !important;
                        color: inherit !important;
                        width: 160px;
                        font-weight: 600;
                        border-radius: 20px;
                        padding-left: 10px;
                    }
                    body.dark-skin .section-switcher-select {
                        background: rgba(255,255,255,0.1) !important;
                        border: 1px solid rgba(255,255,255,0.2) !important;
                    }
                </style>
			</li>

	      <li class="search-bar d-none d-md-inline-block">		  
			  <div class="lookup lookup-circle lookup-right">
			     <input type="text" name="s">
			  </div>
		  </li>			
          
          <!-- Chat -->
          <li class="dropdown notifications-menu">
			<a href="{{ route('chat.view') }}" class="waves-effect waves-light rounded" title="Messages">
			  <i class="ti-comment-alt"></i>
              @if(($unreadMessageCount ?? 0) > 0)
			    <span class="badge badge-pill badge-info" style="position: absolute; top: 10px; right: 5px; font-size: 10px;">{{ $unreadMessageCount }}</span>
              @endif
			</a>
          </li>
		  <!-- Notifications -->
		  <li class="dropdown notifications-menu">
			<a href="#" class="waves-effect waves-light rounded dropdown-toggle" data-toggle="dropdown" title="Notifications">
			  <i class="ti-bell"></i>
              @if(($unreadNotificationCount ?? 0) > 0)
			    <span class="badge badge-pill badge-danger" style="position: absolute; top: 10px; right: 5px; font-size: 10px;">{{ $unreadNotificationCount }}</span>
              @endif
			</a>
			<ul class="dropdown-menu animated bounceIn">

			  <li class="header">
				<div class="p-20">
					<div class="flexbox">
						<div>
							<h4 class="mb-0 mt-0">{{ __('ui.notifications') }}</h4>
						</div>
						<div>
							<form action="{{ route('notifications.clear') }}" method="POST" class="mb-0">
                                @csrf
								<button type="submit" class="btn btn-link text-danger p-0 border-0">{{ __('ui.clear_all') }}</button>
							</form>
						</div>
					</div>
				</div>
			  </li>

			  <li>
				<!-- inner menu: contains the actual data -->
				<ul class="menu sm-scrol">
                  @forelse(($unreadNotifications ?? collect()) as $notification)
				  <li>
					<a href="{{ $notification->data['type'] == 'report' ? route('parent.report.index') : '#' }}">
					  <i class="fa {{ $notification->data['type'] == 'report' ? 'fa-book text-info' : 'fa-bell text-warning' }}"></i> 
                      {{ $notification->data['title'] }}
                      <small class="pull-right">{{ \Carbon\Carbon::parse($notification->data['timestamp'] ?? now())->diffForHumans() }}</small>
					</a>
				  </li>
                  @empty
                  <li class="text-center p-20 text-muted">{{ __('ui.no_notifications') }}</li>
                  @endforelse
				</ul>
			  </li>
			  <li class="footer">
				  <a href="#">{{ __('ui.view_all') }}</a>
			  </li>
			</ul>
		  </li>	
		  
@php
 $userObj = Auth::user();
 $isAdminHeaderUser = $userObj->hasRole('Admin', 'Super Admin');
 $user = $userObj;
@endphp		  
	      <!-- User Account-->
          <li class="dropdown user user-menu">	
			<a href="#" class="waves-effect waves-light rounded dropdown-toggle p-0" data-toggle="dropdown" title="User">
                @php
                    $userType = strtolower($user->usertype);
                    if ($userType == 'student') {
                        $folder = 'student_images';
                    } elseif ($userType == 'employee') {
                        $folder = 'employee_images';
                    } elseif ($userType == 'parent') {
                        $folder = 'parent_images';
                    } else {
                        $folder = 'user_images';
                    }
                    $imagePath = (!empty($user->image)) ? url('upload/'.$folder.'/'.$user->image) : url('upload/no_image.jpg');
                @endphp
				<img src="{{ $imagePath }}" alt="User Image" style="object-fit: cover;">
			</a>
			<ul class="dropdown-menu animated flipInX">
			  <li class="user-body">
	 <a class="dropdown-item" href="{{ route('profile.view') }}"><i class="ti-user text-muted mr-2"></i> {{ __('ui.profile') }}</a>
				 <a class="dropdown-item" href="#"><i class="ti-wallet text-muted mr-2"></i> {{ __('ui.my_wallet') }}</a>
                 @if($isAdminHeaderUser)
				 <a class="dropdown-item" href="{{ route('site.setting') }}"><i class="ti-settings text-muted mr-2"></i> {{ __('ui.settings') }}</a>
                 @endif
				 <div class="dropdown-divider"></div>
				 <a class="dropdown-item" href="{{ route('admin.logout') }}"><i class="ti-lock text-muted mr-2"></i> {{ __('ui.logout') }}</a>
			  </li>
			</ul>
          </li>	
          @if($isAdminHeaderUser)
		  <li>
              <a href="{{ route('site.setting') }}" title="Setting" class="waves-effect waves-light">
			  	<i class="ti-settings"></i>
			  </a>
          </li>
          @endif
			
        </ul>
      </div>
    </nav>
  </header>
