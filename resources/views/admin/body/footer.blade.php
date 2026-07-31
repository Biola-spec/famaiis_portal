  <footer class="main-footer">
    <div class="pull-right d-none d-sm-inline-block">
        <ul class="nav nav-primary nav-dotted nav-dot-separated justify-content-center justify-content-md-end">
		  <li class="nav-item">
			<a class="nav-link" href="javascript:void(0)">FAQ</a>
		  </li>
		  <li class="nav-item">
			<a class="nav-link" href="#">{{ $setting->school_name }}</a>
		  </li>
		</ul>
    </div>
	  @if($setting->copyright)
		  {!! $setting->copyright !!}
	  @else
	  	  &copy; {{ date('Y') }} <a href="#">{{ $setting->school_name }}</a>. {{ __('ui.all_rights_reserved') }}
	  @endif
  </footer>
